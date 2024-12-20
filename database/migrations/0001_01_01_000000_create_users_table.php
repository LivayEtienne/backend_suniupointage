<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('photo')->nullable();
            $table->string('email')->unique();
            $table->string('adresse');
            $table->string('telephone');
            $table->string('matricule')->unique();
            $table->string('cardId')->unique();
            $table->string('role'); // apprenant, employé, vigile, admin
            $table->string('statut')->default('actif'); // actif, bloqué
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
