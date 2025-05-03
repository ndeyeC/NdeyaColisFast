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
        Schema::table('commnandes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(); // Associer un utilisateur (optionnel)
        $table->enum('type_livraison', ['standard', 'express']); // Définition des types de livraison
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commnandes', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'type_livraison']); // Suppression des colonnes ajoutées
        });
    }
};
