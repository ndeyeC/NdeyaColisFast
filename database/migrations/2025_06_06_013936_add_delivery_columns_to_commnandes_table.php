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
        Schema::table('commnandes', function (Blueprint $table) {
            $table->timestamp('date_acceptation')->nullable()->after('driver_id');
            $table->timestamp('date_debut_livraison')->nullable()->after('date_acceptation');
            $table->timestamp('date_livraison')->nullable()->after('date_debut_livraison');
            $table->timestamp('date_annulation')->nullable()->after('date_livraison');
            
            // Colonnes pour les détails de livraison
            $table->text('commentaire_livraison')->nullable()->after('date_annulation');
            $table->string('photo_livraison')->nullable()->after('commentaire_livraison');
            $table->decimal('lat_livraison', 10, 8)->nullable()->after('photo_livraison');
            $table->decimal('lng_livraison', 11, 8)->nullable()->after('lat_livraison');
            
            // Colonnes pour les problèmes et annulations
            $table->json('probleme_signale')->nullable()->after('lng_livraison');
            $table->text('raison_annulation')->nullable()->after('probleme_signale');
            
            // Ajouter les nouveaux statuts
            // Modifier la colonne status si nécessaire pour inclure les nouveaux statuts
        });
    }
        
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commnandes', function (Blueprint $table) {
             $table->dropColumn([
                'date_acceptation',
                'date_debut_livraison', 
                'date_livraison',
                'date_annulation',
                'commentaire_livraison',
                'photo_livraison',
                'lat_livraison',
                'lng_livraison',
                'probleme_signale',
                'raison_annulation'
            ]);
        });
    }
};
