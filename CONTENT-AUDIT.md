# CONTENT-AUDIT — Farmacia Costa Carbonia

Data audit: 15 luglio 2026
Sito analizzato: https://www.farmaciacostacarbonia.com/

## Metodologia e limiti (dichiarazione di trasparenza)

L'ambiente di lavoro remoto applica una policy di rete che **blocca l'accesso HTTP diretto**
al dominio `farmaciacostacarbonia.com` e a `web.archive.org` (errore `CONNECT 403` dal proxy,
verificato con `curl` e con lo strumento di fetch). Non è quindi stato possibile scaricare
l'HTML delle pagine, i media WordPress, i metadati SEO esatti (title/description verbatim),
né verificare markup Elementor, dati strutturati, performance o immagini del sito attuale.

L'audit è stato ricostruito da **fonti realmente consultate**:

- indice dei motori di ricerca (query `site:farmaciacostacarbonia.com` e query mirate),
  che restituisce titoli di pagina e estratti testuali indicizzati del sito stesso;
- Regione Sardegna — portale TS-CNS (sportello di attivazione presso Farmacia Costa);
- directory pubbliche (PagineGialle, PagineBianche, farmaciediturno.org, prontofarmacie.it);
- profili social pubblici (Facebook, Instagram).

Ogni voce riporta il livello di affidabilità. **Le voci contrassegnate "da verificare"
non devono essere pubblicate senza conferma del titolare.**

## 1. Stack tecnico dichiarato

| Voce | Stato | Fonte |
|---|---|---|
| CMS WordPress | Confermato dal committente | Brief di progetto |
| Tema Hello Elementor + Elementor | Confermato dal committente | Brief di progetto |
| Versioni WP/PHP/plugin in produzione | **Non verificabile da questo ambiente** | — |
| Dati strutturati esistenti | **Non verificabile** | — |
| Performance/Lighthouse attuali | **Non verificabile** | — |

## 2. Pagine indicizzate (inventario URL)

| URL | Titolo indicizzato | Stato contenuto |
|---|---|---|
| `/` | Farmacia Costa – La tua farmacia a Carbonia | Da conservare (riscritto) |
| `/chi-siamo/` | Chi siamo – Farmacia Costa | Da conservare (riscritto) |
| `/i-nostri-servizi/` | I nostri servizi – Farmacia Costa | Da conservare (ristrutturato in archivio servizi) |
| `/ricetta-in-farmacia/` | Ricetta in farmacia – Farmacia Costa | Da conservare (riprogettato, vedi §6) |
| `/contattaci/` | Contattaci – Farmacia Costa | Da conservare (riscritto) |
| `/lavora-con-noi/` | Lavora con noi – Farmacia Costa | Da conservare (riscritto) |
| `/tamponi/` | Tamponi – Farmacia Costa | **Obsoleto** (vedi §5) |

Non risultano indicizzati: blog/articoli, pagine privacy/cookie policy, pagine di dettaglio
per singolo servizio. Se in produzione esistono altre pagine (bozze, landing, privacy),
vanno inventariate con accesso reale a WP prima della migrazione (vedi `MIGRATION.md`).

## 3. Contenuti confermati da conservare

### Identità
- Prima farmacia aperta a **Carbonia nel 1938**, anno di fondazione della città (fonte: pagina "Chi siamo" indicizzata).
- Farmacia **tramandata di generazione in generazione**, oggi gestita dai **tre fratelli Costa: Alberto, Andrea ed Enzo** (fonte: "Chi siamo" indicizzata + directory).
- Ragione sociale apparente: "Farmacia Costa SNC" (handle Facebook `farmaciacostaSNC`, nome repo). **P.IVA e dati societari non reperiti: non inventati, da fornire.**

### NAP (Name, Address, Phone)
- Indirizzo: **Piazza Matteotti, 5 — 09013 Carbonia (SU)**, vicino alla chiesa di San Ponziano e al Comune (fonti: sito indicizzato, Regione Sardegna TS-CNS, directory — coerenti tra loro).
- Telefono: **0781 61840** (confermato anche dal committente).
- Email: **farmaciacostabanco@gmail.com** (sito indicizzato + Instagram). *Da confermare come destinataria del form.*

### Orari (⚠ da verificare prima della pubblicazione)
Fonti indicizzate (sito + directory) riportano:
- Mattino: lunedì–sabato 8:30–13:00
- Pomeriggio: lunedì–venerdì 16:30–20:30
- Sabato pomeriggio e domenica: chiuso

⚠ Un post Instagram del 23 settembre (2024) annuncia un **cambio di orario** il cui contenuto
completo non è leggibile da questo ambiente. Gli orari NON sono stati pubblicati come
definitivi: nel nuovo sito sono gestiti da un'unica impostazione centrale e la pagina
li mostra solo dopo conferma (flag "orari verificati").

### Servizi risultanti dal sito attuale
| Servizio | Dettagli indicizzati | Stato |
|---|---|---|
| Telemedicina (partner **HTN**) | ECG, Holter, refertazione cardiologo a distanza | Da confermare attivo |
| Elettrocardiogramma (ECG) | Eseguibile giornalmente, refertato tramite telemedicina | Da confermare |
| Holter cardiaco (ECG 24h) | — | Da confermare |
| Holter pressorio 24h | Misurazione ogni 15 minuti, elettrodi applicati dal farmacista | Da confermare |
| Misurazione pressione | Gratuita (fonte directory) | Da confermare (soprattutto la gratuità) |
| Glicemia | Autoanalisi eseguita dal farmacista | Da confermare |
| Colesterolo totale | Analisi sangue capillare | Da confermare |
| Trigliceridi | — | Da confermare |
| Consulenza audioprotesica | In collaborazione con audioprotesista, vendita apparecchi acustici | Da confermare |
| Attivazione TS-CNS | Sportello ufficiale Regione Sardegna presso la farmacia | Confermato da fonte regionale (tscns.regione.sardegna.it) |
| Ricetta in farmacia | Invio dati, preparazione farmaci, ritiro in sede; scelta originale/equivalente | Da confermare modalità attuale |
| Tamponi antigenici Covid-19 | Vedi §5 | **Presunto obsoleto** |

### Prodotti/reparti citati
Farmaci con ricetta, farmaci da banco, integratori, omeopatici, cosmetici, prodotti per l'infanzia.

### Social
- Facebook: https://www.facebook.com/farmaciacostaSNC/
- Instagram: https://www.instagram.com/farmacia_costa_carbonia/

## 4. Contenuti duplicati / criticità del sito attuale

- I servizi sono descritti in un'unica pagina "I nostri servizi": nessuna pagina dedicata
  per servizio → scarsa visibilità per ricerche locali specifiche ("Holter ECG Carbonia").
- La homepage indicizzata mostra in evidenza contenuto sui tamponi Covid (estratto indicizzato
  della home riguarda le certificazioni dei tamponi): contenuto obsoleto in posizione primaria.
- Nessuna pagina privacy/cookie indicizzata: da verificare l'esistenza in produzione.
- Errori grammaticali puntuali: non verificabili senza accesso all'HTML; il testo indicizzato
  della pagina tamponi contiene una concordanza errata ("Una copia del referto viene
  rilasciato") — tutti i testi sono comunque riscritti da zero.

## 5. Contenuti obsoleti

**`/tamponi/` (tamponi antigenici Covid-19).** Il testo indicizzato descrive screening
regionale, comunicazione ad ATS, detrazione fiscale: procedura del periodo emergenziale.
Nel 2026 il servizio, se ancora attivo, ha modalità diverse.

Decisione applicata nel nuovo progetto:
- il servizio NON è pubblicato tra i servizi attivi;
- l'URL `/tamponi/` è reindirizzato con **301** a `/i-nostri-servizi/` (redirect più
  pertinente disponibile; se il servizio verrà confermato attivo, creare la scheda
  servizio aggiornata e puntare il redirect lì);
- nessun riferimento Covid in homepage.

## 6. Ricetta in farmacia — nota privacy

Il flusso attuale indicizzato prevede l'invio di dati via form ("compila i campi richiesti").
Numero di ricetta / dati sanitari inviati con un form generico costituiscono dati di categorie
particolari (art. 9 GDPR). Nel nuovo sito il percorso è **informativo in 3 passaggi** con CTA
WhatsApp/telefono a messaggio neutro; **nessun upload di ricette o dati clinici** finché non
sarà definita una procedura sicura e approvata (vedi `MIGRATION.md` e riepilogo finale).

## 7. Immagini

Non è stato possibile scaricare i media del sito (rete bloccata). Nessuna fotografia reale
della farmacia o del team è quindi presente nel progetto. Il tema usa **placeholder SVG
chiaramente marcati** (`assets/img/placeholder-*.svg`, commentati nel codice). Prima del
lancio: esportare i media da WordPress (`Strumenti → Esporta` o FTP `wp-content/uploads`)
e sostituire i placeholder con fotografie reali (vedi `EDITOR-GUIDE.md`).

## 8. Metadati SEO attuali

Title indicizzati (formato "Pagina – Farmacia Costa"); meta description verbatim non
recuperabili da questo ambiente. I nuovi metadati sono definiti da zero, unici per pagina.

## 9. Mappa redirect

| Vecchio URL | Nuovo URL | Tipo | Motivo |
|---|---|---|---|
| `/` | `/` | — | invariato |
| `/chi-siamo/` | `/chi-siamo/` | — | slug conservato |
| `/i-nostri-servizi/` | `/i-nostri-servizi/` | — | slug conservato (diventa archivio servizi) |
| `/ricetta-in-farmacia/` | `/ricetta-in-farmacia/` | — | slug conservato |
| `/contattaci/` | `/contattaci/` | — | slug conservato |
| `/lavora-con-noi/` | `/lavora-con-noi/` | — | slug conservato |
| `/tamponi/` | `/i-nostri-servizi/` | **301** | servizio obsoleto (§5) |
| `/servizi/<slug>/` | — | nuovo | pagine di dettaglio servizio (nuove) |

Nessun vecchio URL viene rediretto in massa alla homepage. Il redirect `/tamponi/` è
implementato nel plugin `farmacia-costa-core` (filtro modificabile).

## 10. Dati da confermare con il titolare (riepilogo)

1. Orari definitivi (incluso eventuale cambio annunciato su Instagram) ed eventuali chiusure.
2. Elenco dei servizi realmente attivi oggi, con durata, necessità di prenotazione e prezzi.
3. Gratuità della misurazione della pressione.
4. Stato del servizio tamponi (attivo/dismesso).
5. Email destinataria del form (l'attuale è un indirizzo Gmail "banco").
6. Membri attuali del team, ruoli e fotografie autorizzate.
7. Fotografie reali (esterno, interno, banco, team, foto storiche per la timeline).
8. Dati societari per il footer: ragione sociale esatta, P.IVA, eventuale PEC/REA.
9. Testi legali: privacy policy, cookie policy (da consulente privacy).
10. Coordinate geografiche esatte (impostazione presente, precompilata sull'indirizzo, da verificare su Google Maps).
11. Modalità desiderata per la gestione ricette (solo WhatsApp/telefono o procedura sicura dedicata).
