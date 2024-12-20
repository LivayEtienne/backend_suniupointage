<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Colonnes autorisées pour l'insertion et la mise à jour
    protected $fillable = ['user_id', 'fonction', 'id_departement'];

    /**
     * Un employé appartient à un département.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_departement');
    }

    /**
     * Un employé appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
