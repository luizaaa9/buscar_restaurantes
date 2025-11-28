<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->string('address', 500);
            $table->decimal('latitude', 10, 8); 
            $table->decimal('longitude', 11, 8);
            $table->json('cuisine_types'); 
            $table->json('photos'); 
            $table->decimal('average_rating', 3, 1)->default(0); 
            $table->integer('total_reviews')->default(0);
            $table->timestamps(); 
            $table->index('average_rating');
            $table->index('total_reviews');
            $table->index('created_at');
            $table->fullText(['name', 'description', 'address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};