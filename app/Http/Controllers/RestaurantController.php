<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class RestaurantController extends Controller
{
    public function map()
    {
        $restaurants = Restaurant::all();
        
       
        $restaurantsData = $restaurants->map(function($restaurant) {
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
            ];
        });

        return view('restaurants.map', compact('restaurantsData'));
    }

    
    public function index()
    {
        $restaurants = Restaurant::orderBy('average_rating', 'desc')
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
            'photos' => 'sometimes|array|max:10', // Alterado para 'sometimes'
            'photos.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Alterado para 'sometimes'
        ]);

        try {
            $uploadedPhotos = [];
            
            // FOTOS SÃO OPCIONAIS - só processa se existirem
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $uploadResult = Cloudinary::upload($photo->getRealPath(), [
                        'folder' => 'restaurant_reviews/restaurants'
                    ]);
                    $uploadedPhotos[] = [
                        'url' => $uploadResult->getSecurePath(),
                        'public_id' => $uploadResult->getPublicId()
                    ];
                }
            } else {
                // Se não há fotos, usa um placeholder
                $uploadedPhotos[] = [
                    'url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
                    'public_id' => 'restaurant_placeholder'
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

            // Sincronizar com Firebase
            $restaurant->syncWithFirebase();

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
        $restaurant = Restaurant::findOrFail($id);
        
        $reviewsData = $restaurant->getReviewsFromFirebase();

        return view('restaurants.show', compact('restaurant', 'reviewsData'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $cuisine = $request->get('cuisine');

        $restaurants = Restaurant::when($query, function ($q) use ($query) {
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

    
    public function apiRestaurants(Request $request)
    {
        $restaurants = Restaurant::all()->map(function($restaurant) {
            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'description' => $restaurant->description,
                'address' => $restaurant->address,
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
                'cuisine_types' => is_array($restaurant->cuisine_types) 
                    ? $restaurant->cuisine_types 
                    : json_decode($restaurant->cuisine_types, true) ?? [],
                'photos' => is_array($restaurant->photos) 
                    ? $restaurant->photos 
                    : json_decode($restaurant->photos, true) ?? [],
                'average_rating' => $restaurant->average_rating,
                'total_reviews' => $restaurant->total_reviews,
            ];
        });
        
        return response()->json($restaurants);
    }
}