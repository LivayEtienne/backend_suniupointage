<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http; // Pour envoyer des requêtes HTTP
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all(); // Récupérer tous les utilisateurs
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données entrantes
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'photo' => 'nullable|string', // Facultatif
            'email' => 'required|email|unique:users',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'cardId' => 'nullable|string|unique:users', // Facultatif
            'role' => 'required|string',
            'statut' => 'nullable|string', // Facultatif
            'password' => 'required|string|min:8',
        ]);

        // Hacher le mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Créer un nouvel utilisateur dans la base de données Laravel
        $user = User::create($validated);


        try {
            // Envoyer une requête POST à l'API Node.js
            $response = Http::post('http://localhost:4000/api/users', [
                'userId' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role,
                'matricule' => $user->matricule, // Assurez-vous que ce champ existe
            ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Utilisateur créé, mais échec de la synchronisation avec Node.js.',
                    'user' => $user,
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Utilisateur créé, mais une erreur est survenue lors de la synchronisation avec Node.js.',
                'error' => $e->getMessage(),
                'user' => $user,
            ], 201);
        }

        return response()->json(['message' => 'Utilisateur créé avec succès et synchronisé avec Node.js.', 'user' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Rechercher l'utilisateur par ID
        $user = User::findOrFail($id);
    
        // Valider les données mises à jour
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string',
            'prenom' => 'sometimes|string|max:255',
            'photo' => 'nullable|string',
            'adresse' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'cardId' => 'nullable|string|unique:users,cardId,' . $id,
            'statut' => 'nullable|string',
            'password' => 'sometimes|string|min:8',
        ]);
    
        // Hacher le mot de passe si fourni
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
    
        // Mettre à jour l'utilisateur dans MySQL
        $user->update($validated);
    
        // Préparer les données pour l'API Node.js
        $nodePayload = [
            'nom' => $user->nom,
            'email' => $user->email,
            'role' => $user->role,
            'matricule' => $user->matricule,
            'status' => $user->statut,
        ];
    
        // Communiquer avec l'API Node.js pour mettre à jour dans MongoDB
        try {
            $response = Http::put("http://localhost:4000/api/users/{$user->matricule}", $nodePayload);
    
            if ($response->failed()) {
                return response()->json([
                    'message' => 'Mise à jour dans MongoDB échouée',
                    'error' => $response->body(),
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur de communication avec le microservice Node.js',
                'error' => $e->getMessage(),
            ], 500);
        }
    
        // Retourner la réponse de succès
        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès dans MySQL et MongoDB.',
            'user' => $user,
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
