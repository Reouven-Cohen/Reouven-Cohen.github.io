# Statut du projet Assu-Conseil

## Contexte du projet

- Refonte du site Assu-Conseil avec un design plus moderne, rassurant et oriente conversion.
- Conserver un site simple a utiliser pour les visiteurs.
- Mettre en avant les produits d'assurance, les compagnies partenaires et le parcours de devis.
- Uniformiser l'identite visuelle sur toutes les pages, notamment le logo.
- Preparer un envoi de demandes de devis fiable et coherent.

## Etat actuel du travail

- Structure globale du site harmonisee :
  - header, top bar et footer alignes sur les pages principales,
  - meme logo et meme theme visuel sur l'accueil et les pages internes.
- Landing page retravaillee :
  - hero recentre et plus lisible,
  - offres actives resserrees a 5 produits,
  - CTA devis bien visibles,
  - sections en doublon retirees,
  - textes visibles corriges apres remise en UTF-8 des pages HTML publiques,
  - cartes assurances de l'accueil repassees avec les noms complets des offres.
- Partenaires mis en avant sur l'accueil :
  - ajout d'un bandeau en roulement,
  - integration de tous les logos disponibles dans le depot,
  - chaque logo mene vers `devis.html`,
  - bandeau deplace hors de la zone bleue du hero vers une section claire dediee,
  - section partenaires compactee apres retour de design, avec logos agrandis,
  - bloc partenaires recentre visuellement avec titre et texte recentres.
- Perimetre produit aligne sur la consigne actuelle :
  - offres actives : Senior, TNS, Collective, Obseques, Pret,
  - retrait de la page `Actes rembourses` du perimetre visible,
  - retrait de `Chien / Chat` et `Mutuelle International`,
  - suppression des liens correspondants dans l'accueil, les footers et les pages internes,
  - suppression des pages `pages/actes-rembourses.html`, `pages/chien-chat.html` et `pages/mutuelle-international.html`,
  - redirection des anciennes URLs vers `devis.html`.
- Formulaire de devis aligne avec le perimetre courant :
  - produits retires supprimes de l'interface,
  - valeurs de produits rationalisees en slugs cote front,
  - validation backend restreinte aux 5 offres actives,
  - backend prepare pour un envoi direct via l'API Mailjet sans dependre du dossier `vendor`,
  - configuration runtime prevue via `MAILJET_SECRET_KEY` avec surcharge possible de `MAILJET_FROM_EMAIL` et `MAILJET_TO_EMAIL`,
  - destination par defaut du devis repassee sur `nqrypro@gmail.com`.
- Contenu retire :
  - blog deja retire du site et du brief courant.
  - commentaires de structure retires des fichiers HTML, JS embarque et PHP.

## Travaux en cours

- Verification finale visuelle et coherence globale.
- Verification des derniers libelles apres rationalisation ASCII de certains champs.
- Finalisation du flux Mailjet des devis des que la `Secret Key` est generee et que l'expediteur est valide.

## Journal de coordination

- 2026-04-13 : prise de relais et lecture des documents `prompt.md` et `project-status.md` pour repartir d'une base commune.
- Regle de travail fixee : relire les fichiers `.md` du depot a chaque demande et mettre a jour le suivi projet apres les avancees significatives.
- 2026-04-13 : decision produit appliquee, le blog est retire du site et du brief courant.
- 2026-04-13 : decision produit appliquee, `Chien / Chat` et `Mutuelle International` sortent du perimetre actif.
- 2026-04-13 : bloc partenaires de l'accueil transforme en bandeau cliquable menant au devis.
- 2026-04-13 : ajustement de mise en page, les partenaires sortent du hero bleu et passent dans une section claire sous l'accroche.
- 2026-04-13 : calibrage visuel reduit sur la section partenaires, avec cartes moins hautes et logos plus grands.
- 2026-04-13 : retrait de `Actes rembourses` dans la navigation, suppression de la page dediee et ajout de sa redirection vers `devis.html`.
- 2026-04-13 : nettoyage des petits commentaires purement internes dans les HTML, le JavaScript embarque et le script PHP.
- 2026-04-13 : correction de l'encodage des pages HTML publiques pour restaurer les accents et les libelles lisibles.
- 2026-04-13 : bloc partenaires recentre sur l'accueil avec wording plus propre et plus lisible.
- 2026-04-13 : les cartes d'assurances de l'accueil affichent maintenant les noms complets des 5 offres actives.
- 2026-04-13 : le backend de devis est recable sur l'API Mailjet avec destination par defaut `assu.conseil@orange.fr` et sans dependance `vendor`.
- 2026-04-13 : suppression du BOM UTF-8 en tete de `send_devis.php` pour eviter tout blocage des `header()` PHP avant l'appel Mailjet.
- 2026-04-13 : destination par defaut du flux de devis basculee sur `nqrypro@gmail.com` a la demande projet, tout en gardant `assu.conseil@orange.fr` comme email public visible sur le site.
- 2026-04-14 : correction du parcours devis, les pages produits preselectionnent maintenant le type d'assurance dans `devis.html` via le parametre `produit`, et le front bloque proprement l'envoi si aucun produit n'est choisi.
- 2026-04-14 : durcissement du formulaire de devis, avec reponses JSON garanties cote PHP meme en cas d'erreur fatale et message front plus explicite si `send_devis.php` n'est pas execute par un serveur PHP.
- 2026-04-14 : installation locale de PHP 8.3 via `winget` et ajout des scripts `start-local.ps1`, `stop-local.ps1` et `start-local.bat` pour lancer le site sur un vrai serveur PHP local avec `curl` et `openssl`.
- 2026-04-14 : ajout d'un chargement local de variables Mailjet via `site_assu_conseil/.env` avec gabarit `.env.example`, et message frontend plus explicite en local si la `MAILJET_SECRET_KEY` manque.
- 2026-04-14 : fichier local `site_assu_conseil/.env` initialise pour le flux Mailjet ; il ne manque plus que la valeur de `MAILJET_SECRET_KEY`.

## Difficultes rencontrees

- Plusieurs fichiers HTML ont un encodage heterogene, ce qui complique les patches fins.
- La capture Mailjet fournie ne contient pas encore de `Secret Key` generee, donc l'authentification complete ne peut pas etre validee ici.
- L'adresse expediteur choisie pour Mailjet doit etre verifiee dans le compte, sinon Mailjet refusera l'envoi.
