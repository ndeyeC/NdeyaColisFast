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
            $table->foreignId('delivery_zone_id')->nullable()->after('payment_method');
            $table->timestamp('expiry_date')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_transactions', function (Blueprint $table) {
        $table->dropColumn(['delivery_zone_id', 'expiry_date']);

        });
    }
};
