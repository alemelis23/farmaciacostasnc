# TEST-REPORT — Farmacia Costa 2026

Data: 15 luglio 2026 · Ambiente: container Linux, PHP 8.4.19, Node 22, Chromium (Playwright).

## Ambiente di test

Il sito è stato **eseguito realmente** in locale:

- WordPress **7.0.1** installato via Composer (`roots/wordpress-no-content`, packagist);
- database: plugin ufficiale `sqlite-database-integration` (drop-in `db.php`);
- server: `php -S 127.0.0.1:8080` con router per i permalink;
- tema e plugin del repository collegati via symlink in `wp-content`;
- contenuti di prova creati da `seed.php` (pagine, 9 servizi, 3 membri team, dati audit);
- email intercettate da un mu-plugin di test (`pre_wp_mail` → file di log).

Limite dell'ambiente: la rete del container blocca il sito di produzione e
wordpress.org (irrilevante per i test locali; le uniche voci nel `debug.log`
sono i tentativi di WordPress di contattare wordpress.org).

## 1. Lint e sintassi

| Test | Comando | Risultato |
|---|---|---|
| PHP lint plugin (8 file) | `php -l` su ogni file | ✅ nessun errore |
| PHP lint tema (10 pattern + functions) | `php -l` su ogni file | ✅ nessun errore |
| Sintassi JavaScript | `node --check main.js service-filter.js` | ✅ OK |
| Errori PHP runtime | `wp-content/debug.log` con `WP_DEBUG` attivo dopo l'intera suite | ✅ 0 errori applicativi |

## 2. Suite browser Playwright (Chromium) — `test-browser.js`

**22/22 test superati** (output integrale in `scratchpad/test-results.json` di sessione):

- **Responsive**: 8 pagine × 9 larghezze (320, 360, 390, 430, 768, 1024, 1280, 1440, 1920 px) → nessuno scroll orizzontale. ✅
- Skip link presente e **primo elemento raggiunto con Tab**. ✅
- **Menu mobile** (details/summary): apertura, chiusura con Escape. ✅
- **Barra mobile inferiore**: visibile, 3 azioni (Chiama, WhatsApp, Indicazioni). ✅
- **Filtro servizi**: ricerca "pressione" (2/9 card), categoria Telemedicina (3 card, `aria-pressed`), stato `aria-live` ("3 servizi trovati"), messaggio nessun risultato + reset, stato condivisibile nell'URL (`?q=…&cat=…`). ✅
- **Mappa**: nessun iframe Google prima del clic; caricata dopo "Carica la mappa". ✅
- **prefers-reduced-motion**: contenuti visibili senza animazioni. ✅
- **FAQ**: apertura con clic e tastiera. ✅
- **Form (con JS, via fetch)**: invio valido → conferma accessibile con focus + email registrata; email non valida → errore specifico; honeypot compilato → finto successo senza email; avviso privacy mostrato scegliendo "Invio ricetta". ✅
- **Link**: 13 link `tel:+39078161840`; 10 link `https://wa.me/39078161840` con testo codificato (`text=Buongiorno%2C…`); messaggio WhatsApp contestuale nella pagina servizio ("…sul servizio Glicemia."). ✅
- **Console browser**: 0 errori su tutte le pagine (escluso lo status 404 del documento della pagina 404, che è intenzionale). ✅

## 3. Form senza JavaScript e sicurezza — `test-form-nojs.sh` (curl)

**8/8 superati**:

1. invio valido no-JS → redirect `fcc_status=ok` ✅
2. campi obbligatori mancanti → riepilogo errori specifici ✅ + form ripopolato ✅
3. consenso privacy assente → errore dedicato ✅
4. nonce non valido → richiesta rifiutata ✅
5. trappola temporale (invio < 3 s) → finto ok, nessuna email ✅
6. 7 invii ripetuti → rate limiting dal 6° con messaggio dedicato ✅
7. errore del sistema email (simulato) → messaggio con alternativa telefonica ✅
8. flusso senza JavaScript end-to-end (curl non esegue JS) ✅

## 4. Funzionamento senza JavaScript (Playwright, JS disattivato)

- Contenuti tutti visibili (nessun reveal bloccato) ✅
- Menu mobile apribile (details nativo) ✅
- Link tel (13), WhatsApp (10), indicazioni (5) presenti e funzionanti ✅
- Form presente e inviabile; link alternativo "Apri in Google Maps" ✅

## 5. Accessibilità — axe-core 4.x (WCAG 2A/AA, 2.1 AA, 2.2 AA)

8 pagine × 2 larghezze (390, 1280): **0 violazioni** dopo la correzione del
contrasto del kicker sulla sezione verde scuro (ora #E8B49A su #0C332E = 7,47:1).
Contrasti della palette verificati anche a mano (minimo in uso: 5,30:1).

## 6. Lighthouse (mobile, Chromium headless)

| Pagina | Performance | Accessibility | Best Practices | SEO | LCP | CLS | TBT |
|---|---|---|---|---|---|---|---|
| Home | 98 | 100 | 100 | 100 | 2,1 s | 0,019 | 0 ms |
| /i-nostri-servizi/ | 98 | 100 | 100 | 100 | 2,0 s | 0,015 | — |
| /servizi/holter-pressorio/ | 98 | 100 | 100 | 100 | 1,8 s | 0,049 | — |
| /contattaci/ | 98 | 100 | 100 | 100 | 2,0 s | 0 | — |

Nota: misure su server locale; in produzione dipenderanno dall'hosting
(caching consigliato in `MIGRATION.md`). INP non misurabile in lab
(TBT 0 ms come proxy).

## 7. SEO, redirect, dati strutturati

- `robots.txt` con riga `Sitemap:`; `/wp-sitemap.xml` → 200 ✅
- `/tamponi/` → **301** → `/i-nostri-servizi/`; slug storici invariati → 200 ✅
- Pagina inesistente → 404 con template utile (ricerca, contatti) ✅
- JSON-LD **valido e parse-abile su tutte le 8 pagine**: `Pharmacy` (+`WebSite`)
  ovunque, `BreadcrumbList` sulle interne, `Service`+`FAQPage` sui servizi;
  niente orari nello schema finché non verificati; nessun `Review`/`AggregateRating` ✅
- Meta description unica per pagina (con fallback dal contenuto) ✅
- Crawl dei link interni delle 8 pagine: 0 link rotti
  (esclusi endpoint WP standard `xmlrpc.php` 405 e oembed con query string) ✅

## 8. Verifica visiva (screenshot)

Screenshot full-page a 390/768/1440 px di: home, servizi, dettaglio servizio,
chi siamo, contatti, ricetta, 404 (in `docs/screenshots/`). Difetti trovati e
**corretti** durante la verifica:

1. menu mobile fuori viewport a ≤390 px → header compatto (icone senza etichetta);
2. pulsante WhatsApp flottante visibile anche su mobile (ordine regole CSS) → corretto;
3. campo honeypot visibile (classe CSS errata `.fc-hp` → `.fcc-hp`) → corretto;
4. kicker terracotta su fondo scuro senza contrasto → accento sabbia;
5. sezioni `.fc-reveal` invisibili negli screenshot full-page → solo artefatto
   dell'IntersectionObserver durante la cattura, verificato ok con reduced-motion.

## 9. Bug funzionali trovati e corretti dai test

- `fetch(form.action)` rotto per **DOM clobbering** dell'input `name="action"`
  → `form.getAttribute('action')`.
- Meta description assente in home (`is_singular()` prima di `is_front_page()`).
- Link "Consigli" in menu poteva puntare a URL inesistente → ora usa la vera
  pagina articoli e compare solo se esiste con almeno un articolo.
- Sitemap disattivata da `blog_public=0` (impostazione d'installazione di test).

## Test non eseguibili in questo ambiente

- Test sull'ambiente di **produzione/staging reale** (rete bloccata verso il dominio).
- Consegna email reale (SMTP): testata la logica con interceptor; in produzione
  configurare un plugin SMTP e ritestare il form.
- Screen reader reali (NVDA/VoiceOver): eseguiti axe + test tastiera; consigliata
  una passata manuale prima del lancio.
- INP su utenti reali (richiede field data).
