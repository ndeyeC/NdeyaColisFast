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
            $table->string('region_depart')->nullable();
            $table->string('region_arrivee')->nullable();
            $table->string('type_zone')->nullable();
            $table->integer('prix_base')->nullable();
            $table->integer('prix_final')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commnandes', function (Blueprint $table) {
            
            $table->dropColumn([
                'region_depart',
                'region_arrivee',
                'type_zone',
                'prix_base',
                'prix_final'
            ]);
        });
    
    }
};
