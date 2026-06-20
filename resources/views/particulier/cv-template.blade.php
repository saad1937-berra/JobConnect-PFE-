<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $utilisateur->prenom }} {{ $utilisateur->nom }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #eceff3;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
        }
        .cv-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #111827;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        }
        .cv-toolbar a,
        .cv-toolbar button {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 0;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
        }
        .cv-toolbar a { background: #ffffff; color: #111827; }
        .cv-toolbar button { background: #facc15; color: #111827; }
        .cv-page {
            width: min(210mm, calc(100% - 2rem));
            min-height: 297mm;
            margin: 1.5rem auto;
            background: #ffffff;
            display: grid;
            grid-template-columns: 34% 66%;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
        }
        .cv-sidebar {
            background: #111827;
            color: #ffffff;
            padding: 2rem 1.6rem;
        }
        .cv-main { padding: 2rem 2.2rem; }
        .cv-avatar {
            width: 112px;
            height: 112px;
            border-radius: 50%;
            border: 4px solid #facc15;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #ffffff;
            color: #111827;
            font-size: 2.4rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
        }
        .cv-avatar img { width: 100%; height: 100%; object-fit: cover; }
        h1 {
            margin: 0;
            font-size: 2rem;
            line-height: 1.05;
            text-transform: uppercase;
            letter-spacing: 0;
        }
        .cv-title {
            margin: 0.7rem 0 1.5rem;
            color: #4b5563;
            font-weight: 700;
            font-size: 1rem;
        }
        .cv-sidebar h2,
        .cv-main h2 {
            margin: 1.6rem 0 0.75rem;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .cv-sidebar h2 { color: #facc15; }
        .cv-main h2 {
            color: #111827;
            border-bottom: 2px solid #facc15;
            padding-bottom: 0.35rem;
        }
        .cv-contact {
            display: flex;
            flex-direction: column;
            gap: 0.72rem;
            margin-top: 1rem;
        }
        .cv-contact div {
            display: flex;
            gap: 0.55rem;
            align-items: flex-start;
            line-height: 1.4;
            font-size: 0.86rem;
            word-break: break-word;
        }
        .cv-contact i { color: #facc15; width: 16px; padding-top: 2px; }
        .cv-muted { color: #6b7280; }
        .cv-sidebar .cv-muted { color: #d1d5db; }
        .cv-section p {
            margin: 0;
            line-height: 1.65;
            font-size: 0.95rem;
        }
        .cv-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-top: 0.5rem;
        }
        .cv-tag {
            display: inline-flex;
            flex-direction: column;
            gap: 0.12rem;
            border: 1px solid #d1d5db;
            border-left: 4px solid #facc15;
            border-radius: 6px;
            padding: 0.45rem 0.55rem;
            font-size: 0.84rem;
            font-weight: 700;
            background: #f9fafb;
        }
        .cv-tag span {
            color: #6b7280;
            font-size: 0.68rem;
            text-transform: uppercase;
        }
        .cv-item {
            padding: 0.85rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .cv-item:last-child { border-bottom: 0; }
        .cv-item strong { display: block; font-size: 0.98rem; }
        .cv-item small {
            display: block;
            margin-top: 0.2rem;
            color: #6b7280;
            font-weight: 700;
        }
        .cv-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 0.9rem;
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        @media (max-width: 760px) {
            .cv-page { grid-template-columns: 1fr; min-height: auto; }
            .cv-sidebar, .cv-main { padding: 1.4rem; }
        }
        @media print {
            @page { size: A4; margin: 0; }
            body { background: #ffffff; }
            .cv-toolbar { display: none; }
            .cv-page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="cv-toolbar">
        <a href="{{ route('particulier.profil') }}"><i class="fas fa-arrow-left"></i> Retour profil</a>
        <button type="button" onclick="window.print()"><i class="fas fa-download"></i> Telecharger en PDF</button>
    </div>

    <article class="cv-page">
        <aside class="cv-sidebar">
            <div class="cv-avatar">
                @if($particulier->photo)
                    <img src="{{ asset('storage/'.$particulier->photo) }}" alt="">
                @else
                    {{ strtoupper(substr($utilisateur->prenom, 0, 1) . substr($utilisateur->nom, 0, 1)) }}
                @endif
            </div>

            <h2>Contact</h2>
            <div class="cv-contact">
                <div><i class="fas fa-envelope"></i> <span>{{ $utilisateur->email }}</span></div>
                @if($particulier->tel)
                    <div><i class="fas fa-phone"></i> <span>{{ $particulier->tel }}</span></div>
                @endif
                @if($particulier->adresse)
                    <div><i class="fas fa-map-marker-alt"></i> <span>{{ $particulier->adresse }}</span></div>
                @endif
                @if($particulier->date_naissance)
                    <div><i class="fas fa-calendar"></i> <span>{{ $particulier->date_naissance->format('d/m/Y') }}</span></div>
                @endif
            </div>

            <h2>Formation</h2>
            <p class="cv-muted">{{ $particulier->niveau_etude ?? 'Niveau non renseigne' }}</p>

            <h2>Informations</h2>
            <p class="cv-muted">Profil cree sur JobConnect.</p>
        </aside>

        <main class="cv-main">
            <header>
                <h1>{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</h1>
                <p class="cv-title">{{ $particulier->niveau_etude ? 'Candidat '.$particulier->niveau_etude : 'Candidat JobConnect' }}</p>
            </header>

            <section class="cv-section">
                <h2>Profil</h2>
                <p>{{ $particulier->bio ?: 'Completez votre bio dans votre profil pour afficher un resume professionnel ici.' }}</p>
            </section>

            <section class="cv-section">
                <h2>Competences</h2>
                @if($particulier->competances->isNotEmpty())
                    <div class="cv-tags">
                        @foreach($particulier->competances as $competance)
                            <div class="cv-tag">
                                {{ $competance->nom }}
                                <span>{{ $competance->pivot->niveau ?? 'Niveau non renseigne' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="cv-empty">Ajoutez des competences dans votre profil pour enrichir cette section.</div>
                @endif
            </section>

            <section class="cv-section">
                <h2>Experiences et opportunites</h2>
                @if($candidaturesAcceptees->isNotEmpty())
                    @foreach($candidaturesAcceptees as $candidature)
                        <div class="cv-item">
                            <strong>{{ $candidature->offre?->titre }}</strong>
                            <small>{{ $candidature->offre?->entreprise?->nom }} - candidature acceptee</small>
                        </div>
                    @endforeach
                @else
                    <div class="cv-empty">Les candidatures acceptees pourront apparaitre ici comme opportunites professionnelles.</div>
                @endif
            </section>

            <section class="cv-section">
                <h2>Centres d'interet professionnel</h2>
                <p class="cv-muted">Recrutement, evolution professionnelle et opportunites adaptees au profil.</p>
            </section>
        </main>
    </article>
</body>
</html>
