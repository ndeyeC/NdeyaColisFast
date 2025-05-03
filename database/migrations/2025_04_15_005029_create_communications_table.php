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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Client ou admin
            $table->foreignId('livraison_id')->nullable()->constrained(); // Si lié à une livraison
            $table->text('message');
            $table->boolean('is_admin')->default(false); // Pour identifier l'expéditeur
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
