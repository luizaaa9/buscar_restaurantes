<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Verificar se a tabela já existe
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('user_name');
                $table->integer('rating');
                $table->text('comment');
                $table->timestamps();
                
                $table->index('restaurant_id');
                $table->index('created_at');
            });
        } else {
            // Se a tabela já existe, garantir que tenha as colunas corretas
            Schema::table('reviews', function (Blueprint $table) {
                // Verificar e adicionar foreign key se não existir
                if (!Schema::hasColumn('reviews', 'restaurant_id')) {
                    $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                }
                
                // Verificar outras colunas
                $columns = ['user_name', 'rating', 'comment'];
                foreach ($columns as $column) {
                    if (!Schema::hasColumn('reviews', $column)) {
                        if ($column === 'user_name') {
                            $table->string('user_name')->after('restaurant_id');
                        } elseif ($column === 'rating') {
                            $table->integer('rating')->after('user_name');
                        } elseif ($column === 'comment') {
                            $table->text('comment')->after('rating');
                        }
                    }
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};