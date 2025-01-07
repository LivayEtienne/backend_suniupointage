<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historiques', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->foreignId('user_id')->constrained('users'); // Clé étrangère vers users ou apprenants
            $table->timestamp('heure_entree'); // Heure d'entrée (pointage)
            $table->timestamp('heure_sortie')->nullable(); // Heure de sortie (nullable si l'utilisateur n'est pas encore sorti)
            $table->string('activite')->default('Activité'); // Nouvelle colonne pour l'activité avec une valeur par défaut
            $table->timestamps(); // Created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiques');
    }
};
