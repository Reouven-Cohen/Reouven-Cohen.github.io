# Audit SEO & GEO — assu-conseil.com — 5 octobre 2026

Audit réalisé sur le site **live** (www.assu-conseil.com), en croisant inspection directe du HTML déployé (33 pages du sitemap), recherche web externe (visibilité, présence annuaires, réputation) et le travail déjà documenté dans `SEO.md` / `GEO.md`. Ce rapport ne répète pas ce qui est déjà fait (voir ces deux fichiers) — il se concentre sur ce que cet audit a trouvé de **nouveau**.

## En résumé

Les fondations techniques sont solides : HTTPS, sitemap à jour avec `lastmod`, `robots.txt` ouvert aux bots IA, zéro page `noindex`, zéro image sans `alt`, zéro page sans titre/description/canonical/H1. Le travail GEO récent (FAQ, JSON-LD enrichi, auteur nommé, dates visibles) est bien en ligne et conforme au repo. Les deux points faibles concrets : **5 titres trop longs** (jusqu'à 103 caractères, tronqués dans Google) et **les 12 pages partenaires n'ont aucune donnée structurée et des meta descriptions trop longues**. Le vrai écart, confirmé par des tests de recherche réels, est en dehors du site : sur 4 requêtes testées (dont une très locale, "courtier mutuelle Paris 20e"), Assu-Conseil n'apparaît dans aucun résultat — la faiblesse n'est plus technique, elle est dans l'autorité externe (déjà identifié comme priorité long terme dans `GEO.md`, confirmé ici par des faits).

Note SEO indicative : **7/10** (bases techniques et on-page solides, quelques titres/descriptions à raccourcir, pages partenaires sans schema). Note GEO indicative : **6/10** (structure et données structurées bien faites sur les pages principales, mais aucune visibilité externe mesurée pour l'instant — normal vu l'ancienneté du travail et le niveau de concurrence). Ce sont des appréciations qualitatives, pas des scores officiels.

## Priorités

| # | Action | Pourquoi | Impact | Effort | Concerne |
|---|---|---|---|---|---|
| 1 | Raccourcir les 5 titres `<title>` trop longs (listés ci-dessous, le pire fait 103 caractères) | Google tronque l'affichage au-delà de ~60 caractères ; un titre coupé en plein milieu nuit au clic | Fort | Rapide | SEO |
| 2 | Raccourcir les 7 meta descriptions trop longues sur les pages partenaires (jusqu'à 209 caractères) | Idem, tronquées au-delà de ~155-160 caractères dans le snippet Google | Moyen | Rapide | SEO |
| 3 | Ajouter un JSON-LD minimal (`Organization` ou `Article`) sur les 12 pages partenaires | Seules pages du site sans aucune donnée structurée ; actuellement invisibles en tant qu'entités pour les IA | Moyen | Moyen | GEO |
| 4 | Lancer soi-même un test PageSpeed Insights (mobile) sur la page d'accueil | L'API publique a renvoyé une erreur 429 (quota) pendant cet audit — la performance n'a **pas pu être mesurée**, à vérifier manuellement sur [pagespeed.web.dev](https://pagespeed.web.dev) | Inconnu tant que non mesuré | Rapide | SEO |
| 5 | Vérifier/démentir la mention trouvée sur un blog listant les démarchages téléphoniques (détail plus bas) | Un signal de réputation externe négatif, même mineur, pèse sur la confiance — surtout sur un site YMYL | Faible à moyen | Rapide | GEO |
| 6 | Continuer le travail sur les avis Google / annuaires (point 7 déjà identifié dans `GEO.md`) | Confirmé ici : zéro visibilité du site sur 4 requêtes testées, y compris une requête locale gagnable ("courtier mutuelle Paris 20e") | Fort, long terme | Lourd | SEO + GEO |
| 7 | Revoir le contenu des 12 pages partenaires pour les différencier un peu plus (actuellement très templatées) | Contenu proche d'une page à l'autre = signal de pages "fines" pour un moteur | Faible | Moyen | SEO |

## Détail des constats

### SEO technique

- **HTTPS** : ok sur toutes les pages testées.
- **`robots.txt`** : ouvert (`Allow: /`), bots IA listés explicitement (`GPTBot`, `OAI-SearchBot`, `ChatGPT-User`, `ClaudeBot`, `Claude-SearchBot`, `PerplexityBot`, `Google-Extended`, `Bingbot`). Confirmé identique en direct et en local.
- **`sitemap.xml`** : 33 URLs, toutes avec `<lastmod>`. Confirmé en direct via `curl` (un premier résumé automatique avait annoncé 35 par erreur — vérifié et corrigé par comptage direct).
- **`llms.txt`** : présent et accessible en ligne, contenu conforme au dépôt.
- **Indexabilité** : aucune page `noindex` détectée sur les 33 URLs du sitemap.
- **`viewport`** : présent sur les pages testées (mobile-friendly au niveau balise).
- **Performance (PageSpeed Insights)** : **non mesurée** — l'API publique Google a renvoyé une erreur 429 (quota dépassé, pas de clé API utilisée). Ne pas déduire de chiffre de performance de cet audit ; à tester directement sur [pagespeed.web.dev](https://pagespeed.web.dev/).
- **Contenu en HTML natif** (pas de dépendance JS pour le contenu principal) : confirmé, cohérent avec ce qui était déjà noté dans `GEO.md`.

### SEO on-page et contenu

**Titres `<title>` trop longs** (recommandation Google : ~50-60 caractères avant troncature) :

| Page | Longueur | Titre actuel |
|---|---|---|
| `pages/blog/remboursement-medecines-douces.html` | 103 c | *Comment sont remboursées les médecines douces (ostéopathe, chiropracteur, acupuncteur) ? – Assu-Conseil* |
| `pages/blog/optam-non-optam.html` | 85 c | *Praticien OPTAM ou non-OPTAM : quel remboursement par votre mutuelle ? – Assu-Conseil* |
| `pages/blog/mutuelle-responsable.html` | 74 c | *Mutuelle responsable et 100&nbsp;% Santé : le guide complet – Assu-Conseil* |
| `pages/mutuelle-collective.html` | 68 c | *Mutuelle Entreprise : Complémentaire Santé Collective – Assu-Conseil* |
| `pages/blog/remboursement-orthodontie-enfant.html` | 67 c | *Comment est remboursée l'orthodontie chez l'enfant ? – Assu-Conseil* |

Exemple de correction pour le pire cas : *"Remboursement des médecines douces (ostéo, acupuncture) – Assu-Conseil"* (≈ 70 c, encore un peu long mais nettement mieux) ou retirer le suffixe « – Assu-Conseil » sur les titres déjà longs (le nom de marque apparaît de toute façon dans le snippet via le nom de domaine).

**Meta descriptions trop longues** (recommandation : ~150-160 caractères) — uniquement sur les pages partenaires, toutes au même format :

| Page | Longueur |
|---|---|
| `pages/partenaires/malj.html` | 209 c |
| `pages/partenaires/gsmc.html` | 196 c |
| `pages/partenaires/ffa-luxior.html` | 188 c |
| `pages/partenaires/asaf.html` | 186 c |
| `pages/partenaires/neoliane.html` | 181 c |
| `pages/partenaires/apicil.html` | 179 c |
| `pages/partenaires/swiss-life.html` | 179 c |

Les 5 autres pages partenaires n'ont pas été passées en détail mais suivent probablement le même patron (à vérifier/raccourcir toutes en même temps).

**H1** : une seule balise H1 par page sur les 33 URLs testées, aucune page sans H1. Bon point, rien à corriger.

**Images / `alt`** : aucune image sans attribut `alt` détectée sur les pages testées.

### Données structurées

- Pages principales (accueil, 6 pages produit, 8 articles de blog) : JSON-LD cohérent et confirmé en ligne — `InsuranceAgency`, `Service`, `BlogPosting`, `FAQPage`/`Question`/`Answer`, auteur `Person` (Didier Cohen). Tout correspond à ce qui a été commité dans ce repo.
- **Les 12 pages partenaires n'ont aucune donnée structurée** (`ld=set()` sur toutes les pages testées). Ce sont les seules pages du site dans ce cas. Un JSON-LD `Organization` simple (nom du partenaire, `url` si disponible, `parentOrganization` ou lien vers Assu-Conseil) serait cohérent avec le reste du site et peu coûteux à ajouter.

### GEO : lisibilité par les IA

Rien de nouveau par rapport à `GEO.md` : structure en questions, FAQ, réponses directes, tout est en place et confirmé en ligne. Le seul point technique nouveau trouvé ici est le manque de JSON-LD sur les pages partenaires (voir ci-dessus).

### GEO : autorité et présence externe

**Présence en annuaires** : confirmée. Assu-Conseil apparaît sur Pages Jaunes, Pappers, Mappy, Yelp, Societe.com, annuaire-inverse-france, Hoodspot. Cohérence d'adresse/téléphone vérifiée sur plusieurs d'entre eux (38 rue des Ormeaux, 01 40 24 20 20).

**Avis** : sur Pages Jaunes, 3 avis visibles dans le résultat de recherche (1×5 étoiles, 2×4 étoiles), commentaires positifs ("bons conseils pour choisir une mutuelle santé", "a permis de réduire les cotisations"). Échantillon faible (3 avis) — cohérent avec la démarche déjà en cours côté client pour en solliciter davantage (point déjà noté dans `GEO.md`).

**Horaires** : un résultat d'annuaire externe indique "lundi au vendredi, 10h-13h et 14h-18h", alors que `qui-sommes-nous.html` et le JSON-LD du site indiquent "lundi au vendredi, 9h-18h" (sans coupure méridienne). **Incohérence à vérifier** — soit l'annuaire est obsolète (à corriger côté annuaire), soit les horaires réels ont une coupure déjeuner non reflétée sur le site. La cohérence NAP (nom/adresse/téléphone — ici horaires) entre le site et les annuaires externes est un signal de confiance pour le SEO local.

**Mention trouvée sur un blog personnel** (`leonregent.fr/Demarchage_Assurances.htm`) : cette page recense ~100 entreprises ayant démarché l'auteur par téléphone. Assu-Conseil y est citée une fois, avec la remarque qu'elle se serait d'abord présentée sous le nom "Assurance santé" avant de démarcher à domicile (dates rapportées : 2019, 2023). **Ce n'est pas une accusation d'arnaque** — c'est un témoignage personnel isolé sur du démarchage téléphonique jugé importun, noyé parmi une centaine d'exemples similaires visant d'autres entreprises du secteur. Signalé par honnêteté (ça remonte dans une recherche sur le nom de l'entreprise), mais à relativiser : ce n'est pas un signal de réputation structurant, juste un point à connaître.

### Test de visibilité (requêtes testées et résultats)

Quatre requêtes formulées comme un humain les taperait, testées via recherche web le 5 octobre 2026 :

| Requête | Assu-Conseil apparaît ? | Qui apparaît à la place |
|---|---|---|
| "meilleure mutuelle senior Paris comparateur" | ❌ Non | Meilleurtaux, Skarlett, Dispofi, grands comparateurs nationaux |
| "comment résilier sa mutuelle santé" | ❌ Non | Matmut, Assurland, LeLynx, Réassurez-moi, Apicil, Hyperassur |
| "mutuelle TNS déductible loi Madelin courtier" | ❌ Non | Empruntis, L'Expert-Comptable, AGI Assurance, France-Épargne |
| "courtier mutuelle Paris 20e arrondissement" | ❌ Non | Pages Jaunes (catégorie), 118712, d'autres courtiers locaux (NH Assurances, David Cohen, Bacuet-assurances) |

**Aucune des 4 requêtes ne fait remonter le site**, y compris la requête la plus locale et la moins concurrentielle. C'est un **échantillon indicatif** (les résultats varient selon l'outil, l'utilisateur, le moment — cet audit n'interroge pas directement ChatGPT/Perplexity/Google AI Mode) mais ça confirme concrètement ce que `GEO.md` identifiait déjà comme la priorité long terme (point 7) : le travail technique sur le site est fait, l'autorité externe (backlinks, avis, mentions) reste le chantier qui manque pour transformer ce travail en visibilité réelle.

## Ce qui va bien

- Zéro page non indexable, zéro `alt` manquant, zéro titre/description/canonical manquant — base technique propre sur 33/33 pages.
- JSON-LD cohérent et à jour sur toutes les pages principales (confirmé en ligne, pas seulement dans le repo).
- `robots.txt` et `llms.txt` en ligne et conformes, bots IA explicitement autorisés.
- Présence déjà établie sur plusieurs annuaires reconnus (Pages Jaunes, Societe.com, Pappers...) avec adresse/téléphone cohérents.
- Premiers avis clients positifs, démarche de sollicitation déjà en cours.

## Limites de cet audit

- **Performance (Core Web Vitals / PageSpeed)** non mesurée — quota API dépassé pendant l'audit. À lancer manuellement sur [pagespeed.web.dev](https://pagespeed.web.dev/?url=https%3A%2F%2Fwww.assu-conseil.com%2F).
- **Google Search Console** non consultée (pas d'accès) — impossible de connaître les impressions/clics/positions réelles, ni les éventuelles erreurs d'indexation remontées par Google lui-même.
- **Fiche Google Business Profile** non revérifiée en direct dans cet audit (la recherche web ne l'a pas fait remonter précisément) — `GEO.md` indique qu'elle est déjà vérifiée ; à confirmer visuellement sur Google Maps, notamment pour corriger l'écart d'horaires relevé plus haut.
- **5 des 12 pages partenaires** n'ont pas été vérifiées en détail individuellement pour la longueur de leur meta description (seules 7 ont été mesurées) — à vérifier/corriger toutes ensemble.
- **Test de visibilité IA** : cet audit n'a pas interrogé ChatGPT, Perplexity ou Google AI Mode directement (seulement une recherche web classique). Les résultats peuvent différer d'un outil à l'autre — tester directement ces 4 requêtes dans ces assistants reste la méthode la plus fiable (déjà recommandé comme suivi mensuel dans `GEO.md`, point 8).
