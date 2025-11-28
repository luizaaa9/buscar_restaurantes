<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'firebase_review_id',
        'user_name',
        'rating',
        'comment',
        'original_data'
    ];

    protected $casts = [
        'original_data' => 'array',
        'rating' => 'integer'
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}