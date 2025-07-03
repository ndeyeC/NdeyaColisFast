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
        $table->integer('amount')->after('id'); 
        $table->string('payment_method')->after('amount'); 
        $table->string('status')->after('payment_method'); 
        $table->string('reference')->unique()->after('status'); 
        $table->text('notes')->nullable()->after('reference'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_transactions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'payment_method', 'status', 'reference', 'notes']);
        });
    }
};
