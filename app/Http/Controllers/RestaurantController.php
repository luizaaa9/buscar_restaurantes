<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RestaurantController extends Controller
{
    public function map(Request $request)
    {
        $userLat = $request->get('lat', -23.5505);
        $userLng = $request->get('lng', -46.6333);
        
        $restaurants = Restaurant::all();
        
        $restaurantsData = $restaurants->map(function($restaurant) use ($userLat, $userLng) {
            $distance = $this->calculateDistance(
                $userLat, $userLng,
                (float) $restaurant->latitude, (float) $restaurant->longitude
            );
            
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'description' => $restaurant->description,
                'address' => $restaurant->address,
                'latitude' => (float) $restaurant->latitude,
                'longitude' => (float) $restaurant->longitude,
                'cuisine_types' => is_array($restaurant->cuisine_types) 
                    ? $restaurant->cuisine_types 
                    : json_decode($restaurant->cuisine_types, true) ?? [],
                'photos' => is_array($restaurant->photos) 
                    ? $restaurant->photos 
                    : json_decode($restaurant->photos, true) ?? [],
                'average_rating' => (float) $restaurant->average_rating,
                'total_reviews' => (int) $restaurant->total_reviews,
                'distance' => $distance, 
                'distance_display' => $this->formatDistance($distance)
            ];
        });
        
        $restaurantsData = $restaurantsData->sortBy('distance')->values();
        
        return view('restaurants.map', [
            'restaurants' => $restaurantsData,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'initialLat' => $userLat,
            'initialLng' => $userLng,
            'initialZoom' => 13
        ]);
    }
    
    /**
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Formatar distância para exibição
     */
    private function formatDistance($distance)
    {
        if ($distance < 1) {
            return round($distance * 1000) . ' m';
        }
        return round($distance, 1) . ' km';
    }
    
    /**
     * API para buscar localização do usuário e atualizar mapa
     */
    public function updateUserLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
        
        return redirect()->route('restaurants.map', [
            'lat' => $request->latitude,
            'lng' => $request->longitude
        ]);
    }
    
    public function nearbyRestaurants(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'sometimes|numeric|min:0.1|max:50'
        ]);
        
        $userLat = $request->latitude;
        $userLng = $request->longitude;
        $radius = $request->radius ?? 10;
        
        $restaurants = Restaurant::selectRaw(
            "*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + 
            sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$userLat, $userLng, $userLat]
        )
        ->having('distance', '<', $radius)
        ->orderBy('distance')
        ->orderBy('average_rating', 'desc')
        ->limit(50)
        ->get()
        ->map(function($restaurant) {
            $data = $restaurant->toArray();
            $data['distance'] = round($restaurant->distance, 2);
            $data['distance_display'] = $restaurant->distance < 1 
                ? round($restaurant->distance * 1000) . ' m' 
                : round($restaurant->distance, 1) . ' km';
            return $data;
        });
        
        return response()->json([
            'success' => true,
            'data' => $restaurants,
            'count' => $restaurants->count(),
            'user_location' => [
                'latitude' => $userLat,
                'longitude' => $userLng,
                'radius_km' => $radius
            ]
        ]);
    }
    

    public function index()
    {
        $restaurants = Restaurant::with('reviews')
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->get();

        return view('restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        $cuisineTypes = [
            'Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa',
            'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar',
            'Café', 'Sobremesas', 'Fast Food', 'Pizza', 'Churrascaria',
            'Portuguesa', 'Argentina', 'Peruana', 'Coreana', 'Tailandesa'
        ];

        return view('restaurants.create', compact('cuisineTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'cuisine_types' => 'required|array|min:1',
            'cuisine_types.*' => 'string|max:50',
            'photos' => 'sometimes|array|max:10',
            'photos.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        try {
            $uploadedPhotos = [];
            
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('restaurants/photos', 'public');
                    $uploadedPhotos[] = [
                        'url' => Storage::url($path),
                        'path' => $path,
                        'filename' => $photo->getClientOriginalName()
                    ];
                }
            } else {
                $uploadedPhotos[] = [
                    'url' => asset('images/restaurant-placeholder.jpg'),
                    'path' => 'default',
                    'filename' => 'placeholder.jpg'
                ];
            }

            $restaurant = Restaurant::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'cuisine_types' => $request->cuisine_types,
                'photos' => $uploadedPhotos,
                'average_rating' => 0,
                'total_reviews' => 0
            ]);

            return redirect()->route('restaurants.map')
                ->with('success', 'Restaurante cadastrado com sucesso!' . 
                    (empty($request->photos) ? ' Você pode adicionar fotos depois.' : ''));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar restaurante: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function show($id)
{
    $restaurant = Restaurant::with(['reviews' => function($query) {
        $query->orderBy('created_at', 'desc');
    }])->findOrFail($id);
    
    return view('restaurants.show', compact('restaurant'));
}

    
    public function search(Request $request)
    {
        $query = $request->get('query');
        $cuisine = $request->get('cuisine');

        $restaurants = Restaurant::with('reviews')
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
            })
            ->when($cuisine, function ($q) use ($cuisine) {
                return $q->whereJsonContains('cuisine_types', $cuisine);
            })
            ->orderBy('average_rating', 'desc')
            ->get();

        return view('restaurants.index', compact('restaurants'));
    }
    
    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $cuisineTypes = [
            'Brasileira', 'Italiana', 'Japonesa', 'Mexicana', 'Chinesa',
            'Árabe', 'Francesa', 'Vegetariana', 'Vegana', 'Frutos do Mar',
            'Café', 'Sobremesas', 'Fast Food', 'Pizza', 'Churrascaria',
            'Portuguesa', 'Argentina', 'Peruana', 'Coreana', 'Tailandesa'
        ];
        
        return view('restaurants.edit', compact('restaurant', 'cuisineTypes'));
    }
    
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'cuisine_types' => 'required|array|min:1',
            'cuisine_types.*' => 'string|max:50',
            'photos' => 'sometimes|array|max:10',
            'photos.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);
        
        try {
            $restaurant = Restaurant::findOrFail($id);
            
            $uploadedPhotos = $restaurant->photos ?? [];
            
            if ($request->hasFile('photos')) {
                
                foreach ($restaurant->photos as $photo) {
                    if ($photo['path'] !== 'default') {
                        Storage::disk('public')->delete($photo['path']);
                    }
                }
                
                
                $uploadedPhotos = [];
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('restaurants/photos', 'public');
                    $uploadedPhotos[] = [
                        'url' => Storage::url($path),
                        'path' => $path,
                        'filename' => $photo->getClientOriginalName()
                    ];
                }
            }
            
            $restaurant->update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'cuisine_types' => $request->cuisine_types,
                'photos' => $uploadedPhotos
            ]);
            
            return redirect()->route('restaurants.show', $restaurant->id)
                ->with('success', 'Restaurante atualizado com sucesso!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar restaurante: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    public function destroy($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);
            
            foreach ($restaurant->photos as $photo) {
                if ($photo['path'] !== 'default') {
                    Storage::disk('public')->delete($photo['path']);
                }
            }
            
            $restaurant->reviews()->delete();
            
            $restaurant->delete();
            
            return redirect()->route('restaurants.index')
                ->with('success', 'Restaurante deletado com sucesso!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar restaurante: ' . $e->getMessage());
        }
    }
    
    
    public function apiRestaurants(Request $request)
    {
        try {
            $query = Restaurant::with('reviews');
            
            
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('cuisine')) {
                $query->whereJsonContains('cuisine_types', $request->cuisine);
            }
            
            if ($request->has('min_rating')) {
                $query->where('average_rating', '>=', $request->min_rating);
            }
            
            if ($request->has('limit')) {
                $query->limit($request->limit);
            }
            
            $restaurants = $query->orderBy('average_rating', 'desc')
                ->orderBy('total_reviews', 'desc')
                ->get()
                ->map(function($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                        'description' => $restaurant->description,
                        'address' => $restaurant->address,
                        'latitude' => (float) $restaurant->latitude,
                        'longitude' => (float) $restaurant->longitude,
                        'cuisine_types' => $restaurant->cuisine_types ?? [],
                        'photos' => $restaurant->photos ?? [],
                        'average_rating' => (float) $restaurant->average_rating,
                        'total_reviews' => (int) $restaurant->total_reviews,
                        'reviews_count' => $restaurant->reviews->count(),
                        'reviews_preview' => $restaurant->reviews->take(3)->map(function($review) {
                            return [
                                'user_name' => $review->user_name,
                                'rating' => $review->rating,
                                'comment' => substr($review->comment, 0, 100) . '...',
                                'created_at' => $review->created_at->format('d/m/Y H:i')
                            ];
                        })
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count(),
                'filters' => $request->all()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar restaurantes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function apiStoreRestaurant(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'cuisine_types' => 'required|array|min:1',
                'cuisine_types.*' => 'string|max:50',
                'photos' => 'sometimes|array',
                'photos.*' => 'sometimes|string' 
            ]);
            
            $uploadedPhotos = [];
            
            if ($request->has('photos') && is_array($request->photos)) {
                foreach ($request->photos as $index => $photo) {
                    if (str_starts_with($photo, 'data:image')) {
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
                        $filename = 'restaurant_' . time() . '_' . $index . '.jpg';
                        $path = 'restaurants/photos/' . $filename;
                        
                        Storage::disk('public')->put($path, $imageData);
                        
                        $uploadedPhotos[] = [
                            'url' => Storage::url($path),
                            'path' => $path,
                            'filename' => $filename
                        ];
                    } else {
                        $uploadedPhotos[] = [
                            'url' => $photo,
                            'path' => 'external',
                            'filename' => 'external_' . $index
                        ];
                    }
                }
            } else {
                $uploadedPhotos[] = [
                    'url' => asset('images/restaurant-placeholder.jpg'),
                    'path' => 'default',
                    'filename' => 'placeholder.jpg'
                ];
            }
            
            $restaurant = Restaurant::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'cuisine_types' => $request->cuisine_types,
                'photos' => $uploadedPhotos,
                'average_rating' => 0,
                'total_reviews' => 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante cadastrado com sucesso!',
                'data' => $restaurant->load('reviews')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar restaurante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function apiShowRestaurant($id)
    {
        try {
            $restaurant = Restaurant::with(['reviews' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])->findOrFail($id);
            
            $data = [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'description' => $restaurant->description,
                'address' => $restaurant->address,
                'latitude' => (float) $restaurant->latitude,
                'longitude' => (float) $restaurant->longitude,
                'cuisine_types' => $restaurant->cuisine_types ?? [],
                'photos' => $restaurant->photos ?? [],
                'average_rating' => (float) $restaurant->average_rating,
                'total_reviews' => (int) $restaurant->total_reviews,
                'created_at' => $restaurant->created_at->format('d/m/Y H:i'),
                'updated_at' => $restaurant->updated_at->format('d/m/Y H:i'),
                'reviews' => $restaurant->reviews->map(function($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user_name,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at->format('d/m/Y H:i'),
                        'created_at_timestamp' => $review->created_at->timestamp
                    ];
                })
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurante não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function apiUpdateRestaurant(Request $request, $id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'address' => 'sometimes|string',
                'latitude' => 'sometimes|numeric',
                'longitude' => 'sometimes|numeric',
                'cuisine_types' => 'sometimes|array|min:1',
                'cuisine_types.*' => 'string|max:50',
                'photos' => 'sometimes|array'
            ]);
            
            if ($request->has('photos')) {
                foreach ($restaurant->photos as $photo) {
                    if ($photo['path'] !== 'default' && $photo['path'] !== 'external') {
                        Storage::disk('public')->delete($photo['path']);
                    }
                }
                
                $uploadedPhotos = [];
                foreach ($request->photos as $index => $photo) {
                    if (str_starts_with($photo, 'data:image')) {
                        
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
                        $filename = 'restaurant_' . time() . '_' . $index . '.jpg';
                        $path = 'restaurants/photos/' . $filename;
                        
                        Storage::disk('public')->put($path, $imageData);
                        
                        $uploadedPhotos[] = [
                            'url' => Storage::url($path),
                            'path' => $path,
                            'filename' => $filename
                        ];
                    } else {
                        
                        $uploadedPhotos[] = [
                            'url' => $photo,
                            'path' => 'external',
                            'filename' => 'external_' . $index
                        ];
                    }
                }
                
                $request->merge(['photos' => $uploadedPhotos]);
            }
            
            $restaurant->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante atualizado com sucesso!',
                'data' => $restaurant->load('reviews')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar restaurante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function apiDeleteRestaurant($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);
            
            foreach ($restaurant->photos as $photo) {
                if ($photo['path'] !== 'default' && $photo['path'] !== 'external') {
                    Storage::disk('public')->delete($photo['path']);
                }
            }
            
            
            $restaurant->reviews()->delete();
            
            
            $restaurant->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante deletado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar restaurante',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function apiUploadPhoto(Request $request, $id)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);
            
            $restaurant = Restaurant::findOrFail($id);
            
            $photo = $request->file('photo');
            $path = $photo->store('restaurants/photos', 'public');
            
            $newPhoto = [
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $photo->getClientOriginalName()
            ];
            
            $photos = $restaurant->photos ?? [];
            $photos[] = $newPhoto;
            
            $restaurant->update(['photos' => $photos]);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto adicionada com sucesso!',
                'data' => $newPhoto
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload da foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function apiDeletePhoto(Request $request, $id)
    {
        try {
            $request->validate([
                'photo_index' => 'required|integer|min:0'
            ]);
            
            $restaurant = Restaurant::findOrFail($id);
            $photos = $restaurant->photos ?? [];
            
            if (!isset($photos[$request->photo_index])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto não encontrada'
                ], 404);
            }
            
            $photoToDelete = $photos[$request->photo_index];
            
            
            if ($photoToDelete['path'] !== 'default' && $photoToDelete['path'] !== 'external') {
                Storage::disk('public')->delete($photoToDelete['path']);
            }
            
            
            array_splice($photos, $request->photo_index, 1);
            
            
            if (empty($photos)) {
                $photos[] = [
                    'url' => asset('images/restaurant-placeholder.jpg'),
                    'path' => 'default',
                    'filename' => 'placeholder.jpg'
                ];
            }
            
            $restaurant->update(['photos' => $photos]);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto deletada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function apiNearbyRestaurants(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'sometimes|numeric|min:0.1|max:50', 
                'limit' => 'sometimes|integer|min:1|max:100'
            ]);
            
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius ?? 10; 
            $limit = $request->limit ?? 20;
            
            
            $restaurants = Restaurant::selectRaw(
                "*, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->with('reviews')
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($restaurant) {
                $data = $restaurant->toArray();
                $data['distance'] = round($restaurant->distance, 2);
                return $data;
            });
            
            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count(),
                'location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius_km' => $radius
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar restaurantes próximos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}