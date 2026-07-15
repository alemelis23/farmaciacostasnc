# Farmacia Costa Carbonia — nuovo sito

Rebranding e ricostruzione completa del sito https://www.farmaciacostacarbonia.com/
Concept: **"La farmacia di Carbonia, dal 1938 verso il futuro"**.

Il vecchio sito (Hello Elementor + Elementor) è sostituito da un **block theme
nativo** e un **plugin dedicato**: nessun page builder, nessuna dipendenza che
renda il sito inutilizzabile disattivando un plugin visuale.

## Architettura

```
wp-content/
├── themes/farmacia-costa-2026/     ← presentazione (block theme FSE)
│   ├── style.css                   ← header del tema
│   ├── theme.json                  ← design token: palette, tipografia, spaziature
│   ├── functions.php               ← enqueue asset, icone SVG, card servizio, barra mobile
│   ├── templates/                  ← template HTML nativi (front-page, page-*, archive, single, 404…)
│   ├── parts/                      ← header e footer (template part → pattern dinamici)
│   ├── patterns/                   ← pattern PHP dinamici (hero, finder, storia, team, FAQ, contatti…)
│   └── assets/
│       ├── css/main.css            ← design system, componenti, responsive, reduced-motion
│       ├── js/main.js              ← nav, reveal, mappa a consenso, form fetch (vanilla)
│       ├── js/service-filter.js    ← ricerca+filtro servizi senza reload
│       ├── fonts/                  ← Fraunces + Inter variabili, WOFF2 locali (licenze OFL incluse)
│       └── img/                    ← placeholder SVG chiaramente marcati
└── plugins/farmacia-costa-core/    ← dati e funzionalità permanenti (sopravvivono al cambio tema)
    ├── farmacia-costa-core.php     ← bootstrap + attivazione
    └── includes/
        ├── helpers.php             ← fcc_get(), fcc_phone_href(), fcc_whatsapp_url(), orari…
        ├── class-fcc-settings.php  ← pagina admin "Farmacia": dati aziendali centralizzati
        ├── class-fcc-cpt.php       ← CPT Servizi (+categorie), CPT Team, meta box
        ├── class-fcc-form.php      ← form contatti: nonce, honeypot, rate limit, wp_mail
        ├── class-fcc-schema.php    ← JSON-LD centralizzato (Pharmacy, Service, FAQPage…)
        ├── class-fcc-seo.php       ← meta description, canonical archivi, Open Graph
        └── class-fcc-redirects.php ← redirect 301 (es. /tamponi/)
```

**Principio:** la presentazione appartiene al tema; i dati (telefono, orari,
servizi, team) e le funzionalità permanenti appartengono al plugin.

## Requisiti

- WordPress ≥ 6.5 (testato su 7.0.1)
- PHP ≥ 8.0 (testato su 8.4)
- Nessuna dipendenza esterna: niente jQuery, Bootstrap, icon font, CDN, Google Fonts remoti.

## Installazione

1. Copia `wp-content/themes/farmacia-costa-2026` e
   `wp-content/plugins/farmacia-costa-core` nella `wp-content` del sito.
2. Attiva **prima il plugin** (crea le categorie servizio e i redirect),
   poi il tema.
3. Vai su **Farmacia** (menu admin) e verifica/completa i dati aziendali.
   Gli orari NON vengono pubblicati finché non spunti "Orari verificati".
4. Imposta i permalink su "Nome articolo" (`/%postname%/`) e salva.
5. Crea le pagine con questi slug (i template si agganciano da soli):
   `home` (impostata come homepage statica), `chi-siamo`, `contattaci`,
   `ricetta-in-farmacia`, `lavora-con-noi`, `consigli` (pagina articoli),
   `cookie-policy`, più la pagina Privacy (Impostazioni → Privacy).
6. Inserisci i servizi in **Servizi** e le persone in **Team**.

La procedura completa di migrazione dal sito Elementor è in `MIGRATION.md`.

## Sviluppo locale e build

Non esiste una pipeline di build: CSS e JavaScript sono scritti a mano,
già pronti e serviti così come sono. Il sito funziona senza alcun processo
Node sul server.

Per l'ambiente di test locale usato in sviluppo (WordPress via Composer +
plugin SQLite + PHP built-in server) vedi `TEST-REPORT.md` §Ambiente.

I font provengono dai pacchetti npm `@fontsource-variable/fraunces` e
`@fontsource-variable/inter` (licenza OFL, file di licenza inclusi in
`assets/fonts/`); per aggiornarli basta sostituire i `.woff2`.

## Scelte tecniche principali

- **Block theme FSE** con `theme.json` v2: palette (contrasti verificati AA),
  tipografia fluida (`clamp`), spaziature come token.
- **Pattern PHP dinamici**: header, footer, hero, contatti leggono i dati
  dal plugin a ogni richiesta; si aggiornano ovunque cambiando le impostazioni.
- **Dati centralizzati**: un'unica pagina admin per telefono, WhatsApp, email,
  indirizzo, orari, social, messaggio WhatsApp predefinito.
- **WhatsApp**: link `https://wa.me/39078161840` con testo pre-compilato
  codificato e contestuale per servizio; mai dati sanitari nel messaggio.
- **Privacy by design**: mappa Google caricata solo dopo clic esplicito;
  nessun tracker; form con minimizzazione dei dati e senza salvataggio in DB;
  nessun upload di ricette (vedi `CONTENT-AUDIT.md` §6).
- **Menu mobile senza JS**: disclosure nativa `details/summary`, migliorata
  da JavaScript (Escape, clic esterno). Tutte le informazioni essenziali
  funzionano senza JavaScript.
- **Sicurezza form**: nonce, honeypot, trappola temporale, rate limiting per
  IP (hash), sanitizzazione/escaping, errori senza dati personali nei log,
  destinatario mai esposto nel markup. Compatibile con plugin SMTP.
- **SEO/AEO**: title e description unici, canonical, Open Graph, sitemap core,
  JSON-LD centralizzato (`Pharmacy`, `Service`, `FAQPage` solo con FAQ visibili,
  `BreadcrumbList`); si disattiva da solo se rileva Yoast/Rank Math/SEOPress/AIOSEO
  (filtro `fcc_schema_enabled` per lo schema).
- **Redirect 301**: mappa in `class-fcc-redirects.php` (filtro `fcc_redirects`);
  slug storici conservati, `/tamponi/` → `/i-nostri-servizi/`.

## Integrazione CMP (cookie)

Il footer contiene il link "Gestisci preferenze cookie" con classe
`fc-manage-cookies`: la CMP scelta deve agganciarsi a quel selettore.
Finché non c'è una CMP il sito non carica alcun servizio terzo prima del
consenso (l'unica risorsa esterna possibile è la mappa, attivata su clic).

## Aggiornamenti

- Tema e plugin sono versionati in questo repository: aggiorna via Git/deploy.
- WordPress core e eventuali plugin terzi: dalla bacheca, previo backup.
- Verifica dopo ogni aggiornamento: home, un servizio, contatti, invio form.

## Documentazione

- `CONTENT-AUDIT.md` — audit del sito precedente, redirect, dati da confermare
- `MIGRATION.md` — procedura completa di migrazione da Elementor e rollback
- `EDITOR-GUIDE.md` — guida in italiano per il personale della farmacia
- `TEST-REPORT.md` — tutti i test eseguiti con comandi e risultati
