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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('zone',100); 
            $table->string('type_livraison',50); 
            $table->string('tranche_distance',50); 
            $table->string('tranche_poids',50); 
            $table->decimal('prix', 10, 2); 
            $table->timestamps();
            $table->unique(['zone', 'type_livraison', 'tranche_distance', 'tranche_poids']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};
