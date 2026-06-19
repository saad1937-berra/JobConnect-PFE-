# JobConnect

JobConnect est une application web de recrutement developpee avec Laravel. Elle met en relation des candidats, des entreprises et un administrateur autour des offres d'emploi, des candidatures, du matching, des suggestions et d'une messagerie controlee.

Le projet contient aussi une API protegee par Laravel Sanctum pour les principaux parcours candidat, entreprise et admin.

## Technologies

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- MySQL / MariaDB avec XAMPP
- Blade, CSS custom, Vite
- PHPUnit
- Poppler `pdftotext` pour l'extraction du texte des CV PDF

## Roles

L'application gere quatre etats de compte :

- `particulier` : candidat.
- `entreprise` : recruteur ou entreprise.
- `admin` : administrateur de la plateforme.
- `bloque` : compte suspendu par l'admin.

Chaque role a son propre espace, ses permissions et ses vues dediees.

## Fonctionnalites principales

### Espace public

- Accueil personnalise selon le role connecte.
- Liste des offres d'emploi actives.
- Recherche et filtres par titre, categorie, type de contrat et ville.
- Detail d'une offre.
- Page de confidentialite accessible publiquement.
- Inscription et connexion.
- Mot de passe oublie avec lien email securise.

### Candidat

- Gestion du profil : bio, telephone, adresse, date de naissance, niveau d'etude.
- Upload photo de profil.
- Upload de CV PDF, DOC ou DOCX.
- Stockage prive des CV dans `storage/app/private`.
- Lecture automatique du texte des CV pour ameliorer le matching.
- Gestion des competences.
- Candidature aux offres.
- Suivi des candidatures et de leurs statuts.
- Suggestions d'offres.
- Matching avec score de compatibilite.
- Messagerie avec les entreprises uniquement si les regles metier l'autorisent.

### Entreprise

- Dashboard avec statistiques.
- Gestion du profil entreprise et du logo.
- Creation, modification et suppression d'offres.
- Gestion des candidatures recues.
- Changement du statut d'une candidature : `en_attente`, `en_cours`, `acceptee`, `refusee`.
- Telechargement securise du CV d'un candidat uniquement depuis une candidature appartenant a l'entreprise.
- Suggestions de candidats pour une offre.
- Matching des candidats avec score.
- Contact direct des candidats.
- Signalement d'un candidat a l'admin en cas d'abus ou de harcelement.

### Admin

- Dashboard de statistiques globales.
- Gestion des entreprises.
- Validation des entreprises.
- Gestion des utilisateurs.
- Blocage d'un utilisateur.
- Revocation automatique des tokens API lorsqu'un utilisateur est bloque.
- Gestion des offres.
- Gestion des categories.
- Gestion des competences.
- Lecture de toutes les conversations.
- Envoi d'avertissements aux candidats et aux entreprises.
- Module de signalements avec statut `nouveau`, `en_cours`, `traite` ou `rejete`.

## Systeme de matching

Le matching compare les profils candidats avec les offres selon plusieurs informations :

- competences du candidat ;
- competences demandees par l'offre ;
- niveau d'etude ;
- localisation ;
- contenu du CV lorsque celui-ci est lisible.

Si un CV contient du texte exploitable, le systeme l'utilise dans le calcul. Si aucun CV lisible n'est disponible, l'application utilise la methode basee sur le profil et les competences deja renseignees.

## Lecture des CV

Le service `App\Services\CvTextExtractor` extrait le texte des CV :

- `.docx` via `ZipArchive` ;
- `.pdf` via `pdftotext` quand Poppler est installe ;
- fallback basique pour certains PDF simples.

Commande utile pour reextraire le texte des CV existants :

```bash
php artisan cv:extract-text --force
```

Sur Windows avec XAMPP, Poppler peut etre installe dans :

```text
C:\poppler\Library\bin\pdftotext.exe
```

## Messagerie

La messagerie est volontairement limitee pour eviter les abus.

Regles principales :

- Une entreprise peut contacter un candidat.
- Un candidat ne peut pas contacter une entreprise directement.
- Un candidat peut repondre si l'entreprise l'a contacte en premier.
- Un candidat peut contacter une entreprise si sa candidature a ete acceptee.
- Une entreprise peut contacter l'admin.
- L'admin peut avertir une entreprise ou un candidat.
- L'admin peut consulter toutes les conversations.
- L'entreprise peut signaler une conversation avec un candidat a l'admin.
- Chaque signalement est stocke dans une table dediee et consultable depuis l'espace admin.
- Le formulaire de message affiche des conseils pour rester professionnel.
- Les nouveaux messages generent une notification pour le destinataire.

## Securite

Mesures deja mises en place :

- Hash des mots de passe avec Laravel `Hash`.
- CSRF sur les formulaires web.
- Middleware `auth`, `role` et `not_blocked`.
- Blocage des comptes suspendus sur le web et l'API.
- Suppression des tokens Sanctum quand un compte est bloque.
- Rate limiting sur login, inscription et reset password.
- Reset password API direct desactive.
- Tokens de reset password web stockes hashes en base.
- CV stockes en prive, non exposes directement dans `/storage`.
- Telechargement des CV via routes autorisees.
- Headers HTTP de securite :
  - `X-Frame-Options`
  - `X-Content-Type-Options`
  - `Referrer-Policy`
  - `Permissions-Policy`

Avant production, verifier aussi :

- `APP_DEBUG=false`
- HTTPS actif
- mots de passe admin/seeders changes
- base de donnees avec utilisateur dedie, pas `root` sans mot de passe
- configuration email reelle
- sauvegardes de base de donnees

## Installation locale avec XAMPP

### 1. Cloner ou placer le projet

Exemple :

```text
C:\xampp\htdocs\PFE
```

### 2. Installer les dependances PHP

```bash
composer install
```

### 3. Installer les dependances front

```bash
npm install
```

### 4. Creer le fichier `.env`

```bash
copy .env.example .env
```

Adapter au minimum :

```env
APP_NAME=JobConnect
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pfe
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generer la cle Laravel

```bash
php artisan key:generate
```

### 6. Lancer MySQL avec XAMPP

Creer la base :

```sql
CREATE DATABASE pfe;
```

### 7. Lancer les migrations

```bash
php artisan migrate
```

### 8. Remplir la base avec les seeders

Attention : le seeder principal vide les tables principales avant de recreer les donnees.

```bash
php artisan db:seed
```

Les seeders creent notamment :

- 1 admin ;
- 10 entreprises ;
- 100 candidats ;
- 1000 offres ;
- categories et competences ;
- relations de competences/offres/candidats.

### 9. Lancer le serveur

```bash
php artisan serve --port=8000
```

Adresse :

```text
http://localhost:8000
```

## Comptes de test

Admin :

```text
Email: admin@jobconnect.ma
Mot de passe: admin1234
```

Candidats generes :

```text
Email: candidat001@jobconnect.test
Mot de passe: password123
```

Entreprises generees :

Les entreprises sont creees par `EntrepriseSeeder`. Consulter la base ou le seeder pour les emails exacts.

Important : changer ces mots de passe avant toute mise en production.

## Commandes utiles

Lancer les tests :

```bash
php artisan test
```

Vider le cache des vues :

```bash
php artisan view:clear
```

Vider le cache de configuration :

```bash
php artisan config:clear
```

Relancer migrations + seeders :

```bash
php artisan migrate:fresh --seed
```

Compiler le front :

```bash
npm run build
```

Lancer Vite en developpement :

```bash
npm run dev
```

## API

Routes publiques :

- `POST /api/register`
- `POST /api/login`
- `POST /api/reset-pass` : desactive volontairement pour securite
- `GET /api/offres`
- `GET /api/offres/{id}`

Routes protegees par Sanctum :

- `POST /api/logout`
- notifications
- profil candidat
- CV candidat
- candidatures candidat
- offres entreprise
- candidatures entreprise
- statistiques admin
- gestion admin

Authentification API :

```http
Authorization: Bearer <token>
```

## Structure importante

```text
app/Http/Controllers/web       Controleurs web Blade
app/Http/Controllers           Controleurs API
app/Models                     Modeles Eloquent
app/Services                   Services matching, suggestions, CV
resources/views                Vues Blade
public/css                     Styles de l'application
database/migrations            Structure de la base
database/seeders               Donnees de test
routes/web.php                 Routes web
routes/api.php                 Routes API
routes/console.php             Commandes artisan custom
tests/Feature                  Tests fonctionnels
```

## Tests actuels

Les tests couvrent notamment :

- disponibilite de l'accueil ;
- authentification web et API ;
- restrictions d'acces par role ;
- pages publiques d'offres et filtres ;
- parcours candidat : profil, competences, candidature, CV, suggestions et matching ;
- parcours entreprise : offres, candidatures, CV, matching et suggestions ;
- parcours admin : dashboard, categories, competences, signalements et blocage ;
- autorisations de messagerie ;
- blocage des conversations interdites ;
- notifications ;
- matching et suggestions ;
- reset API desactive ;
- login API interdit pour compte bloque ;
- rejet des tokens API de comptes bloques ;
- stockage prive des CV ;
- interdiction de telecharger le CV d'un autre candidat.

Commande :

```bash
php artisan test
```

## Notes de production

Avant de publier le projet :

- mettre `APP_ENV=production` ;
- mettre `APP_DEBUG=false` ;
- configurer HTTPS ;
- configurer un vrai serveur mail ;
- utiliser un utilisateur MySQL dedie ;
- changer tous les comptes de test ;
- executer `php artisan optimize` ;
- verifier les permissions du dossier `storage` ;
- sauvegarder regulierement la base de donnees et les fichiers prives.
