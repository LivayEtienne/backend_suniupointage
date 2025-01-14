<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http; // Pour envoyer des requêtes HTTP
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\Cohorte;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;   // Pour utiliser les transactions de base de données
use GuzzleHttp\Client;

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
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Photo devient facultative
        'email' => 'required|email|unique:users',
        'adresse' => 'required|string',
        'telephone' => 'required|string',
        'cardId' => 'nullable|string|unique:users', 
        'role' => 'required|string',
        'statut' => 'nullable|string',
        'password' => 'nullable|string|min:8',
    ]);

    // Hacher le mot de passe
    if (!empty($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        unset($validated['password']); // Supprime la clé si elle est vide
    }

    // Si une photo est envoyée, on la stocke
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('photos', 'public');
        $validated['photo'] = $photoPath;
    }

    // Générer automatiquement le matricule
    $lastUser = User::latest()->first();
    $newMatricule = 'USR-' . str_pad($lastUser ? $lastUser->id + 1 : 1, 6, '0', STR_PAD_LEFT);
    $validated['matricule'] = $newMatricule;

    // Créer un nouvel utilisateur
    $user = User::create($validated);

    // Envoyer les données au serveur Node.js pour synchroniser l'utilisateur avec MongoDB
    $client = new Client();  // Utilisation de Guzzle
    try {
        $response = $client->post('http://localhost:4001/api/sync-user', [
            'json' => [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'photo' => $user->photo, // Si vous stockez une photo, vous pouvez l'envoyer aussi
                'adresse' => $user->adresse,
                'telephone' => $user->telephone,
                'cardId' => $user->cardId,
                'role' => $user->role,
                'statut' => $user->statut,
                'matricule' => $user->matricule,
            ]
        ]);

        // Si la réponse de Node.js est positive
        $data = json_decode($response->getBody()->getContents(), true);
        \Log::info('Réponse de Node.js :', $data);

    } catch (\Exception $e) {
        // Gérer les erreurs si la communication avec Node.js échoue
        \Log::error('Erreur de communication avec Node.js : ' . $e->getMessage());
        return response()->json([
            'message' => 'Utilisateur créé avec succès, mais erreur de synchronisation avec Node.js.',
            'user' => $user,
        ], 201);
    }

    // Retourner la réponse avec statut 201
    return response()->json([
        'message' => 'Utilisateur créé avec succès et synchronisé avec Node.js.',
        'user' => $user,
    ], 201);
}

    public function archiver($id)
    {
        // Trouver l'utilisateur par son ID
        $user = User::find($id);
    
        // Vérifier si l'utilisateur existe
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    
        // Mettre à jour le statut de l'utilisateur pour le marquer comme "inactif"
        $user->statut = 'inactif';
        $user->save();
    
        // Retourner une réponse avec le statut mis à jour
        return response()->json([
            'message' => 'Utilisateur archivé avec succès.',
            'user' => $user
        ]);
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
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Valider les données mises à jour
    $validated = $request->validate([
        'nom' => 'sometimes|string|max:255',
        'prenom' => 'sometimes|string|max:255',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'email' => 'sometimes|email|unique:users,email,' . $id,
        'adresse' => 'sometimes|string',
        'telephone' => 'sometimes|string',
        'matricule' => 'sometimes|string|unique:users,matricule,' . $id,
        'cardId' => 'nullable|string|unique:users,cardId,' . $id,
        'role' => 'sometimes|string',
        'statut' => 'nullable|string',
        'mot_de_passe' => 'nullable|string|min:8',
        'departementid' => 'sometimes|exists:departments,id',
    ]);

    // Mise à jour du mot de passe si fourni
    if (!empty($validated['mot_de_passe'])) {
        $validated['mot_de_passe'] = bcrypt($validated['mot_de_passe']);
    }

    // Mise à jour de la carte UID si fournie
    if ($request->has('cardId')) {
        $validated['cardId'] = $request->input('cardId');
    }
    

    // Mise à jour de la photo si un fichier est envoyé
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('photos', $filename, 'public');

        // Mettre à jour l'image dans la base de données
        $validated['photo'] = $path;
    }

    // Mise à jour de l'utilisateur
    $user->update($validated);

    // Retourner une réponse JSON avec le statut HTTP 200
    return response()->json([
        'message' => 'Utilisateur mis à jour avec succès.',
        'user' => $user,
    ], 200);
}

    // Exemple de méthode dans un contrôleur Laravel pour gérer la suppression multiple
public function deleteMultipleUsers(Request $request)
{
    $userIds = $request->input('ids'); // Récupérer les IDs des utilisateurs à supprimer

    // Valider les IDs reçus
    if (empty($userIds) || !is_array($userIds)) {
        return response()->json(['error' => 'Aucun utilisateur sélectionné'], 400);
    }

    // Supprimer les utilisateurs par leurs IDs
    User::whereIn('id', $userIds)->delete();

    return response()->json(['message' => 'Utilisateurs supprimés avec succès']);
}

    // app/Http/Controllers/Api/UserController.php

public function updateUid(Request $request, $id)
{
    // Trouver l'utilisateur par ID
    $user = User::findOrFail($id);

    // Valider les données du cardId
    $validated = $request->validate([
        'uid' => 'nullable|string|unique:users,cardId,' . $id,
    ]);

    // Mettre à jour l'UID (cardId)
    $user->cardId = $validated['uid'];
    $user->save(); // Enregistrer les modifications

    // Retourner une réponse JSON avec le message de succès
    return response()->json([
        'message' => 'UID mis à jour avec succès',
        'user' => $user,
    ], 200);
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

    public function getUserStats()
    {
        // Récupérer le nombre total d'utilisateurs
        $totalUsers = User::count();

        // Récupérer le nombre d'utilisateurs avec le rôle 'vigile'
        $totalVigiles = User::where('role', 'vigile')->count();

        // Récupérer le nombre total de départements
        $totalDepartments = Department::count();

        // Récupérer le nombre total de cohortes
        $totalCohortes = Cohorte::count();

        // Retourner les statistiques sous forme de réponse JSON
        return response()->json([
            'totalUsers' => $totalUsers,
            'totalVigiles' => $totalVigiles,
            'totalDepartments' => $totalDepartments,
            'totalCohortes' => $totalCohortes,
        ]);
    }

    //ajout forma csv ajouter un format csv pour ajouter plusieur users

public function importFromCSV(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $file = $request->file('file');

    try {
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $headers = array_shift($csvData);

        $expectedHeaders = ['nom', 'prenom', 'email', 'adresse', 'telephone', 'role', 'departement_id', 'mot_de_passe'];
        if ($headers !== $expectedHeaders) {
            return response()->json(['error' => 'Le fichier CSV ne contient pas les colonnes attendues.'], 400);
        }

        DB::beginTransaction();
        
        foreach ($csvData as $row) {
            $data = array_combine($headers, $row);

            // Validation des données individuelles
            $validator = Validator::make($data, [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'adresse' => 'required|string|max:255',
                'telephone' => 'required|string|max:20|unique:users,telephone',
                'role' => 'required|string',
                'departement_id' => 'required|exists:departments,id',
                'mot_de_passe' => 'nullable|string|min:8',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Validation échouée pour une ligne: ' . implode(', ', $validator->errors()->all()));
            }

            // Générer le matricule
            $matricule = $this->generateNewMatricule();

            // Créer l'utilisateur
            User::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'adresse' => $data['adresse'],
                'telephone' => $data['telephone'],
                'matricule' => $matricule,
                'role' => $data['role'],
                'departement_id' => $data['departement_id'],
                'mot_de_passe' => isset($data['mot_de_passe']) ? Hash::make($data['mot_de_passe']) : null,
                'statut' => 'actif',
            ]);
        }

        DB::commit();
        return response()->json(['message' => 'Les utilisateurs ont été importés avec succès.'], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

private function generateNewMatricule()
{
    $lastMatricule = DB::table('users')
        ->where('matricule', 'like', 'USR-%')
        ->orderByRaw('CAST(SUBSTRING(matricule, 5) AS SIGNED) DESC')
        ->lockForUpdate()
        ->first();

    if ($lastMatricule) {
        $lastNumber = intval(substr($lastMatricule->matricule, 4));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    return 'USR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}

public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids'); // Tableau d'IDs des utilisateurs à supprimer

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        // Supprimer d'abord les apprenants associés
        DB::table('apprenants')->whereIn('user_id', $ids)->delete();

        // Puis supprimer les utilisateurs
        User::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Users and associated records deleted successfully']);
    }
}
