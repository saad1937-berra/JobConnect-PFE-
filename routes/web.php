<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\OffreWebController;
use App\Http\Controllers\Web\ParticulierWebController;
use App\Http\Controllers\Web\EntrepriseWebController;
use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\NotificationWebController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login',    [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthWebController::class, 'login']);
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthWebController::class, 'register']);
Route::post('/logout',  [AuthWebController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/mot-de-passe/oublie', [AuthWebController::class, 'showForgotForm'])->name('password.request');
Route::post('/mot-de-passe/email',  [AuthWebController::class, 'sendResetLink'])->name('password.email');
Route::get('/mot-de-passe/reset/{token}', [AuthWebController::class, 'showResetForm'])->name('password.reset');
Route::post('/mot-de-passe/update', [AuthWebController::class, 'resetPassword'])->name('password.update');

Route::get('/offres',          [OffreWebController::class, 'index'])->name('offres.index');
Route::get('/offres/{id}',     [OffreWebController::class, 'show'])->name('offres.show');


Route::middleware('auth')->group(function () {

    Route::get('/notifications',                [NotificationWebController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/lire',    [NotificationWebController::class, 'marquerLu'])->name('notifications.lire');
    Route::patch('/notifications/tout-lire',    [NotificationWebController::class, 'marquerToutLu'])->name('notifications.lire.tout');

    Route::middleware('role:particulier')->prefix('particulier')->name('particulier.')->group(function () {

        Route::get('/profil',           [ParticulierWebController::class, 'profil'])->name('profil');
        Route::put('/profil',           [ParticulierWebController::class, 'updateProfil'])->name('profil.update');

        Route::post('/cv',              [ParticulierWebController::class, 'uploadCV'])->name('cv.upload');

        Route::post('/competences',     [ParticulierWebController::class, 'ajouterCompetence'])->name('competence.ajouter');
        Route::delete('/competences/{id}', [ParticulierWebController::class, 'supprimerCompetence'])->name('competence.supprimer');

        Route::post('/postuler',        [ParticulierWebController::class, 'postuler'])->name('postuler');
        Route::get('/candidatures',     [ParticulierWebController::class, 'candidatures'])->name('candidatures');
    });

    Route::middleware('role:entreprise')->prefix('entreprise')->name('entreprise.')->group(function () {

        Route::get('/dashboard',        [EntrepriseWebController::class, 'dashboard'])->name('dashboard');

        Route::get('/profil',           [EntrepriseWebController::class, 'profil'])->name('profil');
        Route::put('/profil',           [EntrepriseWebController::class, 'updateProfil'])->name('profil.update');

        Route::get('/offres',           [EntrepriseWebController::class, 'offres'])->name('offres');
        Route::get('/offres/creer',     [EntrepriseWebController::class, 'creerOffre'])->name('offres.creer');
        Route::post('/offres',          [EntrepriseWebController::class, 'storeOffre'])->name('offres.store');
        Route::get('/offres/{id}/edit', [EntrepriseWebController::class, 'editOffre'])->name('offres.edit');
        Route::put('/offres/{id}',      [EntrepriseWebController::class, 'updateOffre'])->name('offres.update');
        Route::delete('/offres/{id}',   [EntrepriseWebController::class, 'supprimerOffre'])->name('offres.supprimer');

        Route::get('/candidatures',             [EntrepriseWebController::class, 'candidatures'])->name('candidatures');
        Route::get('/candidatures/{id}',        [EntrepriseWebController::class, 'showCandidature'])->name('candidature.show');
        Route::patch('/candidatures/{id}/statut', [EntrepriseWebController::class, 'changerStatut'])->name('candidature.statut');
        Route::get('/candidatures/{id}/cv',     [EntrepriseWebController::class, 'telechargerCV'])->name('candidature.cv');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard',                        [AdminWebController::class, 'dashboard'])->name('dashboard');

        Route::get('/entreprises',                      [AdminWebController::class, 'entreprises'])->name('entreprises');
        Route::patch('/entreprises/{id}/valider',       [AdminWebController::class, 'validerEntreprise'])->name('entreprises.valider');

        Route::get('/utilisateurs',                     [AdminWebController::class, 'utilisateurs'])->name('utilisateurs');
        Route::patch('/utilisateurs/{id}/bloquer',      [AdminWebController::class, 'bloquerUtilisateur'])->name('utilisateurs.bloquer');

        Route::get('/offres',                           [AdminWebController::class, 'offres'])->name('offres');
        Route::delete('/offres/{id}',                   [AdminWebController::class, 'supprimerOffre'])->name('offres.supprimer');

        Route::get('/categories',                       [AdminWebController::class, 'categories'])->name('categories');
        Route::post('/categories',                      [AdminWebController::class, 'storeCategorie'])->name('categories.store');
        Route::put('/categories/{id}',                  [AdminWebController::class, 'updateCategorie'])->name('categories.update');
        Route::delete('/categories/{id}',               [AdminWebController::class, 'supprimerCategorie'])->name('categories.supprimer');

        Route::get('/competances',                      [AdminWebController::class, 'competances'])->name('competances');
        Route::post('/competances',                     [AdminWebController::class, 'storeCompetance'])->name('competances.store');
        Route::put('/competances/{id}',                 [AdminWebController::class, 'updateCompetance'])->name('competances.update');
        Route::delete('/competances/{id}',              [AdminWebController::class, 'supprimerCompetance'])->name('competances.supprimer');
    });
});
