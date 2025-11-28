<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $projectId;
    protected $apiKey;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', env('VITE_FIREBASE_PROJECT_ID'));
        $this->apiKey = env('VITE_FIREBASE_API_KEY');
    }

    /**
     * Salvar review no Firebase via REST API
     */
    public function saveReview($reviewData)
    {
        try {
            $response = Http::post(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/reviews",
                [
                    'fields' => $this->formatDataForFirestore($reviewData)
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                return basename($data['name']); // Retorna o ID do documento
            } else {
                Log::error('Erro ao salvar review no Firebase: ' . $response->body());
                throw new \Exception('Erro ao salvar review: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Erro ao salvar review no Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Buscar reviews por restaurante
     */
    public function getReviewsByRestaurant($restaurantId)
    {
        try {
            // Para simplificar, vamos buscar todos e filtrar
            $response = Http::get(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/reviews"
            );

            if ($response->successful()) {
                $data = $response->json();
                $reviews = $this->parseFirestoreResponse($data);
                
                // Filtrar por restaurant_id
                return array_filter($reviews, function($review) use ($restaurantId) {
                    return isset($review['restaurant_id']) && $review['restaurant_id'] == $restaurantId;
                });
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Erro ao buscar reviews do Firebase: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Deletar review
     */
    public function deleteReview($reviewId)
    {
        try {
            $response = Http::delete(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/reviews/{$reviewId}"
            );

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Erro ao deletar review do Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calcular estatísticas de reviews
     */
    public function calculateReviewStats($restaurantId)
    {
        try {
            $reviews = $this->getReviewsByRestaurant($restaurantId);
            
            if (empty($reviews)) {
                return [
                    'average_rating' => 0,
                    'total_reviews' => 0
                ];
            }

            $totalRating = 0;
            foreach ($reviews as $review) {
                $totalRating += $review['rating'] ?? 0;
            }

            $averageRating = round($totalRating / count($reviews), 1);

            return [
                'average_rating' => $averageRating,
                'total_reviews' => count($reviews)
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao calcular estatísticas: ' . $e->getMessage());
            return [
                'average_rating' => 0,
                'total_reviews' => 0
            ];
        }
    }

    /**
     * Sincronizar restaurante com Firebase
     */
    public function syncRestaurant($restaurant)
    {
        try {
            $response = Http::patch(
                "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/restaurants/{$restaurant->id}",
                [
                    'fields' => $this->formatDataForFirestore([
                        'name' => $restaurant->name,
                        'description' => $restaurant->description,
                        'address' => $restaurant->address,
                        'latitude' => $restaurant->latitude,
                        'longitude' => $restaurant->longitude,
                        'cuisine_types' => $restaurant->cuisine_types,
                        'photos' => $restaurant->photos,
                        'average_rating' => $restaurant->average_rating,
                        'total_reviews' => $restaurant->total_reviews,
                        'created_at' => now()->toISOString(),
                        'updated_at' => now()->toISOString()
                    ])
                ]
            );

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar restaurante com Firebase: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Formatar dados para o Firestore
     */
    private function formatDataForFirestore($data)
    {
        $fields = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $fields[$key] = ['stringValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_array($value)) {
                $fields[$key] = ['arrayValue' => ['values' => $this->formatArrayForFirestore($value)]];
            } elseif ($value instanceof \DateTime) {
                $fields[$key] = ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
            }
        }
        
        return $fields;
    }

    /**
     * Formatar array para Firestore
     */
    private function formatArrayForFirestore($array)
    {
        $values = [];
        foreach ($array as $value) {
            if (is_string($value)) {
                $values[] = ['stringValue' => $value];
            } elseif (is_int($value)) {
                $values[] = ['integerValue' => $value];
            } elseif (is_array($value)) {
                $values[] = ['mapValue' => ['fields' => $this->formatDataForFirestore($value)]];
            }
        }
        return $values;
    }

    /**
     * Parse response do Firestore
     */
    private function parseFirestoreResponse($data)
    {
        $results = [];
        
        if (!isset($data['documents'])) {
            return $results;
        }
        
        foreach ($data['documents'] as $document) {
            $id = basename($document['name']);
            $fields = $document['fields'];
            
            $result = ['id' => $id];
            foreach ($fields as $key => $field) {
                $result[$key] = $this->parseFirestoreValue($field);
            }
            
            $results[$id] = $result;
        }
        
        return $results;
    }

    /**
     * Parse valores do Firestore
     */
    private function parseFirestoreValue($field)
    {
        if (isset($field['stringValue'])) {
            return $field['stringValue'];
        } elseif (isset($field['integerValue'])) {
            return (int)$field['integerValue'];
        } elseif (isset($field['doubleValue'])) {
            return (float)$field['doubleValue'];
        } elseif (isset($field['booleanValue'])) {
            return (bool)$field['booleanValue'];
        } elseif (isset($field['timestampValue'])) {
            return $field['timestampValue'];
        } elseif (isset($field['arrayValue']['values'])) {
            $array = [];
            foreach ($field['arrayValue']['values'] as $value) {
                $array[] = $this->parseFirestoreValue($value);
            }
            return $array;
        }
        
        return null;
    }
}