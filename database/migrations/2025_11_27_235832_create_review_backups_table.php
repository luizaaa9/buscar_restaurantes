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
        Schema::create('review_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('firebase_review_id');
            $table->string('user_name');
            $table->integer('rating');
            $table->text('comment');
            $table->json('original_data')->nullable();
            $table->timestamps();
            $table->index('restaurant_id');
            $table->index('firebase_review_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_backups');
    }
};