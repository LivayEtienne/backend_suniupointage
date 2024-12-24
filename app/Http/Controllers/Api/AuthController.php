<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Authentifier l'utilisateur avec email et mot de passe
    public function login(Request $request)
    {
        // Validation des entrées
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Si la validation échoue, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Chercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Si l'utilisateur n'existe pas ou si le mot de passe est incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Créer et renvoyer un token
        $token = $user->createToken('API Token')->plainTextToken;

        // Message personnalisé en fonction du rôle
        $roleMessage = $this->getRoleMessage($user->role);

        // Réponse avec le token, message personnalisé et informations de l'utilisateur
        return response()->json([
            'message' => 'Authentication successful! ' . $roleMessage,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'role' => $user->role, // Affichage du rôle
            ]
        ], 200);
    }

    // Authentifier l'utilisateur avec cardId
    public function loginWithCardId(Request $request)
    {
        // Validation de cardId
        $validator = Validator::make($request->all(), [
            'cardId' => 'required|string',
        ]);

        // Si la validation échoue, renvoyer les erreurs
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Chercher l'utilisateur par cardId
        $user = User::where('cardId', $request->cardId)->first();

        // Si aucun utilisateur n'est trouvé
        if (!$user) {
            return response()->json(['error' => 'Card ID not found'], 404);
        }

        // Créer et renvoyer un token
        $token = $user->createToken('API Token')->plainTextToken;

        // Message personnalisé en fonction du rôle
        $roleMessage = $this->getRoleMessage($user->role);

        // Réponse avec le token, message personnalisé et informations utilisateur
        return response()->json([
            'message' => 'Authentication successful! ' . $roleMessage,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'role' => $user->role, // Affichage du rôle
            ]
        ], 200);
    }

    // Déconnexion de l'utilisateur
    public function logout(Request $request)
    {
        // Révoquer le token de l'utilisateur actuel
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        // Réponse de déconnexion réussie
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // Fonction pour retourner un message personnalisé selon le rôle
    private function getRoleMessage($role)
    {
        switch ($role) {
            case 'admin':
                return 'Bienvenue Mr l\'Admin';
            case 'vigile':
                return 'Bienvenue Mr le Vigile';
            // Vous pouvez ajouter plus de rôles si nécessaire
            default:
                return 'Bienvenue sur notre plateforme';
        }
    }
}
