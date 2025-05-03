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
        Schema::create('commnandes', function (Blueprint $table) {
            $table->id();
            $table->integer('quantite'); 
            $table->string('adresse_arrivee'); 
            $table->string('adresse_depart'); 
            $table->string('mode_paiement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commnandes');
    }
};
