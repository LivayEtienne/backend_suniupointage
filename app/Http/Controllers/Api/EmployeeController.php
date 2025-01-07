<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Lister tous les employés avec leurs départements.
     */
    public function index()
    {
        // Récupère tous les employés avec leurs relations (département)
        $employees = Employee::with('department')->get();

        return response()->json($employees, 200);
    }

    /**
     * Créer un nouvel employé.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Vérifie si l'utilisateur existe
            'fonction' => 'required|string|max:255',
            'id_departement' => 'required|exists:departments,id', // Vérifie si le département existe
        ]);

        // Création de l'employé
        $employee = Employee::create($validated);

        return response()->json([
            'message' => 'Employé créé avec succès.',
            'employee' => $employee,
        ], 201);
    }

    /**
     * Afficher un employé spécifique.
     */
    public function show($id)
    {
        // Recherche de l'employé avec ses relations
        $employee = Employee::with('departement')->findOrFail($id);

        return response()->json($employee, 200);
    }

    /**
     * Mettre à jour un employé.
     */
    public function update(Request $request, $id)
    {
        // Recherche de l'employé
        $employee = Employee::findOrFail($id);

        // Validation des données mises à jour
        $validated = $request->validate([
            'fonction' => 'string|max:255',
            'id_departement' => 'exists:departments,id', // Vérifie si le département existe
        ]);

        // Mise à jour de l'employé
        $employee->update($validated);

        return response()->json([
            'message' => 'Employé mis à jour avec succès.',
            'employee' => $employee,
        ], 200);
    }

    /**
     * Supprimer un employé.
     */
    public function destroy($id)
    {
        // Recherche de l'employé
        $employee = Employee::findOrFail($id);

        // Suppression de l'employé
        $employee->delete();

        return response()->json(['message' => 'Employé supprimé avec succès.'], 200);
    }
}
