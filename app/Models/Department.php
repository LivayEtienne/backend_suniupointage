<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    // Colonnes autorisées pour l'insertion et la mise à jour
    protected $fillable = ['nom', 'code', 'date_de_creation'];

       /**
     * Un département a plusieurs employés.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_departement');
    }
}
