<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // create une nouvelle migration : php artisan make:migration drop_livreur_ratings_table

public function up()
{
    Schema::dropIfExists('livreur_ratings');
}

public function down()
{
    // Optionnel : recréer la table si rollback nécessaire
}

};
