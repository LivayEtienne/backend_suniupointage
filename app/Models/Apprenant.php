<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apprenant extends Model
{
    use HasFactory;

    // Colonnes autorisées pour l'insertion et la mise à jour
    protected $fillable = ['user_id', 'id_cohorte'];

    /**
     * Un apprenant appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un apprenant appartient à une cohorte.
     */
    public function cohorte()
    {
        return $this->belongsTo(Cohorte::class, 'id_cohorte'); // Définir la relation avec la cohorte
    }
}
