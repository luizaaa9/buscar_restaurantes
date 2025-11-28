<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
        'cuisine_types',
        'photos',
        'average_rating',
        'total_reviews'
    ];

    protected $casts = [
        'cuisine_types' => 'array',
        'photos' => 'array',
        'average_rating' => 'decimal:1',
        'total_reviews' => 'integer'
    ];

    public function getCuisineTypesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setCuisineTypesAttribute($value)
    {
        $this->attributes['cuisine_types'] = json_encode($value);
    }

    public function getPhotosAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setPhotosAttribute($value)
    {
        $this->attributes['photos'] = json_encode($value);
    }

    public function getReviewsFromFirebase()
    {
        try {
            $firestore = app('firebase.firestore')->database();
            $reviews = $firestore->collection('reviews')
                ->where('restaurant_id', '==', (string)$this->id)
                ->orderBy('created_at', 'DESC')
                ->documents();

            $reviewsData = [];
            foreach ($reviews as $review) {
                if ($review->exists()) {
                    $reviewsData[] = $review->data();
                }
            }

            return $reviewsData;
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar reviews do Firebase: ' . $e->getMessage());
            return [];
        }
    }

    public function calculateAverageRating()
    {
        try {
            $reviews = $this->getReviewsFromFirebase();
            
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
            \Log::error('Erro ao calcular rating: ' . $e->getMessage());
            return [
                'average_rating' => 0,
                'total_reviews' => 0
            ];
        }
    }
    public function updateRatingStats()
    {
        $stats = $this->calculateAverageRating();
        
        $this->update([
            'average_rating' => $stats['average_rating'],
            'total_reviews' => $stats['total_reviews']
        ]);

        return $stats;
    }
}