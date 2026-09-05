# Diagrammes JobConnect

## 1. Diagramme de cas d'utilisation

```mermaid
flowchart LR
    Visiteur([Visiteur])
    Candidat([Candidat])
    Entreprise([Entreprise])
    Admin([Administrateur])

    subgraph Systeme["Systeme JobConnect"]
        UC1["Consulter les offres"]
        UC2["Creer un compte"]
        UC3["Se connecter"]
        UC4["Completer le profil candidat"]
        UC5["Ajouter competences"]
        UC6["Deposer ou generer CV"]
        UC7["Postuler a une offre"]
        UC8["Suivre candidatures"]
        UC9["Consulter suggestions et matching"]
        UC10["Completer profil entreprise"]
        UC11["Publier et gerer offres"]
        UC12["Consulter candidatures"]
        UC13["Changer statut candidature"]
        UC14["Telecharger CV candidat"]
        UC15["Echanger par messagerie controlee"]
        UC16["Valider ou refuser entreprises"]
        UC17["Gerer utilisateurs"]
        UC18["Gerer categories et competences"]
        UC19["Traiter signalements"]
        UC20["Consulter tableaux de bord"]
    end

    Visiteur --> UC1
    Visiteur --> UC2
    Visiteur --> UC3
    Candidat --> UC4
    Candidat --> UC5
    Candidat --> UC6
    Candidat --> UC7
    Candidat --> UC8
    Candidat --> UC9
    Candidat --> UC15
    Entreprise --> UC10
    Entreprise --> UC11
    Entreprise --> UC12
    Entreprise --> UC13
    Entreprise --> UC14
    Entreprise --> UC9
    Entreprise --> UC15
    Entreprise --> UC20
    Admin --> UC16
    Admin --> UC17
    Admin --> UC18
    Admin --> UC19
    Admin --> UC20
```

## 2. Diagramme de classes

```mermaid
classDiagram
    class Utilisateur {
        +int id
        +string email
        +string pass
        +string nom
        +string prenom
        +string role
        +timestamp email_verified_at
        +timestamp date_inscription
    }
    class Particulier {
        +int id
        +int utilisateur_id
        +string bio
        +string tel
        +string adresse
        +date date_naissance
        +string niveau_etude
        +string photo
        +string cv_titre
    }
    class Entreprise {
        +int id
        +int utilisateur_id
        +string nom
        +string secteur
        +string description
        +string statut_validation
    }
    class Offre {
        +int id
        +int entreprise_id
        +int categorie_id
        +string titre
        +string contrat
        +string localisation
        +string statut
    }
    class Candidature {
        +int id
        +int particulier_id
        +int offre_id
        +string statut
        +text commentaire
    }
    class Cv
    class Competance
    class Categorie
    class Notification
    class Conversation
    class Message
    class Report

    Utilisateur "1" --> "0..1" Particulier
    Utilisateur "1" --> "0..1" Entreprise
    Utilisateur "1" --> "0..*" Notification
    Entreprise "1" --> "0..*" Offre
    Categorie "1" --> "0..*" Offre
    Particulier "1" --> "0..*" Cv
    Particulier "1" --> "0..*" Candidature
    Offre "1" --> "0..*" Candidature
    Particulier "0..*" --> "0..*" Competance
    Offre "0..*" --> "0..*" Competance
    Conversation "1" --> "0..*" Message
    Conversation "1" --> "0..*" Report
```

## 3. Diagramme entite-association

Voir `03-entite-association.mmd` pour la version complete.

## 4. Diagramme de sequence - candidature

```mermaid
sequenceDiagram
    actor Candidat
    participant App as Application JobConnect
    participant Profil as Profil candidat
    participant Offre as Module offres
    participant Match as MatchingService
    participant Cand as Module candidatures
    participant Notif as Notifications
    actor Entreprise

    Candidat->>App: Se connecter
    App-->>Candidat: Acces espace candidat
    Candidat->>Profil: Completer profil, competences et CV
    Profil-->>App: Profil mis a jour
    Candidat->>Offre: Consulter les offres actives
    Offre-->>Candidat: Liste des offres
    Candidat->>Offre: Ouvrir une offre
    Offre->>Match: Calculer score candidat-offre
    Match-->>Offre: Score et details
    Offre-->>Candidat: Detail offre avec score
    Candidat->>Cand: Postuler
    Cand->>Cand: Verifier candidature unique
    Cand->>Cand: Creer candidature en attente
    Cand->>Notif: Notifier entreprise
    Notif-->>Entreprise: Nouvelle candidature recue
    Cand-->>Candidat: Confirmation de candidature
```

## 5. Diagramme d'activite - candidature

```mermaid
flowchart TD
    A([Debut]) --> B{Utilisateur connecte ?}
    B -- Non --> C[Se connecter ou creer un compte]
    C --> D{Email verifie ?}
    B -- Oui --> D
    D -- Non --> E[Demander verification email]
    E --> Z([Fin])
    D -- Oui --> F[Completer profil candidat]
    F --> G[Ajouter competences]
    G --> H[Deposer ou generer CV]
    H --> I[Consulter les offres]
    I --> J[Selectionner une offre]
    J --> K[Afficher score de matching]
    K --> L{Deja postule ?}
    L -- Oui --> M[Afficher message candidature existante]
    M --> Z
    L -- Non --> N[Envoyer candidature]
    N --> O[Creer candidature en attente]
    O --> P[Notifier entreprise]
    P --> Q[Afficher confirmation]
    Q --> Z([Fin])
```

## 6. Diagramme d'activite - validation entreprise

```mermaid
flowchart TD
    A([Debut]) --> B[Entreprise cree un compte]
    B --> C[Verification email]
    C --> D{Email verifie ?}
    D -- Non --> E[Compte en attente de verification]
    E --> Z([Fin])
    D -- Oui --> F[Compte en attente de validation admin]
    F --> G[Administrateur consulte entreprises en attente]
    G --> H{Decision admin}
    H -- Refuser --> I[Statut refusee]
    I --> J[Entreprise ne peut pas publier]
    J --> Z
    H -- Valider --> K[Statut validee]
    K --> L[Entreprise complete son profil]
    L --> M[Entreprise publie une offre]
    M --> N[Offre visible si active]
    N --> Z([Fin])
```

