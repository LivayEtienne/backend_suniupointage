<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use Illuminate\Http\Request;

class ApprenantController extends Controller
{
    /**
     * Lister tous les apprenants.
     */
    public function index()
    {
        // Inclure à la fois l'utilisateur et la cohorte (avec 'cohorte' qui correspond à 'id_cohorte')
        return Apprenant::with('user', 'cohorte')->get(); 
    }

    /**
     * Créer un nouvel apprenant.
     */
    public function store(Request $request)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',  // Vérifie que l'user_id existe dans la table users
            'id_cohorte' => 'required|exists:cohortes,id',  // Vérifie que l'id_cohorte existe dans la table cohortes
        ]);

        // Créer un apprenant
        $apprenant = Apprenant::create([
            'user_id' => $validated['user_id'],
            'id_cohorte' => $validated['id_cohorte']
        ]);

        return response()->json(['message' => 'Apprenant créé avec succès.', 'apprenant' => $apprenant], 201);
    }

    /**
     * Afficher les détails d'un apprenant.
     */
    public function show($id)
    {
        // Récupérer un apprenant avec ses détails, y compris l'utilisateur et la cohorte
        $apprenant = Apprenant::with('user', 'cohorte')->findOrFail($id);
        return response()->json($apprenant);
    }

    /**
     * Mettre à jour un apprenant.
     */
    public function update(Request $request, $id)
    {
        // Trouver l'apprenant existant
        $apprenant = Apprenant::findOrFail($id);

        // Valider les données entrantes
        $validated = $request->validate([
            'id_cohorte' => 'required|exists:cohortes,id',  // Vérifie que l'id_cohorte existe
        ]);

        // Mettre à jour l'apprenant
        $apprenant->update($validated);

        return response()->json(['message' => 'Apprenant mis à jour avec succès.', 'apprenant' => $apprenant], 200);
    }

    /**
     * Supprimer un apprenant.
     */
    public function destroy($id)
    {
        // Trouver l'apprenant et le supprimer
        $apprenant = Apprenant::findOrFail($id);
        $apprenant->delete();

        return response()->json(['message' => 'Apprenant supprimé avec succès.'], 200);
    }
}
