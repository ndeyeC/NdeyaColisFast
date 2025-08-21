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
        Schema::table('token_transactions', function (Blueprint $table) {
             $table->unsignedBigInteger('commande_id')->nullable()->after('delivery_zone_id');
              $table->foreign('commande_id')->references('id')->on('commnandes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_transactions', function (Blueprint $table) {
            $table->dropForeign(['commande_id']);
            $table->dropColumn('commande_id');
        });
    }
};
