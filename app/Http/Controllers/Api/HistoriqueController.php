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
    public function index(Request $request)
    {
        // Initialiser la requête avec la relation 'utilisateur'
        $query = Historique::with('utilisateur');

        // Vérifier si une date est fournie et filtrer en fonction de celle-ci
        if ($request->has('date')) {
            $date = $request->input('date');

            // Valider le format de la date pour éviter des erreurs
            if (Carbon::createFromFormat('Y-m-d', $date) !== false) {
                $query->whereDate('heure_entree', $date);
            } else {
                // Si le format est incorrect, renvoyer une erreur
                return response()->json(['error' => 'Date invalide'], 400);
            }
        }

        // Récupérer les historiques filtrés (ou tous si aucune date n'est fournie)
        $historiques = $query->get();

        // Retourner les résultats sous forme de réponse JSON
        return response()->json($historiques);
    }

    /**
     * Créer un historique.
     */
    public function store(Request $request)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Vérifie que l'utilisateur existe
            'heure_entree' => 'required|date',
            'heure_sortie' => 'nullable|date',
        ]);

        // Créer un nouvel historique
        $historique = Historique::create([
            'user_id' => $validated['user_id'],
            'heure_entree' => $validated['heure_entree'],
            'heure_sortie' => $validated['heure_sortie'] ?? null, // Si heure_sortie est vide, mettre NULL
        ]);

        return response()->json(['message' => 'Historique créé avec succès.', 'historique' => $historique], 201);
    }

    /**
     * Mettre à jour l'activité d'un historique.
     */
    public function updateActivity(Request $request, $id)
    {
        // Valider les données entrantes
        $validated = $request->validate([
            'activite' => 'required|in:Activiter,Conger,Malade,Voyage', // Vérifie que la valeur est valide
        ]);

        // Trouver l'historique correspondant
        $historique = Historique::findOrFail($id);

        // Mettre à jour l'activité
        $historique->update([
            'activite' => $validated['activite'],
        ]);

        return response()->json(['message' => 'Activité mise à jour avec succès.', 'historique' => $historique], 200);
    }

    /**
     * Afficher un historique spécifique.
     */
    public function show($id)
    {
        // Trouver l'historique par son ID
        $historique = Historique::with('utilisateur')->find($id);

        // Vérifier si l'historique existe
        if (!$historique) {
            return response()->json(['message' => 'Historique non trouvé'], 404);
        }

        // Renvoyer l'historique trouvé
        return response()->json($historique, 200);
    }
}
