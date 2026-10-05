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

---

## Reste à faire

Classé par rapport impact / effort. Les points 1 à 4 sont les plus rentables.

### 1. Restructurer les articles en format "réponse directe" ⭐ priorité

**Constat actuel** : les articles de blog font ~400 mots et sont rédigés en **un seul gros paragraphe**, sans sous-titres (`resiliation-mutuelle.html` : 1 `<h1>`, 1 `<h2>` de call-to-action, aucun `<h3>`, aucune liste).

**Pourquoi c'est bloquant** : une IA ne "lit" pas une page comme un humain, elle la **découpe en passages** (*chunks*) et sélectionne celui qui répond à la question. Un bloc de 400 mots sans structure = un seul chunk flou, difficile à extraire et à citer. Un article découpé en sections avec titres interrogatifs = 5 ou 6 chunks nets, chacun candidat à une citation.

**À faire sur chaque article** :
- Commencer par une **réponse de 2-3 phrases** juste sous le `<h1>` (format "la réponse d'abord", pas d'intro de mise en contexte). C'est ce bloc qui est repris mot pour mot dans les AI Overviews.
- Découper le corps en `<h2>` / `<h3>` formulés **comme des questions** ("Puis-je résilier avant un an ?", "Quel préavis respecter ?", "Qui s'occupe des démarches ?").
- Ajouter des **listes à puces** et au moins un **tableau** quand il y a des chiffres à comparer (les IA extraient très bien les tableaux).
- Viser **800-1500 mots** par article, avec des **chiffres précis, dates et références de loi** (déjà bien fait : "1er décembre 2020", "loi Chatel", "préavis de 2 mois" → c'est exactement ce qui rend un passage citable).

### 2. Ajouter des blocs FAQ + schema `FAQPage` ⭐ priorité

Aucune page n'a actuellement de JSON-LD `FAQPage` (vérifié : seuls `InsuranceAgency`, `BlogPosting`, `Organization`, `ImageObject` sont présents).

- Ajouter en bas de chaque page produit (mutuelle-senior, mutuelle-tns, mutuelle-collective, assurance-pret, assurance-animaux, protection-obseques) et de chaque article une section **"Questions fréquentes"** : 4 à 6 questions, réponses de 40 à 60 mots chacune.
- Doubler cette section d'un JSON-LD `FAQPage` (`mainEntity` → `Question` / `acceptedAnswer`).
- Les questions doivent reprendre **la formulation parlée** des internautes ("c'est quoi une mutuelle responsable ?", "combien coûte une mutuelle senior à 70 ans ?") — les requêtes adressées aux IA sont des phrases complètes, pas des mots-clés.

### 3. Enrichir le JSON-LD de l'entité "Assu-Conseil"

Objectif : que les IA sachent **qui vous êtes** et vous reconnaissent comme une entité réelle et fiable (notion d'*entity recognition*).

Sur `index.html`, compléter le bloc `InsuranceAgency` avec :
- `sameAs` : liens vers la fiche Google Business Profile, LinkedIn, Pages Jaunes, ORIAS → c'est ce qui **relie** le site à des sources tierces vérifiables.
- `foundingDate` : `1998` (ancienneté = signal de confiance fort, déjà mis en avant dans la description).
- `areaServed` : Paris / Île-de-France / France.
- `priceRange`, `openingHoursSpecification` (horaires), `geo` (latitude/longitude).
- `knowsAbout` : liste des domaines d'expertise (mutuelle senior, TNS, collective, obsèques, assurance de prêt, assurance animaux).
- `hasOfferCatalog` : les 6 produits, chacun en `Service` avec sa page.
- **Numéro ORIAS** (obligatoire pour un courtier) via `identifier` → signal d'autorité réglementaire que les IA valorisent sur les sujets financiers/assurance (domaine "YMYL" = *Your Money or Your Life*, où les moteurs sont les plus exigeants sur la fiabilité de la source).

Sur chaque page produit, ajouter un JSON-LD `Service` (nom, description, `provider` → Assu-Conseil, `areaServed`).

### 4. Crédibiliser l'auteur (E-E-A-T)

Actuellement `"author": { "@type": "Organization", "name": "Assu-Conseil" }` sur les articles.

- Passer à un **auteur personne** : `"author": { "@type": "Person", "name": "...", "jobTitle": "Courtier en assurances", "worksFor": ... }`.
- Afficher visiblement sur chaque article : **nom de l'auteur + date de publication + date de mise à jour**. Les IA pondèrent fortement la fraîcheur : un article daté et récemment mis à jour est préféré à un article sans date.
- Étoffer `qui-sommes-nous.html` en vraie page d'autorité : années d'expérience, ORIAS, nombre de clients, compagnies partenaires, photo. C'est la page que l'IA ira lire pour décider si elle peut vous citer comme source d'expertise.

### 5. Ajouter un fichier `llms.txt`

Convention émergente (`/llms.txt` à la racine) : un fichier Markdown qui liste les pages du site avec une description courte, pour guider les IA vers le contenu utile.

- Statut honnête : **aucun moteur ne l'exploite officiellement aujourd'hui**, ni OpenAI, ni Anthropic, ni Google. Coût ≈ 15 minutes, bénéfice spéculatif.
- À faire quand les points 1-4 sont traités, pas avant.

### 6. Autoriser explicitement les bots IA dans `robots.txt`

Le `Allow: /` global suffit techniquement. Mais lister les bots nommément documente le choix et protège d'un blocage accidentel futur :

```
User-agent: GPTBot
Allow: /

User-agent: OAI-SearchBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: Claude-SearchBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: Bingbot
Allow: /
```

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

### 9. Ajouter `lastmod` au sitemap

Les 33 URLs du sitemap n'ont aucune balise `<lastmod>` (vérifié). Les crawlers IA s'en servent pour prioriser le recrawl → sans elle, une page mise à jour peut rester citée dans sa version périmée. À générer automatiquement, idéalement dans le workflow GitHub Actions existant (`.github/workflows/build-css.yml`).

---

## Ordre d'exécution recommandé

| # | Action | Effort | Impact |
|---|---|---|---|
| 1 | Restructurer les 8 articles (réponse d'abord, `<h2>` interrogatifs, listes, tableaux) | élevé | ⭐⭐⭐ |
| 2 | Blocs FAQ + `FAQPage` sur les 6 pages produit + articles | moyen | ⭐⭐⭐ |
| 3 | Enrichir le JSON-LD `InsuranceAgency` (`sameAs`, ORIAS, `foundingDate`, `hasOfferCatalog`) | faible | ⭐⭐ |
| 4 | Auteur personne + dates visibles + page "Qui sommes-nous" étoffée | moyen | ⭐⭐ |
| 9 | `lastmod` dans le sitemap | faible | ⭐ |
| 6 | Bots IA nommés dans `robots.txt` | faible | ⭐ |
| 8 | Suivi mensuel des mentions IA + segment GA4 | faible, récurrent | mesure |
| 7 | Sources tierces (avis, annuaires, forums, presse) | élevé, continu | ⭐⭐⭐ (long terme) |
| 5 | `llms.txt` | faible | spéculatif |

---

## Infos techniques de référence

- Domaine canonique : `https://www.assu-conseil.com`
- ID de mesure GA4 : `G-38HZVW9KJH`
- Numéro ORIAS : **à récupérer** (nécessaire pour le point 3)
- Fichier SEO associé : `SEO.md` (socle du GEO, à lire en premier)
