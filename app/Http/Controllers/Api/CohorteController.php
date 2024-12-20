<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cohorte;
use Illuminate\Http\Request;

class CohorteController extends Controller
{
    /**
     * Liste toutes les cohortes.
     */
    public function index()
    {
        return Cohorte::all(); // Retourne toutes les cohortes
    }

    /**
     * Crée une nouvelle cohorte.
     */
    public function store(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|unique:cohortes,code|max:50',
            'date_de_creation' => 'required|date',
        ]);

        // Créer une cohorte
        $cohorte = Cohorte::create($validated);

        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Cohorte créée avec succès.',
            'cohorte' => $cohorte
        ], 201);
    }

    /**
 * Met à jour une cohorte existante.
 */
public function update(Request $request, $id)
{
    // Trouver la cohorte par son ID
    $cohorte = Cohorte::findOrFail($id);

    // Valider les données
    $validated = $request->validate([
        'nom' => 'sometimes|string|max:255',
        'code' => 'sometimes|string|unique:cohortes,code,' . $id . '|max:50',
        'date_de_creation' => 'sometimes|date',
    ]);

    // Mettre à jour les champs validés
    $cohorte->update($validated);

    // Retourner une réponse JSON
    return response()->json([
        'message' => 'Cohorte mise à jour avec succès.',
        'cohorte' => $cohorte
    ], 200);
}

/**
 * Supprime une cohorte.
 */
public function destroy($id)
{
    // Trouver la cohorte par son ID
    $cohorte = Cohorte::findOrFail($id);

    // Supprimer la cohorte
    $cohorte->delete();

    // Retourner une réponse JSON
    return response()->json([
        'message' => 'Cohorte supprimée avec succès.'
    ], 200);
}


}
