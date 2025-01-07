<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'heure_entree', 'heure_sortie', 'activite'];

    /**
     * L'historique appartient à un utilisateur.
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
