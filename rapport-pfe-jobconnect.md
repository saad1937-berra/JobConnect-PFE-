# Rapport de Projet de Fin d'Etudes

# JobConnect

## Plateforme web de recrutement pour candidats et entreprises

**Candidat :** Saad Berra  
**Etablissement :** SupTechnology  
**Filiere :** Developpement Web et Mobile  
**Encadrant :** M. Ahmed Zellou  
**Annee universitaire :** 2025-2026

---

# Remerciements

Je tiens a exprimer ma profonde gratitude a toutes les personnes qui ont contribue, de pres ou de loin, a la realisation de ce projet de fin d'etudes.

Je remercie tout particulierement mon encadrant, M. Ahmed Zellou, pour son accompagnement, ses remarques, ses conseils et son suivi tout au long de ce travail.

Je remercie egalement l'ensemble du corps pedagogique de SupTechnology pour la qualite de la formation dispensee et pour les connaissances acquises durant mon parcours.

Enfin, j'adresse mes remerciements a ma famille et a mes proches pour leur soutien, leur patience et leurs encouragements.

---

# Resume

Ce rapport presente la conception et la realisation de JobConnect, une plateforme web de recrutement destinee a faciliter la mise en relation entre candidats et entreprises.

Le projet propose trois espaces principaux : un espace candidat, un espace entreprise et un espace administrateur. Les candidats peuvent gerer leur profil, deposer ou generer un CV, consulter les offres, postuler et suivre leurs candidatures. Les entreprises peuvent publier des offres, consulter les candidatures, acceder aux CV autorises et suivre les profils pertinents. L'administrateur assure la moderation, la validation des entreprises, la gestion des utilisateurs et le traitement des signalements.

JobConnect integre egalement un systeme de matching permettant d'estimer la compatibilite entre un candidat et une offre selon plusieurs criteres. Le projet met l'accent sur la securite, la protection des donnees personnelles, la moderation des echanges et la simplicite d'utilisation.

**Mots-cles :** recrutement, Laravel, application web, candidat, entreprise, matching, CV, administration.

---

# Abstract

This report presents the design and development of JobConnect, a web-based recruitment platform intended to facilitate the connection between candidates and companies.

The project provides three main areas: a candidate area, a company area and an administrator area. Candidates can manage their profile, upload or generate a resume, browse job offers, apply and track their applications. Companies can publish job offers, review applications, access authorized resumes and identify relevant profiles. The administrator manages moderation, company validation, users and reports.

JobConnect also includes a matching system that estimates the compatibility between a candidate and a job offer based on several criteria. The project focuses on security, personal data protection, controlled communication and ease of use.

**Keywords:** recruitment, Laravel, web application, candidate, company, matching, resume, administration.

---

# Liste des figures

A completer apres insertion des captures d'ecran et diagrammes.

# Liste des tableaux

A completer apres finalisation du rapport.

# Liste des abreviations

| Abreviation | Signification |
| --- | --- |
| PFE | Projet de Fin d'Etudes |
| CV | Curriculum Vitae |
| CRUD | Create, Read, Update, Delete |
| MVC | Model, View, Controller |
| API | Application Programming Interface |
| UI | User Interface |
| UX | User Experience |

---

# Introduction generale

## Contexte general

Le recrutement occupe une place essentielle dans le fonctionnement des entreprises et dans l'insertion professionnelle des candidats. Il permet aux organisations de trouver les competences necessaires a leur developpement, tout en donnant aux chercheurs d'emploi la possibilite d'acceder a des opportunites adaptees a leurs profils. Pendant longtemps, ce processus s'est appuye sur des methodes classiques telles que les annonces papier, les candidatures spontanees, les entretiens directs ou les recommandations personnelles.

Avec la transformation digitale, les pratiques de recrutement ont fortement evolue. Les plateformes web, les reseaux sociaux professionnels et les solutions de gestion des candidatures sont devenus des outils incontournables pour publier des offres, recevoir des dossiers, filtrer les profils et faciliter la communication entre recruteurs et candidats. Cette evolution permet un gain de temps important et une meilleure visibilite des offres d'emploi, aussi bien pour les entreprises que pour les candidats.

Cependant, cette digitalisation pose egalement plusieurs defis. Les candidats peuvent se retrouver face a un grand nombre d'offres peu adaptees a leur profil. Les entreprises, de leur cote, peuvent recevoir un volume important de candidatures, parfois difficiles a analyser manuellement. A cela s'ajoutent des enjeux lies a la securite des donnees personnelles, notamment les CV, les informations de contact, les messages et les historiques de candidatures.

Dans ce contexte, il devient necessaire de proposer des plateformes plus organisees, plus securisees et plus intelligentes. Une solution moderne de recrutement ne doit pas seulement permettre la publication d'offres et le depot de candidatures. Elle doit aussi accompagner les utilisateurs, controler les acces, faciliter la moderation, proteger les documents sensibles et aider a identifier les correspondances pertinentes entre une offre et un candidat.

C'est dans cette optique que s'inscrit le projet JobConnect. Il s'agit d'une application web de recrutement concue pour centraliser les interactions entre candidats, entreprises et administrateur. Le projet vise a proposer une solution claire, securisee et adaptee au contexte d'un projet de fin d'etudes en developpement web et mobile.

## Presentation synthetique du projet

JobConnect est une plateforme web permettant de mettre en relation les candidats a la recherche d'un emploi et les entreprises souhaitant recruter. L'application est organisee autour de trois espaces principaux : l'espace candidat, l'espace entreprise et l'espace administrateur.

L'espace candidat permet a l'utilisateur de creer un profil professionnel, de renseigner ses informations personnelles, d'ajouter ses competences, de deposer un CV ou de generer un CV a partir des informations saisies. Le candidat peut egalement consulter les offres disponibles, postuler, suivre l'etat de ses candidatures, recevoir des notifications et acceder a des suggestions d'offres.

L'espace entreprise permet aux recruteurs de creer un profil entreprise, de publier des offres d'emploi apres validation administrative, de consulter les candidatures recues, de telecharger les CV autorises, de changer le statut d'une candidature et d'echanger avec les candidats lorsque les regles metier le permettent.

L'espace administrateur joue un role central dans la moderation et la fiabilite de la plateforme. Il permet de valider ou refuser les entreprises, gerer les utilisateurs, traiter les signalements, superviser les offres et consulter des indicateurs globaux sur l'activite de l'application.

Le projet integre aussi un systeme de matching entre candidats et offres. Ce systeme permet d'estimer la compatibilite d'un profil avec une offre en se basant sur plusieurs criteres, notamment les competences, le niveau d'etude, la localisation et les informations disponibles dans le CV lorsque celles-ci sont exploitables.

## Problematique

La problematique principale de ce projet peut etre formulee comme suit :

**Comment concevoir et developper une plateforme web de recrutement securisee, organisee et capable d'aider les candidats a trouver des offres pertinentes tout en aidant les entreprises a identifier les profils adaptes ?**

Cette problematique principale se decline en plusieurs questions secondaires :

- comment offrir a chaque type d'utilisateur un espace adapte a ses besoins ?
- comment permettre aux entreprises de publier des offres tout en garantissant un minimum de controle administratif ?
- comment aider les candidats a identifier les offres les plus pertinentes ?
- comment permettre aux entreprises d'analyser plus facilement les candidatures recues ?
- comment proteger les CV et les donnees personnelles des utilisateurs ?
- comment encadrer la messagerie afin d'eviter les abus et les contacts non autorises ?
- comment mettre en place un systeme de moderation efficace pour l'administrateur ?

Ces questions montrent que le projet ne se limite pas a la creation d'un simple site d'annonces. Il s'agit plutot d'une plateforme complete qui combine gestion des comptes, suivi des candidatures, securite, matching, notifications, messagerie controlee et administration.

## Objectifs du projet

Le projet JobConnect poursuit un objectif principal : realiser une application web permettant de gerer le processus de recrutement de maniere securisee, organisee et efficace.

Pour atteindre cet objectif, plusieurs objectifs specifiques ont ete definis :

- permettre aux candidats de creer un profil professionnel complet ;
- permettre aux candidats de deposer un CV ou de generer un CV a partir des informations saisies ;
- permettre aux candidats de consulter les offres et de postuler ;
- permettre aux candidats de suivre l'evolution de leurs candidatures ;
- permettre aux entreprises validees de publier et gerer leurs offres ;
- permettre aux entreprises de consulter les candidatures recues ;
- proposer un systeme de suggestions et de matching entre offres et candidats ;
- proteger les CV et les donnees personnelles contre les acces non autorises ;
- encadrer la messagerie entre candidats et entreprises selon des regles de gestion claires ;
- fournir a l'administrateur un espace de gestion, de validation et de moderation ;
- assurer une interface claire et utilisable pour chaque type d'utilisateur ;
- produire une application demonstrable, testable et evolutive.

Ces objectifs permettent de structurer le projet autour d'un ensemble de fonctionnalites coherentes. Ils servent egalement de base pour evaluer si la solution realisee repond bien aux besoins identifies.

## Interet du projet

Le projet JobConnect presente un interet a la fois fonctionnel, technique et pedagogique.

Sur le plan fonctionnel, l'application repond a un besoin concret : faciliter la relation entre candidats et recruteurs. Elle permet d'organiser les candidatures, de centraliser les informations et de reduire certaines difficultes liees au tri manuel des profils.

Sur le plan technique, le projet mobilise plusieurs competences importantes du developpement web : conception de base de donnees, authentification, gestion des roles, formulaires, upload de fichiers, securite, tableaux de bord, notifications, messagerie, tests et organisation du code selon une architecture claire.

Sur le plan pedagogique, ce projet constitue une occasion d'appliquer les connaissances acquises durant la formation en developpement web et mobile. Il permet de passer d'un besoin fonctionnel a une solution concrete, en suivant les etapes classiques d'un projet informatique : analyse, conception, realisation, validation et documentation.

## Methodologie suivie

La realisation de JobConnect suit une demarche progressive inspiree du cycle de developpement d'une application web. Cette demarche permet de structurer le travail et de passer progressivement de l'idee initiale a une solution fonctionnelle.

La premiere phase consiste a analyser le besoin. Elle permet de comprendre le contexte du recrutement digital, d'identifier les acteurs du systeme et de definir les fonctionnalites attendues.

La deuxieme phase concerne l'etude de l'existant. Elle consiste a observer des plateformes similaires afin d'identifier les fonctionnalites courantes, les limites possibles et les elements qui peuvent inspirer la conception de JobConnect.

La troisieme phase est la specification des besoins. Elle permet de formaliser les besoins fonctionnels, les besoins non fonctionnels, les regles de gestion et le perimetre du projet. Le cahier des charges realise dans le cadre du projet constitue une reference importante pour cette phase.

La quatrieme phase concerne la conception. Elle permet de definir l'architecture generale de l'application, les principaux modules, les relations entre les entites, les diagrammes UML et la structure de la base de donnees.

La cinquieme phase est la realisation technique. Elle correspond au developpement des fonctionnalites de l'application : authentification, gestion des profils, offres, candidatures, CV, matching, messagerie, notifications et administration.

Enfin, la derniere phase concerne les tests et la validation. Elle permet de verifier que les principaux parcours utilisateurs fonctionnent correctement et que l'application respecte les regles de gestion definies.

Cette methodologie progressive facilite le suivi du projet et permet de limiter les risques d'erreur en validant chaque partie avant de passer a la suivante.

## Outils et technologies mobilises

La realisation de JobConnect repose sur un ensemble de technologies adaptees au developpement d'une application web dynamique.

Le framework Laravel est utilise pour la partie serveur. Il fournit une structure claire pour organiser le code, gerer les routes, les controleurs, les modeles, les vues, l'authentification et les interactions avec la base de donnees.

Le langage PHP est utilise pour la logique applicative. La base de donnees MySQL permet de stocker les utilisateurs, les profils, les offres, les candidatures, les CV, les messages, les notifications et les signalements.

Pour la partie interface, l'application s'appuie sur Blade, CSS et JavaScript. Ces technologies permettent de construire des pages dynamiques, de gerer les formulaires et d'offrir une experience utilisateur adaptee aux differents roles.

Des outils de test et de versioning sont egalement mobilises afin d'assurer une meilleure qualite du projet et de suivre les evolutions du code.

## Organisation du rapport

Ce rapport est organise en plusieurs chapitres afin de presenter de maniere progressive l'ensemble du travail realise.

Le premier chapitre est consacre a la presentation generale du projet. Il introduit l'etablissement, le sujet choisi, le contexte, les motivations, les objectifs, le perimetre et le planning previsionnel.

Le deuxieme chapitre porte sur l'analyse et la specification des besoins. Il presente l'etude de l'existant, les limites des solutions observees, les acteurs du systeme, les besoins fonctionnels et non fonctionnels, ainsi que les principales regles de gestion.

Le troisieme chapitre presente la conception du systeme. Il decrit l'architecture generale, l'organisation MVC, le modele de donnees, les diagrammes UML, les diagrammes d'activite et les choix de conception lies a la securite.

Le quatrieme chapitre detaille la realisation de l'application. Il presente l'environnement de developpement, les technologies utilisees, la structure du projet et les principaux modules developpes : espace candidat, espace entreprise, espace administrateur, offres, candidatures, CV, matching, messagerie, notifications et signalements.

Le cinquieme chapitre est consacre aux tests et a la validation. Il presente la strategie de test, les scenarios fonctionnels, les tests de securite, les tests du matching et les resultats obtenus.

Le sixieme chapitre presente le bilan du projet, les difficultes rencontrees, les competences acquises, les limites actuelles de l'application et les perspectives d'evolution.

Enfin, le rapport se termine par une conclusion generale, une bibliographie et des annexes regroupant notamment le cahier des charges, les diagrammes et les elements complementaires utiles a la comprehension du projet.

---

# Chapitre 1 : Presentation generale du projet

## 1.1 Presentation de l'etablissement

Cette partie presente SupTechnology, l'etablissement dans lequel le projet de fin d'etudes a ete realise.

## 1.2 Presentation du projet JobConnect

JobConnect est une application web de recrutement permettant de mettre en relation les candidats et les entreprises a travers une plateforme centralisee.

## 1.3 Contexte et motivation

Cette partie explique les raisons qui ont conduit au choix du sujet et l'interet du projet dans le domaine du recrutement digital.

## 1.4 Objectifs generaux et specifiques

Cette partie detaille les objectifs principaux et secondaires du projet.

## 1.5 Perimetre du projet

Cette partie precise les fonctionnalites incluses dans le projet ainsi que les fonctionnalites exclues.

## 1.6 Planning previsionnel

Cette partie presente les grandes phases de realisation du projet.

---

# Chapitre 2 : Analyse et specification des besoins

## 2.1 Etude de l'existant

Cette partie analyse les plateformes de recrutement existantes telles que LinkedIn, Indeed, ReKrute, Emploi.ma et ANAPEC.

## 2.2 Limites des solutions existantes

Cette partie presente les limites observees et justifie l'interet de la solution JobConnect.

## 2.3 Identification des acteurs

Les acteurs principaux du systeme sont :

- visiteur ;
- candidat ;
- entreprise ;
- administrateur.

## 2.4 Besoins fonctionnels

Cette partie decrit les principales fonctionnalites attendues par chaque acteur.

## 2.5 Besoins non fonctionnels

Cette partie presente les exigences liees a la securite, l'ergonomie, la performance, la fiabilite et la maintenabilite.

## 2.6 Regles de gestion

Cette partie presente les principales regles metier du systeme.

## 2.7 Diagramme de cas d'utilisation

Le diagramme de cas d'utilisation sera insere dans cette partie.

## 2.8 Description textuelle des cas d'utilisation principaux

Cette partie detaille les principaux cas d'utilisation : inscription, publication d'offre, candidature, validation entreprise, messagerie et signalement.

---

# Chapitre 3 : Conception du systeme

## 3.1 Architecture generale

Cette partie presente l'architecture generale de l'application.

## 3.2 Architecture MVC

Cette partie explique l'organisation du projet selon le modele MVC.

## 3.3 Conception de la base de donnees

Cette partie presente les principales tables et relations du systeme.

## 3.4 Diagramme de classes

Le diagramme de classes sera insere dans cette partie.

## 3.5 Diagramme entite-association

Le diagramme entite-association sera insere dans cette partie.

## 3.6 Diagrammes de sequence

Cette partie presente les interactions principales entre les acteurs et le systeme.

## 3.7 Diagrammes d'activite

Cette partie presente les principaux flux fonctionnels du projet.

## 3.8 Securite et controle d'acces

Cette partie decrit les mecanismes de securite prevus dans l'application.

---

# Chapitre 4 : Realisation de l'application

## 4.1 Environnement de developpement

Cette partie presente l'environnement utilise pour developper l'application.

## 4.2 Technologies utilisees

Cette partie presente les technologies principales : Laravel, PHP, MySQL, Blade, CSS et JavaScript.

## 4.3 Structure du projet

Cette partie decrit l'organisation generale du code source.

## 4.4 Authentification et gestion des roles

Cette partie presente la gestion des comptes et des droits d'acces.

## 4.5 Espace candidat

Cette partie presente les fonctionnalites offertes au candidat.

## 4.6 Espace entreprise

Cette partie presente les fonctionnalites offertes a l'entreprise.

## 4.7 Espace administrateur

Cette partie presente les fonctionnalites offertes a l'administrateur.

## 4.8 Gestion des offres et candidatures

Cette partie explique le fonctionnement de la publication des offres et du suivi des candidatures.

## 4.9 Gestion des CV

Cette partie presente le depot, la consultation et la generation des CV.

## 4.10 Systeme de matching et suggestions

Cette partie explique le principe du score de compatibilite entre offres et candidats.

## 4.11 Messagerie controlee

Cette partie presente les regles de communication entre candidats et entreprises.

## 4.12 Notifications et signalements

Cette partie presente les notifications internes et le traitement des signalements.

## 4.13 Interfaces principales

Cette partie contiendra les captures d'ecran des principales interfaces.

---

# Chapitre 5 : Tests et validation

## 5.1 Strategie de test

Cette partie presente la demarche adoptee pour verifier le bon fonctionnement de l'application.

## 5.2 Tests fonctionnels

Cette partie decrit les tests lies aux principaux parcours utilisateurs.

## 5.3 Tests de securite

Cette partie presente les tests relatifs aux roles, autorisations et protections des donnees.

## 5.4 Tests du matching

Cette partie explique la validation du systeme de matching.

## 5.5 Scenarios de demonstration

Cette partie presente les scenarios utilises pour la demonstration du projet.

## 5.6 Resultats obtenus

Cette partie resume les resultats des tests et validations.

---

# Chapitre 6 : Bilan et perspectives

## 6.1 Bilan du travail realise

Cette partie resume le travail accompli durant le projet.

## 6.2 Difficultes rencontrees

Cette partie presente les principales difficultes techniques et organisationnelles.

## 6.3 Competences acquises

Cette partie presente les competences developpees durant la realisation du projet.

## 6.4 Limites actuelles

Cette partie presente les limites de la version actuelle de JobConnect.

## 6.5 Perspectives d'evolution

Les evolutions possibles incluent :

- application mobile ;
- recherche avancee ;
- calendrier d'entretien ;
- export PDF avance ;
- amelioration du matching ;
- tableaux de bord plus detailles.

---

# Conclusion generale

Cette partie conclut le rapport en rappelant les objectifs atteints, l'apport du projet et les perspectives futures.

---

# Bibliographie et webographie

- Documentation Laravel : https://laravel.com/docs
- Documentation PHP : https://www.php.net/docs.php
- Documentation MySQL : https://dev.mysql.com/doc/
- LinkedIn Talent Solutions : https://business.linkedin.com/
- Indeed : https://www.indeed.com/
- ReKrute : https://www.rekrute.com/
- Emploi.ma : https://www.emploi.ma/
- ANAPEC : https://anapec.ma/

---

# Annexes

## Annexe A : Cahier des charges

Le cahier des charges complet du projet JobConnect sera place dans cette annexe.

## Annexe B : Diagrammes UML

Les diagrammes realises se trouvent dans le dossier `diagrammes-jobconnect`.

## Annexe C : Captures d'ecran

Cette annexe contiendra les captures d'ecran des interfaces principales.

## Annexe D : Guide d'installation

Cette annexe expliquera les etapes d'installation et de lancement du projet.

## Annexe E : Comptes de demonstration

Cette annexe contiendra les comptes utilises pour la demonstration, si necessaire.
