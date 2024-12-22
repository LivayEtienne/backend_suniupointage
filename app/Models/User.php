<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements Authenticatable
{
    use HasFactory, HasApiTokens;

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
        'statut',
        'password',
    ];

    protected $hidden = ['password'];

    // Implémentation des méthodes de l'interface Authenticatable

    public function getAuthIdentifierName()
    {
        return 'id'; // Nom de la colonne utilisée pour identifier l'utilisateur (par défaut 'id')
    }

    public function getAuthIdentifier()
    {
        return $this->getKey(); // Retourne l'identifiant de l'utilisateur (par défaut la clé primaire)
    }

    public function getAuthPassword()
    {
        return $this->password; // Retourne le mot de passe de l'utilisateur
    }

    public function getRememberToken()
    {
        return $this->remember_token; // Si vous utilisez les "remember me" tokens
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value; // Définit le "remember token"
    }

    public function getRememberTokenName()
    {
        return 'remember_token'; // Nom de la colonne "remember_token"
    }

    public function getAuthPasswordName()
    {
        return 'password'; // Le nom de la colonne du mot de passe (par défaut 'password')
    }

    /**
     * Boot method to automatically generate the matricule when creating a user.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->matricule)) {
                $user->matricule = 'MAT-' . strtoupper(uniqid());
            }
        });
    }
}
