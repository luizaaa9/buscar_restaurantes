<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FirebaseLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'collection',
        'document_id',
        'data',
        'error_message',
        'success'
    ];

    protected $casts = [
        'data' => 'array',
        'success' => 'boolean'
    ];
}