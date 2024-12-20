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
            $table->timestamp('date'); // Date et heure de l'événement
            $table->string('action'); // Type d'action (par exemple : "pointage")
            $table->text('description'); // Description de l'événement
            $table->timestamps(); // Created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiques');
    }
};
