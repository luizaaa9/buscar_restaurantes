<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Armazenar nova avaliação (via web)
     */
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);

        try {
            $review = Review::create([
                'restaurant_id' => $restaurant->id,
                'user_name' => $request->user_name,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            $restaurant->updateRatingStats();

            return redirect()->route('restaurants.show', $restaurant->id)
                ->with('success', 'Avaliação enviada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar avaliação: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Deletar avaliação (via web - admin)
     */
    public function destroy($reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);
            $restaurantId = $review->restaurant_id;
            
            $review->delete();
            
            $restaurant = Restaurant::find($restaurantId);
            if ($restaurant) {
                $restaurant->updateRatingStats();
            }
            
            return redirect()->back()
                ->with('success', 'Avaliação deletada com sucesso!');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar avaliação: ' . $e->getMessage());
        }
    }
    
    /**
     * API: Criar nova avaliação
     */
    public function apiStoreReview(Request $request, $restaurantId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|string|max:255',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|min:10|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $restaurant = Restaurant::findOrFail($restaurantId);
            
            $review = Review::create([
                'restaurant_id' => $restaurant->id,
                'user_name' => $request->user_name,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            
            $restaurant->updateRatingStats();
            
            return response()->json([
                'success' => true,
                'message' => 'Avaliação enviada com sucesso!',
                'data' => [
                    'id' => $review->id,
                    'user_name' => $review->user_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                    'created_at_timestamp' => $review->created_at->timestamp,
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                        'new_average_rating' => $restaurant->average_rating,
                        'new_total_reviews' => $restaurant->total_reviews
                    ]
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar avaliação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function apiGetRestaurantReviews(Request $request, $restaurantId)
    {
        try {
            $restaurant = Restaurant::findOrFail($restaurantId);
            
            $query = $restaurant->reviews();
            
            if ($request->has('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
            }
            
            if ($request->has('sort')) {
                if ($request->sort === 'newest') {
                    $query->orderBy('created_at', 'desc');
                } elseif ($request->sort === 'oldest') {
                    $query->orderBy('created_at', 'asc');
                } elseif ($request->sort === 'highest_rating') {
                    $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                } elseif ($request->sort === 'lowest_rating') {
                    $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $perPage = $request->per_page ?? 10;
            $reviews = $query->paginate($perPage);
            
            $reviewData = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                    'created_at_timestamp' => $review->created_at->timestamp,
                    'time_ago' => $review->created_at->diffForHumans()
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $reviewData,
                'pagination' => [
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'has_more_pages' => $reviews->hasMorePages()
                ],
                'restaurant_stats' => [
                    'average_rating' => (float) $restaurant->average_rating,
                    'total_reviews' => (int) $restaurant->total_reviews,
                    'rating_distribution' => [
                        '5_stars' => $restaurant->reviews()->where('rating', 5)->count(),
                        '4_stars' => $restaurant->reviews()->where('rating', 4)->count(),
                        '3_stars' => $restaurant->reviews()->where('rating', 3)->count(),
                        '2_stars' => $restaurant->reviews()->where('rating', 2)->count(),
                        '1_star' => $restaurant->reviews()->where('rating', 1)->count()
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar avaliações',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Deletar avaliação
     */
    public function apiDeleteReview($reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);
            $restaurantId = $review->restaurant_id;
            
            $review->delete();
            
            $restaurant = Restaurant::find($restaurantId);
            if ($restaurant) {
                $restaurant->updateRatingStats();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Avaliação deletada com sucesso!',
                'data' => [
                    'restaurant' => [
                        'id' => $restaurant->id ?? null,
                        'new_average_rating' => $restaurant->average_rating ?? null,
                        'new_total_reviews' => $restaurant->total_reviews ?? null
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar avaliação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Mostrar detalhes de uma avaliação
     */
    public function apiShowReview($reviewId)
    {
        try {
            $review = Review::with('restaurant')->findOrFail($reviewId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $review->id,
                    'user_name' => $review->user_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                    'created_at_timestamp' => $review->created_at->timestamp,
                    'time_ago' => $review->created_at->diffForHumans(),
                    'restaurant' => $review->restaurant ? [
                        'id' => $review->restaurant->id,
                        'name' => $review->restaurant->name,
                        'average_rating' => $review->restaurant->average_rating
                    ] : null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Avaliação não encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * API: Atualizar avaliação
     */
    public function apiUpdateReview(Request $request, $reviewId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_name' => 'sometimes|string|max:255',
                'rating' => 'sometimes|integer|between:1,5',
                'comment' => 'sometimes|string|min:10|max:1000'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $review = Review::findOrFail($reviewId);
            
            $review->update($request->only(['user_name', 'rating', 'comment']));
            
            $restaurant = Restaurant::find($review->restaurant_id);
            if ($restaurant) {
                $restaurant->updateRatingStats();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Avaliação atualizada com sucesso!',
                'data' => $review
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar avaliação',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Listar todas as avaliações (com filtros)
     */
    public function apiAllReviews(Request $request)
    {
        try {
            $query = Review::with('restaurant');
            
            if ($request->has('restaurant_id')) {
                $query->where('restaurant_id', $request->restaurant_id);
            }
            
            if ($request->has('min_rating')) {
                $query->where('rating', '>=', $request->min_rating);
            }
            
            if ($request->has('max_rating')) {
                $query->where('rating', '<=', $request->max_rating);
            }
            
            if ($request->has('user_name')) {
                $query->where('user_name', 'like', '%' . $request->user_name . '%');
            }
            
            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }
            
            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }
            
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);
            
            $perPage = $request->per_page ?? 20;
            $reviews = $query->paginate($perPage);
            
            $reviewData = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('d/m/Y H:i'),
                    'time_ago' => $review->created_at->diffForHumans(),
                    'restaurant' => $review->restaurant ? [
                        'id' => $review->restaurant->id,
                        'name' => $review->restaurant->name,
                        'average_rating' => $review->restaurant->average_rating
                    ] : null
                ];
            });
            

            $stats = [
                'total_reviews' => Review::count(),
                'average_rating_all' => round(Review::avg('rating') ?? 0, 1),
                'total_restaurants' => Restaurant::count(),
                'reviews_today' => Review::whereDate('created_at', today())->count(),
                'reviews_this_month' => Review::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $reviewData,
                'stats' => $stats,
                'pagination' => [
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'has_more_pages' => $reviews->hasMorePages()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar avaliações',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}