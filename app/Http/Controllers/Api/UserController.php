<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
    // Valider les données entrantes
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

    // Créer un nouvel utilisateur (le matricule sera généré automatiquement)
    $user = User::create($validated);

    return response()->json(['message' => 'Utilisateur créé avec succès.', 'user' => $user], 201);
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
    $user = User::findOrFail($id);

    // Valider les données mises à jour
    $validated = $request->validate([
        'nom' => 'sometimes|string|max:255',
        'prenom' => 'sometimes|string|max:255',
        'photo' => 'nullable|string',
        'email' => 'sometimes|email|unique:users,email,' . $id,
        'adresse' => 'sometimes|string',
        'telephone' => 'sometimes|string',
        'cardId' => 'nullable|string|unique:users,cardId,' . $id,
        'role' => 'sometimes|string',
        'statut' => 'nullable|string',
        'password' => 'sometimes|string|min:8',
    ]);

    // Hacher le mot de passe si fourni
    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    $user->update($validated);

    return response()->json(['message' => 'Utilisateur mis à jour avec succès.', 'user' => $user]);
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
