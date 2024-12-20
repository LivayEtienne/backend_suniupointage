<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apprenants', function (Blueprint $table) {
            $table->id(); // ID unique de l'apprenant
            $table->foreignId('user_id')->constrained('users'); // Relation avec la table users
            $table->foreignId('id_cohorte')->constrained('cohortes'); // Relation avec la table cohortes
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apprenants');
    }
};
