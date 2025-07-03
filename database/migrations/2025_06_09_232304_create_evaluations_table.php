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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->integer('note')->unsigned(); // 1 à 5
            $table->text('commentaire')->nullable();
            $table->enum('type_evaluation', ['client', 'driver'])->default('client');
            
            $table->timestamps();
            
            // Un client ne peut évaluer qu'une seule fois par commande
            $table->unique(['commande_id', 'user_id', 'type_evaluation']);
            
            // Index
            $table->index(['driver_id', 'note']);
            $table->index(['created_at', 'note']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
