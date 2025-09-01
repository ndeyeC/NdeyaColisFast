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
        Schema::table('delivery_routes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_routes', 'final_position')) {
                $table->json('final_position')->nullable()->after('current_position');
            }
            
            if (!Schema::hasColumn('delivery_routes', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('final_position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_routes', function (Blueprint $table) {
         $table->dropColumn(['final_position', 'completed_at']);

        });
    }
};
