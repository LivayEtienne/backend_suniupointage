<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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

    public function importApprenants(Request $request)
    {
        // Validation du fichier CSV
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');

        try {
            // Lire et traiter le fichier CSV
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $headers = array_shift($csvData);

            // Vérification des en-têtes
            $expectedHeaders = ['nom', 'prenom', 'email', 'password', 'adresse', 'telephone', 'id_cohorte'];
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
                    'password' => 'required|string|min:6',
                    'adresse' => 'nullable|string|max:255',
                    'telephone' => 'nullable|string|max:20',
                    'id_cohorte' => 'required|exists:cohortes,id',
                ]);

                if ($validator->fails()) {
                    throw new \Exception('Validation échouée pour une ligne: ' . implode(', ', $validator->errors()->all()));
                }

                // Créer l'utilisateur
                $userId = DB::table('users')->insertGetId([
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'adresse' => $data['adresse'] ?? null,
                    'telephone' => $data['telephone'] ?? null,
                    'role' => 'Apprenant',
                    'statut' => 'actif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Créer l'entrée dans la table apprenants
                DB::table('apprenants')->insert([
                    'user_id' => $userId,
                    'id_cohorte' => $data['id_cohorte'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Les apprenants ont été importés avec succès.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
