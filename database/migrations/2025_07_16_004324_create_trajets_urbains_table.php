<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('trajets_urbains', function (Blueprint $table) {
        $table->id();
        $table->foreignId('livreur_id')->constrained('livreurs')->onDelete('cascade');
        $table->string('type_voiture');
        $table->string('matricule');
        $table->time('heure_depart');
        $table->string('destination_region');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets_urbains');
    }
};
