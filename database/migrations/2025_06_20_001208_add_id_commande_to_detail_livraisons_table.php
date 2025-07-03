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
        Schema::table('detail_livraisons', function (Blueprint $table) {
            $table->unsignedBigInteger('id_commande')->after('id'); // ou à l’endroit souhaité
            $table->foreign('id_commande')->references('id')->on('commnandes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_livraisons', function (Blueprint $table) {
            //
        });
    }
};
