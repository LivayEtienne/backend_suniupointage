<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // Définir les colonnes qui peuvent être remplies via des formulaires
    protected $fillable = [
        'nom',
        'prenom',
        'photo',
        'email',
        'adresse',
        'telephone',
        'matricule',
        'cardId',
        'role',
        'statut', // Optionnel, car il a une valeur par défaut
    ];
}
