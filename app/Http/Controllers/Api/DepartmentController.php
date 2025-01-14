<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Lister tous les départements.
     */
    public function index()
    {
        // Récupère tous les départements avec leurs employés
        return Department::with('employees')->get();
    }

    // Dans DepartmentController.php
public function getDepartmentCount()
{
    $departmentCount = \App\Models\Department::count();  // Compte le nombre de départements dans la table `departments`
    
    return response()->json(['departmentCount' => $departmentCount]);
}


    /**
     * Créer un nouveau département.
     */
    public function store(Request $request)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|unique:departments',
            'date_de_creation' => '|date',
        ]);

        // Créer un nouveau département
        $department = Department::create($validated);

        return response()->json(['message' => 'Département créé avec succès.', 'department' => $department], 201);
    }

    public function checkNameExists($nom)
    {
        $exists = Department::where('nom', $nom)->exists();
        return response()->json($exists);
    }
    /**
     * Afficher un département spécifique.
     */
    public function show($id)
    {
        // Récupérer un département avec ses employés
        $department = Department::with('employees')->findOrFail($id);

        return $department;
    }

    /**
     * Mettre à jour un département.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        // Valider les données entrantes
        $validated = $request->validate([
            'nom' => 'string|max:255',
            'code' => 'string|unique:departments,code,' . $id, // Vérifie l'unicité sauf pour ce département
            'date_de_creation' => 'date',
        ]);

        // Mettre à jour le département
        $department->update($validated);

        return response()->json(['message' => 'Département mis à jour avec succès.', 'department' => $department], 200);
    }

    /**
     * Supprimer un département.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        // Supprimer le département
        $department->delete();

        return response()->json(['message' => 'Département supprimé avec succès.'], 200);
    }
}
