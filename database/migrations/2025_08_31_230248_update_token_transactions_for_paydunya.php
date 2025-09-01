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
            $table->string('payment_method')->default('paydunya')->change();
            
            $table->string('paydunya_token')->nullable()->after('reference');
        });

        DB::table('token_transactions')
            ->where('payment_method', 'cinetpay')
            ->update(['payment_method' => 'paydunya']);
    

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_transactions', function (Blueprint $table) {
            $table->dropColumn('paydunya_token');
        });

        DB::table('token_transactions')
            ->where('payment_method', 'paydunya')
            ->update(['payment_method' => 'cinetpay']);
    
    }
};
