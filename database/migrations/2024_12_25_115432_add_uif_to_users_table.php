<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Changer 'uif' pour une chaîne de caractères, car il contient des valeurs alphanumériques
            $table->string('uif')->unique()->after('cardId'); // unique pour garantir qu'il n'y a pas de doublons
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Suppression du champ 'uif'
            $table->dropColumn('uif');
        });
    }
};
