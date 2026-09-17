# Référencement Assu-Conseil — suivi

Hébergement : GitHub Pages, domaine `https://www.assu-conseil.com` (fichier `CNAME`).

## Fait

### Technique on-page
- `sitemap.xml` créé à la racine, liste les 24 pages du site avec priorités.
- `robots.txt` créé à la racine, autorise tout et pointe vers le sitemap.
- `meta description` ajoutée sur les 7 pages qui n'en avaient pas (assurance-pret, assurance-animaux, mentions-legales, politique-confidentialite, mutuelle-collective, protection-obseques, qui-sommes-nous). Les autres pages en avaient déjà.
- Balise `<link rel="canonical">` ajoutée sur les 24 pages HTML du site.
- Données structurées `schema.org` (JSON-LD, type `InsuranceAgency`) ajoutées sur `index.html` : nom, adresse, téléphone, email, logo.
- Favicon ajouté (absent auparavant, donc pas de logo dans les résultats de recherche/onglets) : `favicon.ico`, `favicon-16x16.png`, `favicon-32x32.png`, `apple-touch-icon.png`, `android-chrome-192x192.png`, `android-chrome-512x512.png` générés à partir du monogramme "A" du logo (`logo.png`), + `site.webmanifest`. Balises `<link rel="icon">` / `apple-touch-icon` / `manifest` ajoutées sur les 24 pages.
- Balises Open Graph / Twitter Card (`og:title`, `og:description`, `og:image`, `twitter:card`, etc.) ajoutées sur les 24 pages, avec `og:image` pointant vers `logo.png` — améliore l'aperçu du site (logo + titre + description) quand le lien est partagé (réseaux sociaux, messagerie).

### Nouvelle page produit
- `pages/assurance-animaux.html` créée (assurance chien/chat) sur le même modèle que les autres pages produit, avec formulaire de devis dédié (espèce, nom, race, sexe, date de naissance, case "tatoué ou pucé" obligatoire).
- Intégrée partout : accueil (`index.html`), formulaire `devis.html`, menus "Autres produits" des autres pages produit, pied de page de tout le site.

### Google Business Profile
- Fiche existante, revendiquée. **À vérifier** : confirmer que le statut est bien "Vérifiée" (pas juste "Revendiquée") — c'est la validation postale/téléphonique qui débloque le bénéfice SEO local.

### Google Analytics (GA4)
- ID de mesure : `G-38HZVW9KJH`.
- Ajouté sur les **24 pages** du site. 8 pages avaient déjà le tag Google Ads (`AW-17878035895`) et ont reçu une ligne `gtag('config', 'G-38HZVW9KJH')` en plus. 16 pages (mentions légales, confidentialité, qui-sommes-nous, liste partenaires, 12 fiches partenaires) n'avaient **aucun tag** avant et ont reçu le bloc complet (Ads + GA4).
- Événement GA4 `generate_lead` ajouté sur les **7 formulaires de devis** du site (`devis.html` + les 6 formulaires intégrés dans mutuelle-senior, mutuelle-tns, mutuelle-collective, assurance-pret, protection-obseques, assurance-animaux), déclenché uniquement quand l'envoi réussit. Testé et confirmé fonctionnel dans le rapport Temps réel.

### Google Search Console
- Propriété validée (via la détection automatique du tag `gtag.js` d'Analytics — **ne pas retirer ce tag du site**, sinon la validation Search Console saute).
- `sitemap.xml` soumis.

### Performance
- Le CDN Tailwind Play (`cdn.tailwindcss.com`, non recommandé en production par Tailwind lui-même) a été retiré des **24 pages** et remplacé par un CSS compilé et minifié (`css/tailwind.css`, ~26 Ko vs plusieurs centaines de Ko de JS pour le CDN), chargé via `<link rel="stylesheet" href="/css/tailwind.css">`.
- Mise en place du build : `package.json` + `tailwind.config.js` (palette de couleurs `primary`/`accent` et police `Inter` reprises à l'identique de l'ancienne config inline) + `src/tailwind-input.css` (les 3 directives `@tailwind`). Commande : `npm run build:css`.
- `.github/workflows/build-css.yml` : reconstruit et recommit automatiquement `css/tailwind.css` à chaque push sur `main` qui touche un fichier `.html` ou la config Tailwind — pas besoin de builder manuellement avant de push.
- Vérifié visuellement (captures d'écran) sur l'accueil, une page produit et une page partenaire imbriquée : rendu identique à l'ancien CDN, aucune classe manquante.
- `node_modules/` ignoré via `.gitignore` ; `css/tailwind.css` lui est bien commité (c'est un artefact de build nécessaire à GitHub Pages, qui ne fait pas tourner de build lui-même).

### Mots-clés SEO
Liste de mots-clés fournie par le client, intégrée dans les balises `<title>` / `<meta description>` (+ Open Graph/Twitter miroir) des pages concernées :

| Mot-clé | Page(s) mise(s) à jour |
|---|---|
| Comparateur (en) mutuelle | `index.html`, `devis.html` |
| Devis mutuelle gratuit | `devis.html` (déjà présent, renforcé) |
| Assurance de prêt | `pages/assurance-pret.html` (déjà présent) |
| Mutuelle TNS | `pages/mutuelle-tns.html` (déjà présent) |
| Mutuelle entreprise | `pages/mutuelle-collective.html` |
| Mutuelle senior | `pages/mutuelle-senior.html` (déjà présent) |
| Mutuelle responsable | article de blog `pages/blog/mutuelle-responsable.html` |

Mots-clés complémentaires ajoutés (suggestion Claude, validée par le client) :
- **Complémentaire santé** (synonyme à fort volume de "mutuelle") — ajouté dans `index.html`, `mutuelle-senior.html`, `mutuelle-tns.html`, `mutuelle-collective.html`.
- **Mutuelle pas chère / complémentaire pas chère** — ajouté dans la description de `index.html`.
- **Courtier assurance Paris 20e** (variante locale) — ajouté dans `pages/qui-sommes-nous.html` et `index.html`.
- **Résiliation mutuelle** — fort volume de recherche, pas encore présent sur le site. Piste à traiter via un futur article de blog plutôt qu'un ajout forcé dans une page existante (voir "Reste à faire").

## Reste à faire

1. **Marquer `generate_lead` comme conversion dans GA4** : Rapports → Cycle de vie → Engagement → Événements → activer "Marquer comme conversion" en face de `generate_lead`. L'événement doit d'abord apparaître dans ce rapport (délai 24-48h après le premier déclenchement, contrairement au rapport Temps réel qui est instantané).
2. **Vérifier le statut de la fiche Google Business Profile** (vérifiée ou non).
3. **Suivre l'indexation dans Search Console** dans les jours/semaines suivants : section "Pages" (indexées vs exclues) et "Performances" (mots-clés, positions).
4. Pistes moyen terme non abordées : contenu/blog pour le SEO, backlinks, avis clients sur la fiche Google Business Profile.
5. Idée d'article de blog sur le mot-clé "résiliation mutuelle" (loi Chatel / résiliation infra-annuelle).

## Infos techniques de référence

- Domaine canonique : `https://www.assu-conseil.com`
- ID Google Ads : `AW-17878035895`
- ID de mesure GA4 : `G-38HZVW9KJH`
- Adresse : 38 rue des Ormeaux, 75020 Paris
- Téléphone : 01.40.24.20.20
- Email : assu.conseil@orange.fr
