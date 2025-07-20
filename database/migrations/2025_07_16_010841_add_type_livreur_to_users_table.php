<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
       if (!Schema::hasColumn('users', 'type_livreur')) {
    $table->enum('type_livreur', ['urbain', 'classique'])->nullable()->after('role');
}

       
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['type_livreur']);
    });
}

};
