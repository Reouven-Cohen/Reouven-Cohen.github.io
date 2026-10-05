# GEO Assu-Conseil — référencement dans les IA (suivi)

**GEO** = *Generative Engine Optimization* : être cité par les IA génératives quand un internaute leur pose une question d'assurance ("quelle mutuelle pour un senior ?", "comment résilier ma mutuelle ?", "courtier mutuelle Paris 20e").

Moteurs visés :
- **Google AI Overviews / AI Mode** (le bloc de réponse IA en haut des résultats Google)
- **ChatGPT Search** (crawler `OAI-SearchBot`, navigation `ChatGPT-User`)
- **Perplexity** (`PerplexityBot`)
- **Claude** (`ClaudeBot`, `Claude-SearchBot`)
- **Microsoft Copilot / Bing Chat** (via l'index Bing, crawler `bingbot`)
- **Mistral / Le Chat** (marché français)

Site : `https://www.assu-conseil.com` — GitHub Pages, 33 URLs au sitemap.

> ⚠️ À retenir : le GEO **n'est pas** un canal séparé du SEO. Les IA s'appuient en grande partie sur les index de recherche existants (Google, Bing) pour aller chercher leurs sources. Tout le travail du fichier `SEO.md` est donc le socle du GEO. Ce fichier liste ce qui s'ajoute **par-dessus**.

---

## Déjà acquis (hérité du travail SEO)

Ces points sont faits et comptent directement pour le GEO :

- **`robots.txt` ouvert** (`User-agent: * / Allow: /`) → aucun crawler IA n'est bloqué. C'est le prérequis n°1 : la plupart des sites perdent la visibilité IA en bloquant ces bots par défaut (ou via leur CDN/Cloudflare).
- **`sitemap.xml`** (33 URLs) déclaré dans `robots.txt` → les crawlers IA l'utilisent pour découvrir les pages.
- **Données structurées JSON-LD** : `InsuranceAgency` sur l'accueil (nom, adresse, téléphone, email, logo), `BlogPosting` sur les 8 articles de blog (auteur, éditeur, date de publication). Les IA lisent ces données pour identifier *qui* parle et *de quand* date l'info.
- **Balises canoniques** sur les 24 pages → évite que l'IA cite une URL dupliquée ou la mauvaise version.
- **HTML statique, pas de contenu généré en JavaScript** → point souvent sous-estimé : les crawlers IA (contrairement à Googlebot) **n'exécutent pas le JavaScript**. Un site en React/Vue sans SSR est quasi invisible pour eux. Ici, tout le contenu est dans le HTML servi : ✅.
- **Site léger** (CSS compilé, 26 Ko, plus de CDN Tailwind) → crawl rapide, pas de timeout.
- **Google Business Profile vérifié** → source d'identité pour les réponses locales ("courtier assurance Paris 20e").
- **8 articles de blog** répondant à des questions précises (OPTAM/non-OPTAM, remboursement couronne dentaire, implant, orthodontie, prothèses auditives, médecines douces, mutuelle responsable, résiliation) → c'est exactement le bon format de départ : une page = une question.
- ✅ **Les 8 articles restructurés en format "réponse directe"** (2026-10-05) : chaque article commence par une réponse de 2-3 phrases, puis est découpé en `<h2>` formulés comme des questions ("Puis-je résilier à tout moment ?", "Qu'est-ce que l'OPTAM ?"...), avec tableaux comparatifs (paniers dentaires, classes I/II des aides auditives, cas de résiliation) et listes à puces là où il y avait des chiffres à comparer. Aucun fait ajouté — uniquement du contenu existant redécoupé. `dateModified` mis à jour dans le JSON-LD de chaque article.
- ✅ **FAQ + schema `FAQPage` sur les 6 pages produit** (2026-10-05) : mutuelle-senior, mutuelle-tns, mutuelle-collective, assurance-pret, assurance-animaux, protection-obseques ont chacune une section "Questions fréquentes" (4-5 questions en accordéon `<details>`, sans JS) + le JSON-LD `FAQPage` correspondant. Ces 6 pages n'avaient jusque-là aucune donnée structurée du tout. Toutes les réponses reprennent du contenu déjà présent sur la page (aucun chiffre inventé).
- ✅ **FAQ + schema `FAQPage` sur les 8 articles de blog** (2026-10-05) : 3 questions complémentaires par article (qui ne répètent pas les `<h2>` déjà dans le corps du texte), même format accordéon + JSON-LD `FAQPage` en plus du `BlogPosting` existant. Toutes les réponses vérifiées chiffre par chiffre contre le contenu déjà présent.

---

## Reste à faire

Classé par rapport impact / effort. Le point 2 est le plus rentable maintenant que le point 1 est fait.

### 3. ✅ JSON-LD de l'entité "Assu-Conseil" enrichi — fait le 2026-10-05

Objectif : que les IA sachent **qui vous êtes** et vous reconnaissent comme une entité réelle et fiable (notion d'*entity recognition*).

Ajouté au bloc `InsuranceAgency` sur `index.html` :
- `identifier` : numéro **ORIAS 07002705** + numéro **RCS Paris B414889923** (trouvés sur `mentions-legales.html`, déjà publics, juste absents des données structurées).
- `foundingDate` : `1998`.
- `areaServed` : Paris / Île-de-France / France.
- `openingHoursSpecification` : lundi-vendredi 9h-18h (trouvé sur `qui-sommes-nous.html`).
- `knowsAbout` : les 6 domaines d'expertise.
- `hasOfferCatalog` : les 6 produits en `Service`, chacun avec sa page.
- `legalName` : "ASSU CONSEIL" (dénomination légale sur les mentions légales).

Ajouté aussi un JSON-LD `Service` dédié sur chacune des 6 pages produit (nom, description reprise du `<meta description>` existant, `provider` → Assu-Conseil, `areaServed`).

**Non fait, faute de données disponibles sur le site** (à ne pas inventer) :
- `sameAs` : liens vers la fiche Google Business Profile, LinkedIn, Pages Jaunes — **aucune de ces URLs n'existe sur le site actuellement**. Il faudrait que le client les fournisse.
- `geo` (latitude/longitude) et `priceRange` : pas de source fiable sur le site pour ces valeurs sans les inventer.

### 4. ✅ Auteur crédibilisé (E-E-A-T) — fait le 2026-10-05

- `"author"` passé de `Organization` à `Person` sur les 8 articles : `{ "@type": "Person", "name": "Didier Cohen", "jobTitle": "Dirigeant d'Assu-Conseil", "worksFor": {...} }`.
- Ligne visible ajoutée sous le sous-titre de chaque article : "Par Didier Cohen, dirigeant d'Assu-Conseil · Publié le 17 septembre 2026 · Mis à jour le 5 octobre 2026".
- Numéro ORIAS (07002705) rendu visible sur `qui-sommes-nous.html`, en plus des mentions légales.

**Reste possible, non fait** : étoffer encore `qui-sommes-nous.html` (photo, nombre de clients précis, détail des compagnies partenaires) — la page a déjà "20+ ans", "100% indépendant" et "des milliers de clients" (formulation déjà présente), donc le gain resterait marginal sans nouvelles infos du client.

### 5. Ajouter un fichier `llms.txt`

Convention émergente (`/llms.txt` à la racine) : un fichier Markdown qui liste les pages du site avec une description courte, pour guider les IA vers le contenu utile.

- Statut honnête : **aucun moteur ne l'exploite officiellement aujourd'hui**, ni OpenAI, ni Anthropic, ni Google. Coût ≈ 15 minutes, bénéfice spéculatif.
- À faire quand les points 1-4 sont traités, pas avant.

### 6. ✅ Bots IA nommés dans `robots.txt` — fait le 2026-10-05

`GPTBot`, `OAI-SearchBot`, `ChatGPT-User`, `ClaudeBot`, `Claude-SearchBot`, `PerplexityBot`, `Google-Extended`, `Bingbot` sont maintenant listés explicitement (en plus du `Allow: /` global déjà présent), pour documenter le choix et protéger d'un blocage accidentel futur.

À noter : `GPTBot` sert à l'**entraînement** des modèles, `OAI-SearchBot` à l'**indexation** pour ChatGPT Search. Ce sont les bots de recherche (`OAI-SearchBot`, `Claude-SearchBot`, `PerplexityBot`) qui comptent pour être cité en temps réel.

### 7. Présence sur les sources tierces que les IA citent le plus ⭐ le vrai levier long terme

C'est le point le plus important et le plus long. Les IA ne citent pas que des sites d'entreprises : elles s'appuient massivement sur des sources tierces jugées neutres. Les études sur les citations des AI Overviews montrent que Reddit, les forums, Wikipédia, les avis et les annuaires professionnels y sont surreprésentés par rapport à leur place dans les résultats Google classiques.

Traduction concrète pour un courtier :
- **Avis Google** (déjà en cours côté client) : volume + réponses apportées aux avis. Les IA résument les avis pour juger de la réputation.
- **Annuaires de courtiers / ORIAS / Pages Jaunes** : fiches complètes et cohérentes avec le site (même nom, même adresse, même téléphone — la cohérence NAP est lue comme un signal de fiabilité).
- **Forums et communautés FR** (Reddit r/france, r/vosfinances, forums santé/retraite) : répondre à de vraies questions, en se présentant, sans spam. C'est lent mais c'est ce qui se retrouve cité.
- **Presse locale / blogs partenaires** : un article ou une interview dans un média du 20e arrondissement vaut plus en GEO qu'un backlink d'annuaire.
- Ce chantier recouvre le point "Backlinks" du `SEO.md` : même action, double bénéfice.

### 8. Mettre en place un suivi des mentions IA

Aucun outil ne donne de "position" en GEO comme en SEO. Méthode de suivi :

- **Tests manuels mensuels** : poser 15-20 questions-types à ChatGPT, Perplexity, Google AI Mode et Le Chat ("meilleure mutuelle senior Paris", "comment résilier ma mutuelle", "courtier mutuelle TNS Paris 20e") et noter si Assu-Conseil est cité. À consigner dans un tableau daté, dans ce fichier.
- **GA4** : créer un segment sur le trafic référent IA (`chatgpt.com`, `perplexity.ai`, `claude.ai`, `copilot.microsoft.com`, `gemini.google.com`). Volume faible mais **taux de conversion typiquement bien supérieur** au trafic SEO classique (l'internaute arrive avec une intention déjà qualifiée par l'IA).
- **Search Console** : les impressions issues des AI Overviews sont comptées dans les données Web standard, sans distinction — on ne peut pas les isoler. Ne pas chercher à les séparer.

### 9. ✅ `lastmod` ajouté au sitemap — fait le 2026-10-05

Les 33 URLs ont maintenant une balise `<lastmod>`, basée sur la date du dernier commit git touchant chaque fichier (pas une date arbitraire). Les crawlers IA s'en servent pour prioriser le recrawl.

**Limite à connaître** : ces dates sont figées au 2026-10-05 pour l'instant — elles ne se mettront pas à jour automatiquement au prochain changement de contenu. Pour que `lastmod` reste fiable dans le temps, il faudrait soit le régénérer manuellement à chaque modification de page, soit automatiser ça dans `.github/workflows/build-css.yml` (régénérer le sitemap à partir des dates de commit à chaque push). Non fait pour l'instant — à décider si le volume de mises à jour du site justifie l'automatisation.

---

## Ordre d'exécution recommandé

| # | Action | Effort | Impact |
|---|---|---|---|
| 1 | ✅ Restructurer les 8 articles (réponse d'abord, `<h2>` interrogatifs, listes, tableaux) | — | fait le 2026-10-05 |
| 2 | ✅ Blocs FAQ + `FAQPage` sur les 6 pages produit + les 8 articles de blog | — | fait le 2026-10-05 |
| 3 | ✅ JSON-LD `InsuranceAgency` enrichi (ORIAS, `foundingDate`, `hasOfferCatalog`) + `Service` sur les 6 pages produit | — | fait le 2026-10-05 |
| 4 | ✅ Auteur personne (Didier Cohen) + dates visibles + ORIAS visible | — | fait le 2026-10-05 |
| 9 | ✅ `lastmod` dans le sitemap | — | fait le 2026-10-05 |
| 6 | ✅ Bots IA nommés dans `robots.txt` | — | fait le 2026-10-05 |
| 8 | Suivi mensuel des mentions IA + segment GA4 | faible, récurrent | mesure |
| 7 | Sources tierces (avis, annuaires, forums, presse) | élevé, continu | ⭐⭐⭐ (long terme) |
| 5 | `llms.txt` | faible | spéculatif |

---

## Infos techniques de référence

- Domaine canonique : `https://www.assu-conseil.com`
- ID de mesure GA4 : `G-38HZVW9KJH`
- Numéro ORIAS : **à récupérer** (nécessaire pour le point 3)
- Fichier SEO associé : `SEO.md` (socle du GEO, à lire en premier)
