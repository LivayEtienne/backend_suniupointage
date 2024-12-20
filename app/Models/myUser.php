<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class myUser extends Model
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

}
