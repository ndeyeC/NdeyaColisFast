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
        Schema::create('detail_livraisons', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_livraison')->nullable(); 
            $table->string('statut_livraison')->default('En attente');
            $table->decimal('prix_livraison', 8, 2)->default(0); 
            $table->integer('duree_livraison')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_livraisons');
    }
};
