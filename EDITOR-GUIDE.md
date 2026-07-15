# Guida per la redazione — Farmacia Costa

Questa guida spiega come aggiornare il sito **senza scrivere codice**.
Tutto si fa dalla bacheca di WordPress.

## 1. Modificare orari, telefono, WhatsApp e indirizzo

Menu **Farmacia** (icona con la croce) → *Dati della farmacia*.

- **Orari**: una riga per giorno, fasce separate da virgola.
  Esempio: `08:30-13:00, 16:30-20:30`. Lascia vuoto per "Chiuso".
- ⚠ Gli orari compaiono sul sito **solo** se la casella
  "**Orari verificati**" è spuntata. Finché non lo è, il sito invita a
  chiamare. Spuntala solo quando gli orari sono sicuramente corretti.
- **Telefono**: il campo "come mostrato" è quello che si legge (es. `0781 61840`),
  il campo "formato internazionale" è quello che fa partire la chiamata
  (`+39078161840`). Cambiali insieme.
- **WhatsApp**: solo cifre con prefisso, senza `+` (es. `39078161840`).
- **Chiusure straordinarie**: testo libero (es. "Chiusi il 15 agosto"),
  appare accanto agli orari.

Salvando, **tutto il sito si aggiorna da solo**: header, footer, contatti,
pulsanti e dati per Google.

## 2. Aggiungere o modificare un servizio

Menu **Servizi** → *Aggiungi servizio* (o apri un servizio esistente).

1. **Titolo**: il nome del servizio (es. "Holter pressorio 24 ore").
2. **Contenuto**: la spiegazione, scritta semplice. Usa i titoli "Intestazione 2"
   per le sezioni ("Come si svolge", "Per chi può essere utile").
3. **Dettagli del servizio** (riquadro sotto il contenuto):
   - *Descrizione breve*: 1–2 frasi per le card (massimo 200 caratteri);
   - *Prenotazione*: necessaria / consigliata / non necessaria;
   - *Durata* e *Prezzo*: compilali **solo se confermati**, altrimenti vuoti;
   - *Servizio attivo*: togli la spunta per nascondere il servizio dal sito
     senza cancellarlo;
   - *In evidenza in homepage*: per i servizi principali.
4. **FAQ del servizio**: domande e risposte specifiche (compaiono nella pagina
   del servizio e nei dati per Google).
5. **Categoria servizio** (colonna destra): scegline una
   (Telemedicina, Prevenzione, Autoanalisi, Consulenze, Servizi al cittadino).
6. **Immagine in evidenza**: facoltativa, usata nelle condivisioni social.

## 3. Modificare una FAQ generale

Le FAQ di homepage e pagina servizi sono nel tema: **Aspetto → Editor →
Pattern → Farmacia Costa → Domande frequenti**. Se preferisci, chiedi a chi
gestisce il sito: le FAQ dei singoli servizi invece si cambiano dal punto 2.4.

## 4. Cambiare un membro del team

Menu **Team**:

- *Titolo* = nome e cognome;
- riquadro *Dettagli*: ruolo (es. "Farmacista titolare"), area di competenza
  (solo se verificata) e una breve frase professionale **approvata dalla persona**;
- *Immagine in evidenza* = fotografia (quadrata, almeno 600×600 px, autorizzata).
- L'ordine si controlla con *Attributi* → Ordine (0, 1, 2…).

## 5. Pubblicare un articolo (Consigli e novità)

Menu **Articoli** → *Aggiungi articolo*. Titolo chiaro, paragrafi brevi,
un'immagine in evidenza. Quando c'è almeno un articolo pubblicato, la voce
"Consigli" compare automaticamente nel menu del sito.

Ricorda: niente promesse mediche ("cura", "risultato garantito") e sempre
l'invito a rivolgersi al medico per le decisioni sulla salute.

## 6. Modificare le pagine (Chi siamo, Contatti, Ricetta…)

Menu **Pagine**. Il testo introduttivo di ogni pagina è normale contenuto a
blocchi: modificalo come un documento. Le sezioni "di design" (storia, team,
mappa, form) sono automatiche: non serve toccarle.

## 7. Sostituire un'immagine segnaposto

Le immagini con la dicitura "Segnaposto" vanno sostituite con fotografie reali:

- **Team**: carica la foto come *Immagine in evidenza* della persona (punto 4).
- **Hero e storia** (homepage/chi siamo): sono nel tema — Aspetto → Editor →
  Pattern → "Hero homepage" e "Storia dal 1938": seleziona l'immagine e
  premi *Sostituisci*. Scrivi sempre il **testo alternativo** (cosa si vede
  nella foto, per chi usa lo screen reader).
- Formato consigliato: JPG/WebP, lato lungo 1600 px, peso sotto i 300 KB.

## 8. Verificare l'anteprima mobile

Prima di pubblicare qualsiasi modifica: in alto nell'editor premi l'icona
con lo schermo (Visualizza) → **Mobile**. Controlla che i testi non siano
tagliati e i pulsanti siano raggiungibili. Dopo la pubblicazione, apri la
pagina anche dal telefono.

## Cosa NON fare

- Non pubblicare prezzi, durate od orari non confermati.
- Non inserire dati sanitari dei clienti da nessuna parte (nemmeno nelle FAQ).
- Non cambiare i colori o le dimensioni dei testi nei singoli blocchi:
  la palette del tema garantisce leggibilità e coerenza.
- Non cancellare pagine: se un servizio non c'è più, togli la spunta
  "Servizio attivo" e avvisa chi gestisce il sito per l'eventuale redirect.
