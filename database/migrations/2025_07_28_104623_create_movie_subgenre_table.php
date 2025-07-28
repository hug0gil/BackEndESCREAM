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
        Schema::create('movie_subgenre', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete();
            $table->foreignId('subgenre_id')->constrained('subgenres')->cascadeOnDelete();
            $table->primary(['movie_id', 'subgenre_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_subgenre');
    }
};
