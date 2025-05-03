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
        Schema::table('communications', function (Blueprint $table) {
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->boolean('is_read')->default(false);
            $table->string('receiver_type', 50);
            $table->string('sender_type', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            $table->dropColumn(['sender_id', 'receiver_id', 'is_read', 'receiver_type', 'sender_type']);
        });
    }
};
