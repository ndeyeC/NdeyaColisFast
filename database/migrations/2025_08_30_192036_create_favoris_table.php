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
        Schema::create('favoris', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');       // client
            $table->unsignedBigInteger('livreur_id');    // livreur
            $table->timestamps();

            $table->unique(['user_id', 'livreur_id']);   // éviter doublons
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('livreur_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoris');
    }
};
