@extends('layouts.app')

@section('title', 'Confidentialite')

@push('styles')
<style>
    .privacy-page { max-width: 980px; margin: 0 auto; padding: 2.5rem 1rem; }
    .privacy-hero { margin-bottom: 2rem; border-bottom: 1px solid var(--border, #e5e7eb); padding-bottom: 1.5rem; }
    .privacy-hero h1 { font-family: var(--font-head, sans-serif); font-size: 2rem; font-weight: 800; margin-bottom: .5rem; }
    .privacy-hero p, .privacy-section p, .privacy-section li { color: var(--muted, #555); line-height: 1.7; }
    .privacy-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .privacy-section { background: #fff; border: 1px solid var(--border, #e5e7eb); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem; }
    .privacy-section h2 { font-size: 1.05rem; font-weight: 800; margin-bottom: .75rem; }
    .privacy-section ul { padding-left: 1.2rem; margin: 0; }
    .privacy-note { background: #fff7ed; border: 1px solid #fed7aa; color: #7c2d12; border-radius: 8px; padding: 1rem; margin-top: 1rem; line-height: 1.6; }
    @media (max-width: 800px) { .privacy-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="privacy-page">
    <div class="privacy-hero">
        <h1>Politique de confidentialite</h1>
        <p>Cette page explique comment JobConnect collecte, utilise et protege les donnees des candidats, entreprises et administrateurs.</p>
    </div>

    <div class="privacy-grid">
        <section class="privacy-section">
            <h2>Donnees collectees</h2>
            <ul>
                <li>Compte : nom, prenom, email et role.</li>
                <li>Profil candidat : bio, telephone, adresse, niveau d'etude, competences et CV.</li>
                <li>Profil entreprise : nom, secteur, description, adresse, site web et logo.</li>
                <li>Candidatures, statuts, commentaires et notifications.</li>
                <li>Messages et signalements utilises pour la moderation.</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>Utilisation</h2>
            <ul>
                <li>Permettre aux candidats de postuler.</li>
                <li>Permettre aux entreprises de gerer offres et candidatures.</li>
                <li>Calculer suggestions et scores de matching.</li>
                <li>Assurer une messagerie professionnelle et moderee.</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>Protection des CV</h2>
            <p>Les CV sont stockes dans un espace prive du serveur. Ils ne sont pas exposes directement dans le dossier public. Le telechargement passe par des routes autorisees.</p>
        </section>

        <section class="privacy-section">
            <h2>Messagerie</h2>
            <p>Les conversations sont limitees par des regles metier. Une entreprise peut signaler une conversation abusive a l'admin, et l'admin peut intervenir.</p>
        </section>

        <section class="privacy-section">
            <h2>Securite</h2>
            <ul>
                <li>Mots de passe hashes.</li>
                <li>Comptes bloques rejetes sur web et API.</li>
                <li>Tokens API revoques lors du blocage.</li>
                <li>Protection CSRF et limitation des tentatives sensibles.</li>
            </ul>
        </section>

        <section class="privacy-section">
            <h2>Droits des utilisateurs</h2>
            <p>Un utilisateur peut demander la correction, la suppression ou la limitation d'utilisation de ses donnees en contactant l'administrateur.</p>
        </section>
    </div>

    <div class="privacy-note">
        Avant une mise en production reelle, completer cette page avec les informations legales et les coordonnees officielles du responsable du traitement.
    </div>
</div>
@endsection
