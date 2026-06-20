<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParticulierController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OffreController;

// ─── Routes publiques ───────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/reset-pass', [AuthController::class, 'resetPass'])->middleware('throttle:3,1');

// Offres publiques
Route::get('/offres',      [OffreController::class, 'index']);
Route::get('/offres/{id}', [OffreController::class, 'show']);

// ─── Routes authentifiées ───────────────────────────────────────────
Route::middleware(['auth:sanctum', 'not_blocked', 'throttle:120,1'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Notifications
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/lire',  [NotificationController::class, 'marquerLu']);
    Route::patch('/notifications/tout-lire',  [NotificationController::class, 'marquerToutLu']);

    // ─── Particulier ────────────────────────────────────────────────
    Route::middleware('role:particulier')->prefix('particulier')->group(function () {
        Route::get('/profil',                          [ParticulierController::class, 'gererProfil']);
        Route::put('/profil',                          [ParticulierController::class, 'updateProfil']);
        Route::post('/cv',                             [ParticulierController::class, 'uploadCV']);
        Route::post('/competences',                    [ParticulierController::class, 'ajouterCompetence']);
        Route::delete('/competences/{id}',             [ParticulierController::class, 'supprimerCompetence']);
        Route::get('/offres',                          [ParticulierController::class, 'consulterOffres']);
        Route::post('/postuler',                       [ParticulierController::class, 'postuler']);
        Route::get('/candidatures',                    [ParticulierController::class, 'suivreCandidature']);
    });

    // ─── Entreprise ─────────────────────────────────────────────────
    Route::middleware('role:entreprise')->prefix('entreprise')->group(function () {
        Route::get('/dashboard',                       [EntrepriseController::class, 'consulterDashboard']);
        Route::get('/offres',                          [EntrepriseController::class, 'consulter']);
        Route::post('/offres',                         [EntrepriseController::class, 'publier']);
        Route::put('/offres/{id}',                     [EntrepriseController::class, 'modifier']);
        Route::delete('/offres/{id}',                  [EntrepriseController::class, 'supprimer']);
        Route::get('/candidatures/{id}/cv',            [EntrepriseController::class, 'telechargerCV']);
        Route::patch('/candidatures/{id}/statut',      [EntrepriseController::class, 'statutCandidature']);
    });

    // ─── Admin ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/statistiques',                    [AdminController::class, 'consulterStatistiques']);
        Route::get('/entreprises',                     [AdminController::class, 'gererEntreprise']);
        Route::patch('/entreprises/{id}/valider',      [AdminController::class, 'validerEntreprise']);
        Route::patch('/utilisateurs/{id}/bloquer',     [AdminController::class, 'bloquerUtilisateur']);
        Route::patch('/utilisateurs/{id}/debloquer',   [AdminController::class, 'debloquerUtilisateur']);
        Route::post('/notifications',                  [NotificationController::class, 'envoyer']);
    });
});
