<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cohorte extends Model
{
    use HasFactory;

    /**
     * Colonnes autorisées pour l'insertion et la mise à jour
     */
    protected $fillable = ['nom', 'code', 'date_de_creation'];

    /**
     * Une cohorte peut avoir plusieurs étudiants ou employés (exemple de relation).
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
