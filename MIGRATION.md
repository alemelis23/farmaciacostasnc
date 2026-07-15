# MIGRATION — da Hello Elementor/Elementor al nuovo sito

Procedura prudente: **non disattivare né eliminare Elementor all'inizio**.
Il nuovo tema e il nuovo plugin convivono con l'esistente finché tutto è verificato.

## 0. Prerequisiti

- Accesso admin WordPress, accesso FTP/SSH o pannello hosting.
- Un ambiente di **staging** (copia del sito). Non lavorare direttamente in produzione.
- PHP ≥ 8.0 e WordPress ≥ 6.5 sull'hosting (verifica in Salute del sito).
- Questo repository.

## 1. Backup (obbligatorio, prima di tutto)

1. Backup completo file (`wp-content` incluso) e database.
2. Esporta i contenuti anche da **Strumenti → Esporta** (XML) come seconda rete di sicurezza.
3. Scarica la cartella `wp-content/uploads` (servirà per recuperare le fotografie reali).
4. Verifica che il backup sia ripristinabile prima di continuare.

## 2. Inventario del sito Elementor (in staging)

1. Elenca le pagine esistenti (Pagine → Tutte): confronta con `CONTENT-AUDIT.md` §2.
   Pagine attese: Home, Chi siamo, I nostri servizi, Ricetta in farmacia,
   Contattaci, Lavora con noi, Tamponi (+ eventuali pagine privacy).
2. Per ogni pagina annota: slug, title/description SEO, immagini usate, moduli presenti.
3. Salva una copia PDF/HTML di ogni pagina (stampa da browser) come riferimento visivo.
4. Verifica i plugin attivi (SEO, cache, moduli, cookie) e annota le impostazioni.

## 3. Installazione del nuovo codice (in staging)

1. Copia `wp-content/themes/farmacia-costa-2026` e `wp-content/plugins/farmacia-costa-core`.
2. Attiva il plugin `Farmacia Costa Core` (il tema resta quello vecchio per ora:
   il plugin non tocca il frontend Elementor).
3. Compila **Farmacia → Dati della farmacia**: telefono, WhatsApp, email,
   indirizzo, orari (spunta "Orari verificati" solo dopo conferma del titolare),
   social, coordinate (da Google Maps, tasto destro sul punto → copia coordinate).

## 4. Migrazione dei contenuti in Gutenberg

1. Crea/aggiorna le pagine con gli slug storici (`chi-siamo`, `contattaci`,
   `ricetta-in-farmacia`, `lavora-con-noi`) **come contenuto a blocchi**:
   copia i testi dalla copia salvata al punto 2, NON il markup Elementor.
2. Crea i servizi in **Servizi** (uno per servizio realmente attivo, vedi
   `CONTENT-AUDIT.md` §3): titolo, contenuto, descrizione breve, categoria,
   prenotazione, durata/prezzo solo se confermati, spunta "Servizio attivo".
3. Crea i membri del team in **Team** con fotografie autorizzate.
4. Carica le fotografie reali (recuperate da `uploads` o nuove) e sostituisci
   i placeholder (vedi `EDITOR-GUIDE.md` §7).
5. Pagina articoli: crea la pagina `consigli` e impostala in
   Impostazioni → Lettura → Pagina articoli. Homepage: pagina `home` statica.

## 5. Attivazione del nuovo tema (in staging)

1. Attiva `Farmacia Costa 2026`.
2. Impostazioni → Permalink → "Nome articolo" → Salva (rigenera le regole).
3. Svuota ogni cache (plugin cache, cache server/hosting, CDN).

## 6. Verifiche in staging

- [ ] Ogni URL storico risponde 200 (vedi tabella redirect in `CONTENT-AUDIT.md` §9)
- [ ] `/tamponi/` risponde 301 verso `/i-nostri-servizi/`
- [ ] Home, servizi, dettaglio servizio, chi siamo, contatti, 404 corretti su mobile e desktop
- [ ] Telefono e WhatsApp cliccabili e con numero corretto
- [ ] Form contatti: invio valido + email ricevuta (configura un plugin SMTP se
      l'hosting non consegna le email di `wp_mail`)
- [ ] Title/description corretti (se usi un plugin SEO, verifica che non ci siano
      duplicazioni di JSON-LD: in tal caso disattiva lo schema del plugin SEO oppure
      usa il filtro `fcc_schema_enabled`)
- [ ] Test dati strutturati con https://validator.schema.org/
- [ ] Nessun contenuto contiene ancora shortcode/markup Elementor:
      cerca in Bacheca → Ricerca o via SQL: `SELECT ID FROM wp_posts WHERE
      post_content LIKE '%elementor%' AND post_status='publish';`

## 7. Pubblicazione in produzione

1. Nuovo backup completo di produzione.
2. Ripeti i passi 3–5 in produzione (o promuovi lo staging secondo l'hosting).
3. Svuota cache e rigenera i permalink.
4. Verifica subito: home, un servizio, contatti, form, 404, redirect `/tamponi/`.
5. Invia la sitemap (`/wp-sitemap.xml`) in Google Search Console e monitora
   Copertura/404 per 2–4 settimane.

## 8. Dismissione di Elementor (solo alla fine)

Quando il nuovo sito è attivo e verificato da alcuni giorni:

1. Verifica ancora l'assenza di shortcode Elementor nei contenuti (punto 6).
2. **Disattiva** Elementor, Elementor Pro e Hello Elementor. Naviga il sito: nulla deve cambiare.
3. Dopo un'ulteriore verifica (e con backup fresco), **elimina** i plugin e il tema
   Hello Elementor. L'eliminazione definitiva richiede l'autorizzazione del titolare.
4. Le revisioni/meta di Elementor rimaste nel database possono essere pulite in
   seguito (`_elementor_*` in `wp_postmeta`): non è urgente e va fatto con backup.

## 9. Rollback

Se qualcosa non va dopo la pubblicazione:

1. Aspetto grafico: riattiva il tema precedente (Aspetto → Temi) — i contenuti
   Elementor tornano visibili perché non sono stati cancellati.
2. Problema più ampio: ripristina il backup completo (file + database) del punto 7.1.
3. Il plugin `farmacia-costa-core` può restare attivo in entrambi i casi:
   non interferisce con Elementor.

## Note

- Non modificare mai la produzione senza backup recente.
- Non eliminare pagine vecchie: gli slug storici sono riutilizzati dal nuovo sito.
- L'upload di ricette NON è stato implementato per ragioni GDPR (dati di categorie
  particolari): il flusso è informativo con contatto WhatsApp/telefono neutro.
  Se si vorrà attivarlo servirà una soluzione dedicata (storage cifrato non pubblico,
  retention, DPIA) approvata da un consulente privacy.
