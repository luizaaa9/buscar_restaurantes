<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);

        try {
            $firestore = app('firebase.firestore')->database();
            
            $reviewData = [
                'restaurant_id' => (string)$restaurant->id,
                'user_name' => $request->user_name,
                'rating' => (int)$request->rating,
                'comment' => $request->comment,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            $firestore->collection('reviews')->add($reviewData);

            $restaurant->updateRatingStats();

            return redirect()->route('restaurants.show', $restaurant->id)
                ->with('success', 'Avaliação enviada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar avaliação: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($reviewId)
    {
        try {
            $firestore = app('firebase.firestore')->database();
            
            $reviewDoc = $firestore->collection('reviews')->document($reviewId);
            $review = $reviewDoc->snapshot();
            
            if ($review->exists()) {
                $restaurantId = $review->data()['restaurant_id'];
                
                $reviewDoc->delete();
                
                $restaurant = Restaurant::find($restaurantId);
                if ($restaurant) {
                    $restaurant->updateRatingStats();
                }
                
                return response()->json(['success' => true]);
            }
            
            return response()->json(['success' => false, 'message' => 'Review não encontrada']);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao deletar review: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao deletar review']);
        }
    }
}