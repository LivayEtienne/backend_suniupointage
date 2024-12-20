<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Historique;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    /**
     * Lister tous les historiques.
     */
    public function index()
    {
        // Récupérer tous les historiques
        $historiques = Historique::with('utilisateur')->get();
        return response()->json($historiques);
    }

    /**
     * Créer un historique.
     */
    public function store(Request $request)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',  // Vérifie que l'utilisateur existe
            'date' => 'required|date',
            'action' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Créer un nouvel historique
        $historique = Historique::create($validated);

        return response()->json(['message' => 'Historique créé avec succès.', 'historique' => $historique], 201);
    }
}
