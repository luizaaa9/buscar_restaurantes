<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_rating' => 'decimal:1',
    ];

   public function reviews()
{
    return $this->hasMany(Review::class);
}

    public function updateRatingStats()
    {
        $this->total_reviews = $this->reviews()->count();
        $this->average_rating = $this->reviews()->avg('rating') ?? 0;
        $this->save();
    }

    public function storePhoto($file)
    {
        $path = $file->store('restaurants/photos', 'public');
        return [
            'url' => Storage::url($path),
            'path' => $path,
            'filename' => $file->getClientOriginalName()
        ];
    }

    public function deletePhoto($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    
}