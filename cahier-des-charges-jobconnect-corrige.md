# Cahier des charges - JobConnect

## Informations generales

**Nom du projet :** JobConnect

**Sujet :** Plateforme web de recrutement pour candidats et entreprises

**Candidat :** Saad Berra

**Etablissement :** SupTechnology

**Filiere :** Developpement Web et Mobile

**Annee universitaire :** 2025-2026

**Encadrant :** M. Ahmed Zellou

**Objectif du document :** definir le besoin, le perimetre, les acteurs, les fonctionnalites attendues, les contraintes et les criteres de validation du projet JobConnect.

## 1. Introduction

JobConnect est une plateforme web de recrutement destinee a faciliter la relation entre les candidats a la recherche d'opportunites professionnelles et les entreprises souhaitant publier des offres et suivre les candidatures.

Le document presente le cahier des charges fonctionnel du projet. Il decrit les besoins a satisfaire, les roles des utilisateurs, les regles de gestion principales, les contraintes non fonctionnelles et les criteres permettant de valider la solution.

## 2. Contexte du projet

Le recrutement en ligne est devenu un moyen essentiel pour diffuser les offres d'emploi, recevoir les candidatures et gerer les echanges entre recruteurs et candidats. Cependant, les plateformes classiques peuvent parfois manquer de controle sur la fiabilite des comptes, la pertinence des candidatures et la securite des donnees personnelles.

JobConnect propose une solution organisee autour de trois espaces : candidat, entreprise et administrateur. Elle permet de centraliser les profils, les offres, les candidatures, les CV, les suggestions et la messagerie dans un environnement controle.

## 3. Etude de l'existant

Plusieurs plateformes de recrutement existent deja, telles que LinkedIn, Indeed, ReKrute, Emploi.ma et ANAPEC. Elles proposent des fonctionnalites importantes comme la publication d'offres, le depot de CV, la recherche de profils et les alertes.

Ces solutions restent souvent generalistes ou orientees vers un usage massif. Dans le cadre de ce projet, JobConnect se distingue par une approche encadree : validation des entreprises, controle des acces, protection des CV, messagerie limitee aux relations autorisees et suivi administratif des signalements.

| Critere | Plateformes existantes | JobConnect |
| --- | --- | --- |
| Publication d'offres | Disponible | Disponible pour les entreprises validees |
| Espace candidat | Disponible | Profil complet, CV, competences et suivi |
| Espace entreprise | Disponible | Publication, candidatures et suggestions |
| Validation entreprise | Variable | Validation obligatoire par administrateur |
| Matching | Souvent partiel | Score selon profil, competences et offre |
| Moderation | Variable | Blocage, signalements et suivi admin |

## 4. Problematique

Le projet repond aux questions suivantes :

- comment aider un candidat a trouver des offres adaptees a son profil ?
- comment aider une entreprise a identifier rapidement les candidatures pertinentes ?
- comment proteger les CV et les donnees personnelles ?
- comment limiter les abus dans les echanges entre candidats et recruteurs ?
- comment permettre a l'administrateur de controler les comptes, les offres et les signalements ?

## 5. Objectifs du projet

### 5.1 Objectif principal

Concevoir et realiser une application web de recrutement permettant une mise en relation securisee, organisee et pertinente entre candidats et entreprises.

### 5.2 Objectifs specifiques

- permettre l'inscription et la connexion des utilisateurs ;
- gerer trois espaces distincts : candidat, entreprise et administrateur ;
- permettre aux entreprises validees de publier et gerer leurs offres ;
- permettre aux candidats de completer leur profil, gerer leur CV et postuler ;
- proposer un systeme de matching entre offres et candidats ;
- assurer le suivi des candidatures et des notifications ;
- encadrer la messagerie pour eviter les abus ;
- fournir un espace d'administration pour la moderation ;
- garantir la securite des donnees sensibles.

## 6. Perimetre du projet

### 6.1 Fonctionnalites incluses

- gestion des comptes utilisateurs ;
- verification des comptes et controle des roles ;
- gestion des profils candidats ;
- gestion des profils entreprises ;
- publication et gestion des offres ;
- depot, consultation et generation de CV ;
- gestion des candidatures ;
- matching et suggestions ;
- notifications ;
- messagerie controlee ;
- signalements ;
- tableaux de bord ;
- moderation administrative.

### 6.2 Fonctionnalites exclues

- paiement en ligne ;
- application mobile native ;
- visioconference ;
- signature electronique de contrat ;
- integration directe avec des reseaux sociaux externes ;
- systeme d'intelligence artificielle externe.

## 7. Acteurs du systeme

### 7.1 Visiteur

Le visiteur peut consulter les offres publiques, filtrer les offres, voir le detail d'une offre, creer un compte et se connecter.

### 7.2 Candidat

Le candidat peut creer un compte, completer son profil, ajouter ses competences, deposer ou generer un CV, consulter les offres, postuler, suivre ses candidatures, recevoir des notifications et communiquer lorsque les regles de messagerie l'autorisent.

### 7.3 Entreprise

L'entreprise peut creer un compte, attendre la validation administrative, completer son profil, publier des offres apres validation, gerer ses offres, consulter les candidatures, changer les statuts, consulter les suggestions et communiquer avec les candidats autorises.

### 7.4 Administrateur

L'administrateur gere les utilisateurs, valide ou refuse les entreprises, bloque les comptes si necessaire, gere les categories et competences, consulte les offres, traite les signalements et supervise l'activite globale.

## 8. Besoins fonctionnels

### 8.1 Gestion des utilisateurs

| Besoin | Description | Priorite |
| --- | --- | --- |
| Inscription | Creation d'un compte candidat ou entreprise | Haute |
| Connexion | Acces securise a l'espace utilisateur | Haute |
| Verification | Confirmation de l'adresse email | Haute |
| Roles | Separation des droits candidat, entreprise et admin | Haute |
| Blocage | Suspension possible d'un compte par l'admin | Haute |

### 8.2 Espace candidat

| Besoin | Description | Priorite |
| --- | --- | --- |
| Profil | Informations personnelles et professionnelles | Haute |
| Competences | Ajout des competences avec niveau | Haute |
| CV | Depot et generation d'un CV | Haute |
| Candidatures | Postuler et suivre les statuts | Haute |
| Suggestions | Recevoir des offres adaptees | Moyenne |
| Messagerie | Echanger selon les regles autorisees | Moyenne |

### 8.3 Espace entreprise

| Besoin | Description | Priorite |
| --- | --- | --- |
| Profil entreprise | Informations de presentation de l'entreprise | Haute |
| Offres | Creation, modification et suppression d'offres | Haute |
| Candidatures | Consultation et traitement des candidatures | Haute |
| CV candidats | Consultation securisee des CV recus | Haute |
| Suggestions | Liste de candidats pertinents par offre | Moyenne |
| Signalement | Declaration d'un abus a l'administrateur | Moyenne |

### 8.4 Espace administrateur

| Besoin | Description | Priorite |
| --- | --- | --- |
| Dashboard | Statistiques globales de la plateforme | Haute |
| Utilisateurs | Consultation, blocage et deblocage | Haute |
| Entreprises | Validation ou refus des comptes entreprises | Haute |
| Referentiels | Gestion des categories et competences | Moyenne |
| Signalements | Traitement des signalements | Moyenne |
| Moderation | Supervision des contenus et conversations | Moyenne |

## 9. Regles de gestion principales

- une entreprise doit etre validee avant de publier des offres ;
- un candidat ne peut postuler qu'une seule fois a la meme offre ;
- une candidature possede un statut de suivi : en attente, en cours, acceptee ou refusee ;
- les CV doivent etre proteges et accessibles uniquement aux utilisateurs autorises ;
- une entreprise peut contacter un candidat dans le cadre d'une candidature ;
- un candidat peut repondre ou contacter l'entreprise uniquement lorsque la relation est autorisee ;
- l'administrateur peut bloquer un utilisateur et traiter les signalements ;
- les offres d'une entreprise refusee, bloquee ou non validee ne doivent pas etre visibles publiquement.

## 10. Matching et suggestions

Le matching permet d'estimer la compatibilite entre une offre et un candidat. Il repose sur les informations du profil, les competences, la localisation, le niveau d'etude et les informations disponibles dans le CV lorsque celles-ci sont exploitables.

L'objectif n'est pas de remplacer le recruteur, mais de l'aider a prioriser les profils et d'aider le candidat a identifier les offres les plus pertinentes.

## 11. Besoins non fonctionnels

### 11.1 Securite

- protection des mots de passe ;
- controle des acces selon les roles ;
- protection contre les actions non autorisees ;
- stockage prive des CV ;
- validation des donnees saisies ;
- limitation des tentatives sensibles ;
- moderation des messages et signalements.

### 11.2 Ergonomie

- interface claire pour chaque type d'utilisateur ;
- navigation simple entre les principales fonctionnalites ;
- messages d'erreur et de confirmation comprehensibles ;
- tableaux de bord lisibles ;
- formulaires organises et faciles a remplir.

### 11.3 Performance et fiabilite

- listes paginees lorsque les donnees sont nombreuses ;
- temps de reponse acceptable pour les operations courantes ;
- conservation correcte des donnees ;
- disponibilite de donnees de demonstration pour tester les parcours.

### 11.4 Maintenabilite

- organisation claire du code ;
- separation des responsabilites ;
- facilite d'evolution des fonctionnalites ;
- validation du comportement par des tests.

## 12. Contraintes du projet

- le projet doit etre une application web utilisable en local pour la demonstration ;
- la base de donnees doit stocker les comptes, profils, offres, candidatures, messages et signalements ;
- les documents sensibles, notamment les CV, ne doivent pas etre exposes publiquement ;
- la solution doit etre compatible avec le contexte de formation en developpement web et mobile ;
- les fonctionnalites doivent rester demonstrables pendant la soutenance.

## 13. Criteres d'acceptation

Le projet sera considere valide si :

- un visiteur peut consulter les offres publiques ;
- un candidat peut creer un compte, completer son profil, ajouter un CV et postuler ;
- une entreprise validee peut publier une offre et gerer les candidatures ;
- une entreprise non validee ne peut pas publier d'offre ;
- le systeme propose des suggestions ou scores de matching ;
- les CV ne sont accessibles qu'aux utilisateurs autorises ;
- la messagerie respecte les regles de controle ;
- l'administrateur peut gerer les utilisateurs, entreprises, referentiels et signalements ;
- les principaux parcours peuvent etre presentes sans erreur lors de la demonstration.

## 14. Planning previsionnel

| Phase | Contenu | Etat |
| --- | --- | --- |
| Phase 1 | Analyse du besoin et conception | Terminee |
| Phase 2 | Mise en place du projet et authentification | Terminee |
| Phase 3 | Espace candidat et gestion du CV | Terminee |
| Phase 4 | Espace entreprise et gestion des offres | Terminee |
| Phase 5 | Espace administrateur et moderation | Terminee |
| Phase 6 | Matching, suggestions et notifications | Terminee |
| Phase 7 | Tests, corrections et documentation | En cours |
| Phase 8 | Preparation de la soutenance et finalisation | En cours |

## 15. Evolutions possibles

- amelioration du score de matching ;
- recherche avancee multicritere ;
- calendrier d'entretien ;
- historique detaille des actions administratives ;
- export PDF avance du CV ;
- application mobile ;
- statistiques plus detaillees pour les entreprises.

## 16. Conclusion

JobConnect vise a fournir une plateforme de recrutement complete, securisee et adaptee aux besoins des candidats, des entreprises et de l'administrateur. Le projet met l'accent sur la qualite des parcours utilisateurs, la protection des donnees, la moderation et l'aide a la decision grace au matching.

Ce cahier des charges servira de reference pour verifier que la solution realisee repond bien aux besoins fonctionnels et non fonctionnels definis.
