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
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commnandes', 'id'); // Clé étrangère ajustée
            $table->foreignId('driver_id')->constrained('users');
             $table->json('start_point')->comment('{lat, lng}');
              $table->json('end_point')->comment('{lat, lng}');
              $table->json('polyline')->nullable()->comment('Tracé OSRM');
               $table->json('steps')->nullable()->comment('Étapes détaillées');
               $table->decimal('distance_km', 8, 2);
               $table->integer('duration_minutes');
               $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                 $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
    }
};
