<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Particulier;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|unique:utilisateurs,email',
            'pass'     => 'required|min:8|confirmed',
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'role'     => 'required|in:particulier,entreprise',
        ]);

        $utilisateur = Utilisateur::create([
            'email'  => $request->email,
            'pass'   => Hash::make($request->pass),
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'role'   => $request->role,
        ]);

        if ($request->role === 'particulier') {
            Particulier::create(['utilisateur_id' => $utilisateur->id]);
        } elseif ($request->role === 'entreprise') {
            Entreprise::create([
                'utilisateur_id' => $utilisateur->id,
                'nom' => $request->nom,
            ]);
        }

        return response()->json([
            'message' => 'Inscription reussie.',
            'user'    => $utilisateur,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (!$utilisateur || !Hash::check($request->pass, $utilisateur->pass)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if ($utilisateur->role === 'bloque') {
            return response()->json(['message' => 'Compte suspendu.'], 403);
        }

        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion reussie.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $utilisateur,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Deconnexion reussie.']);
    }

    public function resetPass(Request $request)
    {
        return response()->json([
            'message' => 'La reinitialisation API directe est desactivee. Utilisez le flux email securise.',
        ], 410);
    }
}
