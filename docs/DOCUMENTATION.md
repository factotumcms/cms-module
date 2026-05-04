# Factotum CMS — Documentazione Tecnica e Funzionale

> **Package:** `wave8/factotum-cms`  
> **Namespace:** `Wave8\Factotum\Cms`  
> **Dipendenze:** `wave8/factotum-base ^1.7`, `kalnoy/nestedset ^7.0`  
> **PHP:** `^8.4` · **Laravel:** `11+`

---

## Indice

1. [Panoramica Generale](#1-panoramica-generale)
2. [Architettura e Struttura del Package](#2-architettura-e-struttura-del-package)
3. [Installazione e Configurazione](#3-installazione-e-configurazione)
4. [Content Types — Tipi di Contenuto](#4-content-types--tipi-di-contenuto)
5. [Content Fields — Campi Dinamici](#5-content-fields--campi-dinamici)
6. [Contents — Gestione dei Contenuti](#6-contents--gestione-dei-contenuti)
7. [Taxonomie e Termini](#7-taxonomie-e-termini)
8. [Traduzioni (Multi-lingua)](#8-traduzioni-multi-lingua)
9. [URL Aliases — Gestione degli URL Pubblici](#9-url-aliases--gestione-degli-url-pubblici)
10. [Front Controller — Risoluzione degli URL Pubblici](#10-front-controller--risoluzione-degli-url-pubblici)
11. [Sitemap — Generazione Automatica](#11-sitemap--generazione-automatica)
12. [API Reference](#12-api-reference)
13. [Pattern Architetturali](#13-pattern-architetturali)
14. [Schema del Database](#14-schema-del-database)

---

## 1. Panoramica Generale

**Factotum CMS** è un modulo CMS headless per Laravel, progettato come package riutilizzabile e indipendente. Permette di gestire contenuti strutturati in modo completamente dinamico: i tipi di contenuto, i loro campi e le tassonomie vengono definiti a runtime tramite API, senza necessità di scrivere codice o migrazioni.

### Principi Fondamentali

- **Headless-first**: tutte le operazioni avvengono tramite API REST protette, con un FrontController opzionale per il rendering server-side.
- **Schema dinamico**: la creazione di un Content Type genera automaticamente tabella e modello Eloquent nell'applicazione host.
- **Multi-lingua nativo**: ogni contenuto ha una lingua (`lang`) e può essere collegato alle sue traduzioni tramite un sistema di *translation groups*.
- **URL decoupled**: gli URL pubblici sono gestiti tramite una tabella dedicata (`url_aliases`), completamente disaccoppiata dalla struttura dei contenuti.
- **Estensibilità**: DTO, Resource e Service sono configurabili tramite il file `data_transfer.php`, permettendo all'applicazione host di sovrascriverne il comportamento.

---

## 2. Architettura e Struttura del Package

```
factotum-cms/
├── config/
│   ├── config.php              # Configurazione principale del modulo
│   └── data_transfer.php       # Binding DTO ↔ Resource (sovrascrivibili)
├── database/
│   ├── factories/
│   ├── migrations/             # Migrazioni del package
│   └── seeder/
├── routes/
│   ├── api/
│   │   ├── protected/          # API autenticate (auth:sanctum)
│   │   └── public/             # API pubbliche
│   └── web/
│       └── public/             # Route web (FrontController)
├── src/
│   ├── Casts/                  # Custom Eloquent casts
│   ├── Console/Commands/       # Comandi Artisan
│   ├── Contracts/              # Interfacce dei servizi
│   │   └── Api/                # Contratti servizi API
│   ├── Dtos/Api/               # Data Transfer Objects
│   ├── Enums/                  # Enumerazioni
│   ├── Events/                 # Eventi (predisposto)
│   ├── Http/
│   │   ├── Controllers/Api/    # Controller API
│   │   ├── Controllers/Web/    # FrontController
│   │   ├── Requests/Api/       # Form Request validation
│   │   └── Responses/
│   ├── Jobs/                   # Job asincroni
│   ├── Listeners/              # Listener eventi (predisposto)
│   ├── Models/                 # Modelli Eloquent
│   ├── Observers/              # Observer per side-effect automatici
│   ├── Policies/               # Authorization policies
│   ├── Providers/              # Service Providers
│   ├── Resources/              # API Resources e Model Resources
│   ├── Rules/                  # Validation rules
│   ├── Services/               # Business logic
│   │   └── Api/                # Servizi API
│   └── Traits/                 # Trait riutilizzabili
├── stubs/                      # Template per generazione dinamica
└── resources/views/            # Blade templates
```

### Service Providers

| Provider | Responsabilità |
|----------|---------------|
| `ModuleServiceProvider` | Entry point: registra tutti i sub-provider, comandi, observer e scheduling |
| `ServiceProvider` | Binding delle interfacce ai servizi concreti (DI container) |
| `ConfigServiceProvider` | Merge e pubblicazione dei file di configurazione |
| `RouteServiceProvider` | Registrazione delle rotte API (protette e pubbliche) e web |
| `EventServiceProvider` | [Predisposto] Registrazione eventi e listener |
| `LangServiceProvider` | Caricamento dei file di traduzione |

---

## 3. Installazione e Configurazione

### Comando di Installazione

```bash
php artisan factotum-cms:install [--migrate] [--seed] [--force]
```

Il comando `factotum-cms:install` esegue i seguenti step:

1. **Pubblica configurazioni**: copia `config.php` in `config/factotum_cms.php`
2. **Pubblica migrazioni**: copia le migrazioni nella directory `database/migrations`
3. **Pubblica modelli**: (opzionale) copia i modelli stub nell'applicazione
4. **Pubblica provider**: registra `FactotumCmsServiceProvider` nell'app
5. **Esegue migrazioni**: `php artisan migrate` (se richiesto)
6. **Seeding**: popola il database con dati iniziali (se richiesto)

### Configurazione Principale (`config/factotum_cms.php`)

```php
return [
    'module_name' => 'Cms',
    
    'sitemap' => [
        'enabled'            => env('FACTOTUM_SITEMAP_ENABLED', true),
        'base_url'           => env('FACTOTUM_SITEMAP_BASE_URL', env('APP_URL')),
        'max_urls_per_file'  => 50000,
        'output_path'        => public_path(),
        'default_changefreq' => 'weekly',
        'default_priority'   => '0.5',
        'routable_types'     => [
            Content::class => ['changefreq' => 'weekly',  'priority' => '0.8'],
            Term::class    => ['changefreq' => 'monthly', 'priority' => '0.6'],
        ],
        'ping_google' => true,
        'ping_bing'   => true,
    ],
];
```

### Configurazione Data Transfer (`config/data_transfer.php`)

Questo file mappa ogni DTO e Resource alla propria classe concreta. L'applicazione host può sovrascrivere questi binding per personalizzare il comportamento di input/output delle API:

```php
return [
    // DTO Bindings — sovrascrivibili per estendere la validazione
    CreateContentDto::class     => CreateContentDto::class,
    UpdateContentDto::class     => UpdateContentDto::class,
    CreateContentTypeDto::class => CreateContentTypeDto::class,
    // ...
    
    // Resource Bindings — sovrascrivibili per cambiare il formato di output
    ContentResource::class      => ContentResource::class,
    ContentTypeResource::class  => ContentTypeResource::class,
    // ...
];
```

---

## 4. Content Types — Tipi di Contenuto

### Concetto

Un **Content Type** rappresenta una tipologia di contenuto (es. "pages", "news", "products"). È il mattone fondamentale del CMS: definisce la struttura e il comportamento di una famiglia di contenuti.

### Modello: `ContentType`

```
Tabella: content_types
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK auto-increment |
| `label` | `string(64)` | Etichetta visuale (nullable) |
| `type` | `string(32)` | Identificativo univoco (es. "pages", "news") |
| `editable` | `boolean` | Se il tipo è modificabile dall'utente |
| `order_no` | `integer` | Ordinamento nella UI |
| `icon` | `string(64)` | Icona CSS/SVG (nullable) |
| `sitemap` | `boolean` | Se includere i contenuti nella sitemap |
| `visible` | `boolean` | Se il tipo è visibile nell'interfaccia |
| `hierarchical` | `boolean` | Se i contenuti supportano una struttura ad albero |

### Tipi Base (Enum `BaseContentType`)

```php
enum BaseContentType: string
{
    case PAGES = 'pages';
    case NEWS  = 'news';
}
```

### Relazioni

- `contentFields()` → `HasMany` verso `ContentField`
- `contents()` → `HasMany` verso `Content`
- `taxonomies()` → `BelongsToMany` verso `Taxonomy` (pivot: `content_type_taxonomy`)
- `urlAliases()` → `MorphMany` verso `UrlAlias` (trait `HasUrlAliases`)

### Generazione Dinamica (Observer: `ContentTypeObserver`)

Quando viene **creato** un Content Type, l'observer esegue automaticamente:

1. **Crea una tabella dinamica** nel database con il nome `snake_case` del tipo (es. `pages`, `news`). La tabella contiene le colonne base:
   - `id` (PK)
   - `content_id` (FK verso `contents`)
   - `timestamps`
   - `soft_deletes`

2. **Genera un modello Eloquent** in `app/Models/{PascalCase}.php` partendo dallo stub `DynamicModel.php.stub`

Quando viene **aggiornato** il campo `type`:
- Rinomina la tabella dinamica
- Rinomina e aggiorna il file del modello

Quando viene **eliminato**:
- Elimina la tabella dinamica dal database

---

## 5. Content Fields — Campi Dinamici

### Concetto

I **Content Field** sono i campi personalizzati che definiscono la struttura dati di un Content Type. Ogni campo aggiunto tramite API si traduce in una **colonna reale** sulla tabella dinamica del Content Type.

### Modello: `ContentField`

```
Tabella: content_fields
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `content_type_id` | `FK` | Riferimento al Content Type |
| `name` | `string` | Nome tecnico del campo (diventa nome colonna) |
| `label` | `string` | Etichetta visuale |
| `type` | `enum` | Tipo di campo (vedi sotto) |
| `order_no` | `integer` | Ordine di visualizzazione |
| `configs` | `json` | Configurazione aggiuntiva (cast: `CfConfigResource`) |

### Tipi di Campo (`ContentFieldType`)

```php
enum ContentFieldType: string
{
    case SELECT                   = 'select';
    case TEXT                     = 'text';
    case NUMBER                   = 'number';
    case URL                      = 'url';
    case TEXTAREA                 = 'textarea';
    case CHECKBOX                 = 'checkbox';
    case IMAGE_UPLOAD             = 'image_upload';
    case LINKED_CONTENT           = 'linked_content';
    case MULTIPLE_LINKED_CONTENT  = 'multiple_linked_content';
}
```

### Mapping Tipo → Colonna Database

| ContentFieldType | Tipo Colonna DB |
|------------------|----------------|
| `TEXT`, `TEXTAREA`, `URL`, `SELECT` | `string` (nullable) |
| `NUMBER` | `integer` (default: 0) |
| `CHECKBOX` | `boolean` (default: false) |
| `IMAGE_UPLOAD` | `unsignedBigInteger` (FK → `media.id`) |
| `LINKED_CONTENT` | `string` (nullable) |
| `MULTIPLE_LINKED_CONTENT` | `string` (nullable) |

### Generazione Dinamica (Observer: `ContentFieldObserver`)

- **created**: aggiunge una colonna alla tabella dinamica del Content Type, con il tipo corretto e posizionamento dopo l'ultima colonna non-riservata
- **updated (name)**: rinomina la colonna nella tabella dinamica
- **deleted**: rimuove la colonna dalla tabella dinamica

---

## 6. Contents — Gestione dei Contenuti

### Concetto

Un **Content** è l'istanza concreta di un Content Type. Rappresenta una pagina, un articolo, un prodotto, ecc. I contenuti supportano:

- **Struttura gerarchica** (nested set) per Content Type gerarchici
- **Multi-lingua** tramite il campo `lang` e il sistema di traduzioni
- **Workflow di pubblicazione** tramite stati
- **Parametri SEO e Social** in formato JSON
- **Campi dinamici** salvati nella tabella satellite del Content Type

### Modello: `Content`

```
Tabella: contents
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `content_type_id` | `FK` | Riferimento al Content Type |
| `user_id` | `FK` | Autore del contenuto |
| `parent_id` | `FK` (self, nullable) | Pagina genitore (nested set) |
| `status` | `string(25)` | Stato di pubblicazione |
| `title` | `string(255)` | Titolo del contenuto |
| `editor_type` | `string(32)` | Tipo di editor usato |
| `content` | `longText` | Corpo del contenuto |
| `url` | `string(191)` | Slug URL del contenuto |
| `abs_url` | `string(191)` | URL assoluto calcolato (unique con `lang`) |
| `lang` | `string(5)` | Lingua del contenuto |
| `show_in_menu` | `boolean` | Visibile nel menu |
| `is_home` | `boolean` | Se è la homepage |
| `is_visible` | `boolean` | Se il contenuto è visibile |
| `order_no` | `integer` | Ordinamento |
| `seo_params` | `json` | Parametri SEO (title, description, ecc.) |
| `social_params` | `json` | Parametri social (og:title, og:image, ecc.) |

### Traits

| Trait | Funzionalità |
|-------|-------------|
| `HasTranslations` | Collegamento alle traduzioni multi-lingua |
| `HasUrlAliases` | Generazione e gestione URL aliases |
| `NodeTrait` | Supporto nested set (kalnoy/nestedset) |
| `SoftDeletes` | Soft delete |

### Stati di Pubblicazione (`ContentStatus`)

```php
enum ContentStatus: string
{
    case DRAFT     = 'draft';
    case PENDING   = 'pending';
    case REVIEW    = 'review';
    case PUBLISHED = 'published';
}
```

### Tipo di Editor (`ContentEditorType`)

```php
enum ContentEditorType: string
{
    case BUILDER = 'builder';   // Page builder strutturato
    case WYSIWYG = 'wysiwyg';  // Editor WYSIWYG classico
}
```

### Campi Dinamici — Architettura

I campi dinamici vengono salvati in una **tabella satellite** con lo stesso nome del Content Type:

```
contents (tabella principale)
    ├── id: 1
    ├── content_type_id: 1 (pages)
    ├── title: "Chi siamo"
    └── ...
    
pages (tabella satellite)
    ├── id: 1
    ├── content_id: 1  ──────→ FK verso contents.id
    ├── page_template: "about"
    ├── page_operation: "show_content"
    └── ...
```

Il `ContentService` gestisce la lettura e scrittura dei campi dinamici (`getDynamicFields`, `updateDynamicFields`) risolvendo automaticamente il modello Eloquent generato in `App\Models\{PascalCase}`.

### Nested Set (Struttura Gerarchica)

Per i Content Type con `hierarchical = true`, i contenuti supportano una struttura ad albero. Lo scope del nested set è personalizzato per `content_type_id`, permettendo alberi indipendenti per ogni tipo.

```php
// Ottenere il percorso gerarchico
$content->buildHierarchicalPath(); 
// → "chi-siamo/il-team" per una pagina innestata
```

### Generazione Automatica URL Aliases (Observer: `ContentUrlAliasObserver`)

Quando un Content viene:

- **Creato**: viene generato automaticamente un URL alias canonico basato su `abs_url`
- **Aggiornato** (campi `url`, `abs_url`, `lang`): l'alias canonico viene aggiornato. Il vecchio alias diventa un redirect 301 verso il nuovo
- **Eliminato**: tutti gli alias associati vengono eliminati

---

## 7. Taxonomie e Termini

### Concetto

Il sistema di tassonomie permette di categorizzare i contenuti con strutture gerarchiche flessibili.

- **Taxonomy**: definisce una famiglia di categorie (es. "Categorie", "Tag", "Genere")
- **Term**: singola voce all'interno di una Taxonomy, con supporto nested set

### Modello: `Taxonomy`

```
Tabella: taxonomies
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `name` | `string` | Identificativo tecnico |
| `label` | `string` | Etichetta visuale |
| `is_hierarchical` | `boolean` | Se supporta struttura ad albero |
| `sort_order` | `integer` | Ordinamento |

#### Relazioni

- `terms()` → `HasMany` verso `Term`
- `contentTypes()` → `BelongsToMany` verso `ContentType` (pivot `content_type_taxonomy`)

La pivot include:
- `is_required`: se la tassonomia è obbligatoria per il Content Type
- `allow_multiple`: se è possibile associare più termini

### Modello: `Term`

```
Tabella: terms
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `taxonomy_id` | `FK` | Riferimento alla Taxonomy |
| `parent_id` | `FK` (nullable) | Termine genitore (nested set) |
| `name` | `string` | Nome del termine |
| `slug` | `string` | Slug URL-friendly |
| `description` | `text` | Descrizione (nullable) |
| `lang` | `string(5)` | Lingua |
| `sort_order` | `integer` | Ordinamento |

#### Trait

- `HasTranslations` — supporto multi-lingua
- `HasUrlAliases` — generazione URL alias
- `NodeTrait` — struttura nested set (scopata per `taxonomy_id`)

#### Relazione Polimorfica con Contenuti

La relazione Content ↔ Term è polimorfica tramite la tabella `termables`:

```php
// Da Content
$content->terms();    // MorphToMany

// Da Term
$term->contents();    // MorphedByMany
```

### Generazione Automatica URL Aliases (Observer: `TermUrlAliasObserver`)

L'URI di un termine viene costruito gerarchicamente:

```
/{taxonomy-name}/{ancestor-slug}/.../{term-slug}
```

Esempio: `/categorie/tecnologia/intelligenza-artificiale`

Quando un termine cambia `slug`, `parent_id` o `lang`, l'observer:
1. Aggiorna l'alias canonico del termine
2. **Aggiorna ricorsivamente tutti i discendenti** per riflettere il nuovo percorso

---

## 8. Traduzioni (Multi-lingua)

### Concetto

Il sistema di traduzioni permette di collegare entità dello stesso tipo in lingue diverse tramite un meccanismo di **translation groups**. Ogni gruppo è identificato da un UUID univoco.

### Locali Supportati (`Locale`)

```php
enum Locale: string
{
    case IT = 'it';
    case EN = 'en';
}
```

### Modello: `Translation`

```
Tabella: translations
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `translation_group` | `uuid` | Identificativo del gruppo di traduzione |
| `translatable_id` | `bigint` | ID dell'entità tradotta |
| `translatable_type` | `string` | Tipo dell'entità (morph) |
| `locale` | `string(5)` | Locale della traduzione |

**Vincoli di unicità:**
- `(translation_group, locale)` — una sola entità per locale per gruppo
- `(translatable_id, translatable_type)` — ogni entità può appartenere a un solo gruppo

### Come Funziona il Collegamento

```
Translation Group: "abc-123-uuid"
┌─────────────────────────────────────────────┐
│  translatable_type: Content                 │
│  ┌─────────────┐  ┌─────────────┐           │
│  │ Content #1  │  │ Content #5  │           │
│  │ lang: it    │  │ lang: en    │           │
│  │ locale: it  │  │ locale: en  │           │
│  └─────────────┘  └─────────────┘           │
└─────────────────────────────────────────────┘
```

### Trait: `HasTranslations`

Ogni modello che usa il trait `HasTranslations` ottiene:

```php
// Relazione al record Translation
$content->translation();           // MorphOne → Translation

// UUID del gruppo
$content->translationGroup();      // "abc-123-uuid" | null

// Tutte le entità tradotte (inclusa se stessa)
$content->translatedSiblings();    // Collection<Content>

// Ottenere una specifica traduzione
$content->getTranslation(Locale::EN);  // Content | null

// Verificare se esiste una traduzione
$content->isTranslatedIn(Locale::EN);  // bool

// Locali disponibili e mancanti
$content->availableLocales();  // ['it', 'en']
$content->missingLocales();    // []
```

### Servizio: `TranslationService`

Il servizio gestisce le operazioni CRUD sulle traduzioni:

#### `link(Model $source, Model $target, Locale $sourceLocale, Locale $targetLocale): string`

Collega due entità come traduzioni reciproche. Logica:

1. Verifica che source e target siano di tipo diverso per locale
2. Verifica che siano dello stesso tipo di modello
3. Se uno dei due è già in un gruppo, il nuovo viene aggiunto a quel gruppo
4. Se nessuno ha un gruppo, ne viene creato uno nuovo (UUID)
5. Se entrambi sono già in gruppi diversi → errore

#### `unlink(Model $model): void`

Rimuove un'entità dal suo gruppo di traduzione. Se il gruppo rimane con un solo membro, viene eliminato anche quello (cleanup automatico).

#### `resolveModel(string $type, int $id): Model`

Risolve un tipo readable (`'content'`, `'term'`) nel modello Eloquent corrispondente.

---

## 9. URL Aliases — Gestione degli URL Pubblici

### Concetto

Gli **URL Aliases** sono il cuore del sistema di routing pubblico del CMS. Rappresentano una mappatura completa e decoupled tra un URI pubblico e l'entità a cui corrisponde.

Questo approccio permette:
- URL SEO-friendly indipendenti dalla struttura interna
- Redirect automatici 301 al cambio di slug
- Supporto multi-canale e multi-lingua
- URL personalizzabili manualmente

### Modello: `UrlAlias`

```
Tabella: url_aliases
```

| Campo | Tipo | Descrizione |
|-------|------|-------------|
| `id` | `bigint` | PK |
| `uri` | `string(2048)` | URI pubblico (es. `/chi-siamo/il-team`) |
| `routable_type` | `string` | Classe del modello (morph) |
| `routable_id` | `bigint` | ID del modello |
| `locale` | `string(10)` | Locale dell'alias |
| `is_canonical` | `boolean` | Se è l'alias canonico attivo |
| `redirect_to` | `string(2048)` | URI di redirect (null se canonico) |

**Indici:**
- `UNIQUE(uri, locale)` — ogni URI è univoco per locale
- `INDEX(routable_type, routable_id, locale)` — lookup rapido per entità
- `INDEX(is_canonical)` — filtro sugli alias canonici

### Scopes

```php
UrlAlias::canonical();                     // Solo alias canonici
UrlAlias::forLocale(Locale::IT);          // Per locale
UrlAlias::forUri('/chi-siamo');           // Per URI esatto
UrlAlias::forRoutable($content);          // Per entità
```

### Trait: `HasUrlAliases`

Aggiunge a qualsiasi modello la capacità di avere URL alias:

```php
$content->urlAliases();      // MorphMany → UrlAlias
$content->canonicalUrl();    // ?UrlAlias (il canonico corrente)
$content->getCanonicalUri(); // ?string (solo l'URI)
```

I modelli che usano `HasUrlAliases`:
- `Content`
- `Term`
- `ContentType`

### Servizio: `UrlAliasService`

#### `resolve(string $uri): ?UrlAlias`

Risolve un URI ad un UrlAlias. Normalizza l'URI (aggiunge `/` iniziale, rimuove il trailing slash).

#### `createForRoutable(Model $routable, string $uri, Locale|string $locale, bool $isCanonical = true): UrlAlias`

Crea un nuovo alias per un'entità. Se `isCanonical = true`, demote automaticamente gli eventuali alias canonici esistenti per la stessa entità + locale.

#### `updateCanonical(Model $routable, string $newUri, Locale|string $locale): UrlAlias`

Aggiorna l'alias canonico. Gestisce diversi scenari:

1. **URI invariato**: ritorna l'alias esistente senza modifiche
2. **URI cambiato**: 
   - Il vecchio alias canonico diventa non-canonico
   - Imposta `redirect_to` verso il nuovo URI (redirect 301)
   - Se il nuovo URI esisteva già come alias (es. ri-promozione), lo riattiva
   - Altrimenti, crea un nuovo alias canonico

```
Flusso cambio URL:
                                
/chi-siamo (canonical)  ──→  /chi-siamo (redirect → /about-us)
                              /about-us (canonical, nuovo)
```

#### `deleteForRoutable(Model $routable): void`

Elimina tutti gli alias associati a un'entità.

#### `setRedirect(UrlAlias $alias, string $targetUri): UrlAlias`

Imposta manualmente un redirect su un alias esistente.

### Generazione Automatica

Gli URL alias vengono creati e aggiornati automaticamente tramite observer:

| Evento | Content (`ContentUrlAliasObserver`) | Term (`TermUrlAliasObserver`) |
|--------|-------------------------------------|-------------------------------|
| `created` | Crea alias da `abs_url` | Crea alias da `{taxonomy}/{path}` |
| `updated` | Aggiorna se cambia `url`, `abs_url`, `lang` | Aggiorna se cambia `slug`, `parent_id`, `lang` + tutti i discendenti |
| `deleted` | Elimina tutti gli alias | Elimina tutti gli alias |

---

## 10. Front Controller — Risoluzione degli URL Pubblici

### Route Catch-All

```php
Route::get('{path}', FrontController::class)->where('path', '.*');
```

Questa route è registrata come **ultima** nel web group, con middleware `web` e `SetLocale`. Cattura qualsiasi path e lo risolve tramite il sistema di URL aliases.

### Flusso di Risoluzione (`FrontController::__invoke`)

```
Richiesta HTTP: GET /chi-siamo/il-team
         │
         ▼
    Normalizza URI: "/chi-siamo/il-team"
         │
         ▼
    UrlAliasService::resolve($uri)
         │
    ┌────┴────┐
    │ trovato? │
    └────┬────┘
      NO │         SI
         ▼          │
      404          ┌┴──────────────┐
                   │ redirect_to?  │
                   └┬──────────────┘
                 SI │            NO
                    ▼             │
              301 Redirect     ┌──┴──────────┐
                               │ is_canonical?│
                               └──┬──────────┘
                             NO   │         SI
                                  ▼          │
                            301 →         ┌──┴──────────┐
                            canonical     │  Tipo entità │
                                          └──┬──────────┘
                                             │
                              ┌──────────────┼───────────────┐
                              ▼              ▼               ▼
                           Content         Term        ContentType
                              │              │               │
                        handleContent    TermResource  ContentTypeResource
                              │
                    ┌─────────┴─────────┐
                    │   ContentType?     │
                    └─────────┬─────────┘
                              │
                    ┌─────────┼──────────┐
                    ▼                    ▼
                  PAGES               NEWS
                    │                    │
              page_operation        view: news
                    │
          ┌─────────┼──────────┐
          ▼                    ▼
    SHOW_CONTENT         CONTENT_LIST
          │                    │
    view: {template}    view: content-list
```

### Operazioni Pagina (`PageOperation`)

```php
enum PageOperation: string
{
    case SHOW_CONTENT = 'show_content';  // Mostra il contenuto della pagina
    case CONTENT_LIST = 'content_list';  // Mostra una lista di contenuti
}
```

Per `SHOW_CONTENT`, il template viene risolto con fallback:
1. Prima cerca nella app principale: `resources/views/{page_template}.blade.php`
2. Poi fallback al package: `factotum_cms::{page_template}`

---

## 11. Sitemap — Generazione Automatica

### Concetto

Il sistema genera file XML sitemap statici a partire dalla tabella `url_aliases`, garantendo che i motori di ricerca indicizzino correttamente tutti i contenuti pubblicati.

### Architettura

```
┌─────────────────┐     ┌──────────────────────┐     ┌──────────────────┐
│  Laravel         │     │  GenerateSitemap     │     │  Filesystem      │
│  Scheduler       │────→│  Job (queue)         │────→│  public/         │
│  (daily)         │     │                      │     │  ├── sitemap.xml │
└─────────────────┘     │  SitemapGenerator     │     │  ├── sitemap-it  │
                        │  Service              │     │  └── sitemap-en  │
                        └──────────┬───────────┘     └──────────────────┘
                                   │
                         ┌─────────┴──────────┐
                         │  Ping Search       │
                         │  Engines           │
                         │  (Google, Bing)    │
                         └────────────────────┘
```

### Comando Artisan

```bash
# Dispatch alla coda
php artisan factotum-cms:generate-sitemap

# Esecuzione sincrona
php artisan factotum-cms:generate-sitemap --sync
```

### Scheduling Automatico

Il package registra automaticamente lo scheduling giornaliero nel `ModuleServiceProvider`:

```php
$schedule->command('factotum-cms:generate-sitemap --sync')->daily();
```

### Logica di Generazione (`SitemapGeneratorService`)

1. **Pulizia**: elimina tutti i file `sitemap*.xml` dalla directory di output
2. **Query**: recupera tutti i `UrlAlias` canonici con i relativi modelli (`routable`)
3. **Filtro**: esclude:
   - Alias senza entità associata (`routable` null)
   - Entità soft-deleted
   - Contenuti non in stato `PUBLISHED`
4. **Raggruppamento**: divide per locale
5. **Chunking**: se un locale ha più di 50.000 URL (limite standard), li divide in file separati
6. **Hreflang**: costruisce una mappa di alternanze linguistiche basata sui translation groups
7. **Scrittura file**: genera i file XML sub-sitemap e il file indice

### File Generati

```
public/
├── sitemap.xml              # Sitemap Index
├── sitemap-it-1.xml         # Sub-sitemap locale italiano
├── sitemap-en-1.xml         # Sub-sitemap locale inglese
└── sitemap-it-2.xml         # (se > 50.000 URL)
```

### Formato XML Sub-Sitemap

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
  <url>
    <loc>https://example.com/chi-siamo</loc>
    <lastmod>2026-05-01T10:30:00+00:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
    <xhtml:link rel="alternate" hreflang="it" href="https://example.com/chi-siamo" />
    <xhtml:link rel="alternate" hreflang="en" href="https://example.com/about-us" />
  </url>
</urlset>
```

### Formato XML Sitemap Index

```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://example.com/sitemap-it-1.xml</loc>
    <lastmod>2026-05-04T00:00:00+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>https://example.com/sitemap-en-1.xml</loc>
    <lastmod>2026-05-04T00:00:00+00:00</lastmod>
  </sitemap>
</sitemapindex>
```

### Ping Motori di Ricerca

Dopo la generazione, il servizio notifica automaticamente:
- **Google**: `https://www.google.com/ping?sitemap={sitemap_url}`
- **Bing**: `https://www.bing.com/ping?sitemap={sitemap_url}`

Configurabili via `.env`:
```env
FACTOTUM_SITEMAP_PING_GOOGLE=true
FACTOTUM_SITEMAP_PING_BING=true
```

---

## 12. API Reference

Tutte le API protette richiedono autenticazione via **Sanctum** (`auth:sanctum`).

**Base URL**: `/api/v1`

### 12.1 Content Types

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/content-types` | Crea un nuovo Content Type | `create` on `ContentType` |
| `GET` | `/content-types/{contentType}` | Legge un Content Type | `read` on `contentType` |
| `PUT` | `/content-types/{contentType}` | Aggiorna un Content Type | `update` on `contentType` |
| `DELETE` | `/content-types/{contentType}` | Elimina un Content Type | `delete` on `contentType` |

#### Esempio di creazione

```json
POST /api/v1/content-types
{
    "type": "products",
    "label": "Prodotti",
    "editable": true,
    "sitemap": true,
    "hierarchical": false
}
```

### 12.2 Content Fields

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/content-types/{contentType}/content-fields` | Crea un campo | `createContentField` |
| `GET` | `/content-types/{contentType}/content-fields/{contentField}` | Legge un campo | `read` |
| `PUT` | `/content-types/{contentType}/content-fields/{contentField}` | Aggiorna un campo | `update` |
| `DELETE` | `/content-types/{contentType}/content-fields/{contentField}` | Elimina un campo | `delete` |

#### Esempio di creazione campo

```json
POST /api/v1/content-types/1/content-fields
{
    "name": "page_template",
    "label": "Template Pagina",
    "type": "select",
    "order_no": 1,
    "configs": { ... }
}
```

### 12.3 Contents

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/content-types/{contentType}/contents` | Crea un contenuto | `createContent` |
| `GET` | `/content-types/{contentType}/contents/{content}` | Legge un contenuto | `read` |
| `PUT` | `/content-types/{contentType}/contents/{content}` | Aggiorna un contenuto | `update` |
| `DELETE` | `/content-types/{contentType}/contents/{content}` | Elimina un contenuto | `delete` |

#### Esempio di creazione contenuto

```json
POST /api/v1/content-types/1/contents
{
    "title": "Chi siamo",
    "url": "chi-siamo",
    "abs_url": "chi-siamo",
    "lang": "it",
    "status": "published",
    "editor_type": "builder",
    "content": "<p>Contenuto della pagina</p>",
    "show_in_menu": true,
    "parent_id": null,
    "fields": {
        "page_template": "about",
        "page_operation": "show_content"
    }
}
```

L'oggetto `fields` viene salvato nella tabella satellite del Content Type.

### 12.4 Taxonomies

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/taxonomies` | Crea una Taxonomy | `create` |
| `GET` | `/taxonomies/{taxonomy}` | Legge una Taxonomy | `read` |
| `PUT` | `/taxonomies/{taxonomy}` | Aggiorna una Taxonomy | `update` |
| `DELETE` | `/taxonomies/{taxonomy}` | Elimina una Taxonomy | `delete` |
| `POST` | `/taxonomies/{taxonomy}/content-types/{contentType}` | Associa a Content Type | `update` |
| `DELETE` | `/taxonomies/{taxonomy}/content-types/{contentType}` | Disassocia da Content Type | `update` |

#### Esempio di associazione

```json
POST /api/v1/taxonomies/1/content-types/1
{
    "is_required": true,
    "allow_multiple": false
}
```

### 12.5 Terms

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `GET` | `/taxonomies/{taxonomy}/terms/tree` | Albero completo | — |
| `POST` | `/taxonomies/{taxonomy}/terms` | Crea un termine | `createTerm` |
| `GET` | `/taxonomies/{taxonomy}/terms/{term}` | Legge un termine | `read` |
| `PUT` | `/taxonomies/{taxonomy}/terms/{term}` | Aggiorna un termine | `update` |
| `DELETE` | `/taxonomies/{taxonomy}/terms/{term}` | Elimina un termine | `delete` |
| `GET` | `/content-types/{ct}/contents/{c}/terms` | Termini di un contenuto | `read` on content |
| `POST` | `/content-types/{ct}/contents/{c}/terms/sync` | Sincronizza termini | `update` on content |

#### Esempio sync termini a contenuto

```json
POST /api/v1/content-types/1/contents/1/terms/sync
{
    "term_ids": [1, 3, 7]
}
```

### 12.6 Translations

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/translations/link` | Collega due traduzioni | `link` |
| `DELETE` | `/translations/{translation}` | Scollega una traduzione | `unlink` |
| `GET` | `/translations/{type}/{id}` | Lista traduzioni di un'entità | `read` |
| `GET` | `/translations/{type}/{id}/locales` | Locali disponibili/mancanti | `read` |

Il parametro `{type}` accetta: `content`, `term`.

#### Esempio di link

```json
POST /api/v1/translations/link
{
    "source_type": "content",
    "source_id": 1,
    "source_locale": "it",
    "target_type": "content",
    "target_id": 5,
    "target_locale": "en"
}
```

#### Risposta locali

```json
GET /api/v1/translations/content/1/locales

{
    "data": {
        "available": ["it", "en"],
        "missing": []
    }
}
```

### 12.7 URL Aliases

| Metodo | Endpoint | Azione | Autorizzazione |
|--------|----------|--------|----------------|
| `POST` | `/url-aliases` | Crea un alias manualmente | — |
| `GET` | `/url-aliases/{urlAlias}` | Legge un alias | `read` |
| `PUT` | `/url-aliases/{urlAlias}` | Aggiorna un alias | `update` |
| `DELETE` | `/url-aliases/{urlAlias}` | Elimina un alias | `delete` |
| `GET` | `/url-aliases/for/{type}/{id}` | Alias di un'entità | — |

Il parametro `{type}` accetta: `content`, `term`, `content_type`.

#### Esempio di creazione

```json
POST /api/v1/url-aliases
{
    "uri": "/custom-url",
    "routable_type": "content",
    "routable_id": 1,
    "locale": "it",
    "is_canonical": false
}
```

### 12.8 Front Controller (Web)

| Metodo | Endpoint | Azione |
|--------|----------|--------|
| `GET` | `/{path}` | Risolve qualsiasi URL pubblico |

Questa è una route catch-all registrata con middleware `web` e `SetLocale`.

---

## 13. Pattern Architetturali

### Contract → Service → Controller

Ogni dominio segue il pattern:

```
Interface (Contract)  →  Service (Implementazione)  →  Controller (HTTP layer)
```

I controller non contengono mai logica di business: delegano interamente ai servizi iniettati tramite DI.

### Data Transfer Object (DTO) Pattern

Le request HTTP vengono mappate a DTO fortemente tipizzati prima di raggiungere il servizio:

```
HTTP Request → FormRequest (validazione) → DTO → Service → Model
```

I DTO sono configurabili tramite `data_transfer.php`, permettendo all'app host di sostituirli con versioni estese.

### Observer Pattern per Side-Effect

Le operazioni automatiche (creazione tabelle, generazione URL) sono gestite tramite Eloquent Observer, disaccoppiando la logica dal flusso CRUD principale:

| Observer | Modello | Side-effect |
|----------|---------|-------------|
| `ContentTypeObserver` | `ContentType` | Creazione/modifica/eliminazione tabelle e modelli dinamici |
| `ContentFieldObserver` | `ContentField` | Aggiunta/modifica/eliminazione colonne dinamiche |
| `ContentUrlAliasObserver` | `Content` | Generazione e aggiornamento URL alias |
| `TermUrlAliasObserver` | `Term` | Generazione e aggiornamento URL alias (con propagazione discendenti) |

### Morph Relations

Il package fa ampio uso di relazioni polimorfiche per massima flessibilità:

```
url_aliases.routable  →  Content | Term | ContentType
translations.translatable  →  Content | Term
termables  →  Content (e potenzialmente altri modelli)
```

---

## 14. Schema del Database

### Diagramma ER

```
┌──────────────┐       ┌──────────────────┐       ┌──────────────┐
│ content_types │───┐   │ content_type_    │   ┌───│  taxonomies  │
│              │   └──→│ taxonomy (pivot) │←──┘   │              │
│ id           │       │ content_type_id  │       │ id           │
│ type         │       │ taxonomy_id      │       │ name         │
│ label        │       │ is_required      │       │ label        │
│ editable     │       │ allow_multiple   │       │ is_hierarchi │
│ hierarchical │       └──────────────────┘       │ sort_order   │
│ sitemap      │                                   └──────┬───────┘
│ visible      │                                          │
│ order_no     │                                          │ HasMany
│ icon         │                                          ▼
└──────┬───────┘                                   ┌──────────────┐
       │ HasMany                                   │    terms     │
       ▼                                           │              │
┌──────────────┐                                   │ id           │
│content_fields│                                   │ taxonomy_id  │
│              │                                   │ parent_id    │
│ id           │                                   │ name         │
│ content_type │                                   │ slug         │
│ name         │                                   │ description  │
│ label        │                                   │ lang         │
│ type         │                                   │ sort_order   │
│ order_no     │                                   │ _lft, _rgt   │
│ configs      │                                   └──────┬───────┘
└──────────────┘                                          │
       │                                                  │
       │ HasMany                                          │
       ▼                                MorphToMany       │
┌──────────────┐     ┌──────────────┐  (termables)       │
│   contents   │────→│  termables   │←───────────────────┘
│              │     │              │
│ id           │     │ term_id      │
│ content_type │     │ termable_id  │
│ user_id      │     │ termable_type│
│ parent_id    │     └──────────────┘
│ status       │
│ title        │     ┌──────────────┐
│ content      │     │  {dynamic}   │    Tabella satellite
│ url          │────→│  es: pages   │    (generata da ContentTypeObserver)
│ abs_url      │     │              │
│ lang         │     │ id           │
│ editor_type  │     │ content_id   │ ← FK
│ show_in_menu │     │ {field_1}    │ ← colonne dinamiche
│ is_home      │     │ {field_2}    │
│ is_visible   │     │ ...          │
│ order_no     │     └──────────────┘
│ seo_params   │
│ social_params│
│ _lft, _rgt   │
└──────┬───────┘
       │
       │ MorphMany          MorphMany
       ├─────────────────────────────────────────┐
       ▼                                         ▼
┌──────────────────┐                    ┌──────────────────┐
│   url_aliases    │                    │  translations    │
│                  │                    │                  │
│ id               │                    │ id               │
│ uri              │                    │ translation_group│
│ routable_type    │ ← Content|Term|    │ translatable_type│ ← Content|Term
│ routable_id      │    ContentType     │ translatable_id  │
│ locale           │                    │ locale           │
│ is_canonical     │                    └──────────────────┘
│ redirect_to      │
└──────────────────┘
```

### Lista Migrazioni (in ordine)

| Migrazione | Tabella |
|-----------|---------|
| `000013` | `content_types` |
| `000014` | `content_fields` |
| `000015` | `contents` |
| `000016` | `taxonomies` |
| `000017` | `terms` |
| `000018` | `termables` (pivot polimorfica) |
| `000019` | `content_type_taxonomy` (pivot) |
| `000020` | `translations` |
| `000021` | Aggiunge nested set a `terms` (`_lft`, `_rgt`) |
| `000022` | `url_aliases` |
| `000023` | Aggiunge nested set a `contents` |

---

## Appendice A — Variabili d'Ambiente

| Variabile | Default | Descrizione |
|-----------|---------|-------------|
| `FACTOTUM_SITEMAP_ENABLED` | `true` | Abilita/disabilita la generazione sitemap |
| `FACTOTUM_SITEMAP_BASE_URL` | `APP_URL` | Base URL per i link nella sitemap |
| `FACTOTUM_SITEMAP_PING_GOOGLE` | `true` | Ping Google dopo generazione |
| `FACTOTUM_SITEMAP_PING_BING` | `true` | Ping Bing dopo generazione |

## Appendice B — Comandi Artisan

| Comando | Descrizione |
|---------|-------------|
| `factotum-cms:install` | Installa il modulo CMS (migrazioni, seeder, provider) |
| `factotum-cms:generate-sitemap [--sync]` | Genera i file sitemap XML |

