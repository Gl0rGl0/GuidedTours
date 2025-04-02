# Portale Gestione Visite Guidate Volontarie

## Dati del Gruppo

*   Daniel Barbetti (Matricola: 740851)
*   Giorgio Felappi (Matricola: 740733)

## Breve Descrizione del Progetto

L'applicazione web mira a facilitare l'incontro tra la domanda e l'offerta di visite guidate organizzate da enti locali senza scopo di lucro, avvalendosi di guide volontarie. Il sistema gestisce l'intero ciclo di vita delle visite, dalla pianificazione alla partecipazione degli utenti.

### (i) Quali informazioni saranno trattate nell'applicazione web sviluppata?

L'applicazione tratterà le seguenti informazioni principali:

1.  **Ambito Territoriale:** L'area geografica di competenza dell'organizzazione (impostata una tantum).
2.  **Luoghi:** Dettagli sulle località visitabili (nome univoco, descrizione, indirizzo/coordinate), accessibili e all'interno dell'ambito territoriale.
3.  **Tipi di Visita:** Modelli per le visite associate a un luogo (titolo, descrizione, punto d'incontro, periodo e giorni di programmabilità, orario, durata, eventuale biglietto, numero min/max partecipanti).
4.  **Volontari:** Guide accreditate identificate da un nickname/username univoco, associate ai tipi di visita per cui sono idonee.
5.  **Disponibilità Volontari:** Dichiarazioni periodiche (mensili) dei volontari sulle date in cui possono guidare visite nel mese successivo.
6.  **Date Precluse:** Giornate specifiche indicate dal configuratore in cui nessuna visita può essere programmata.
7.  **Visite Specifiche:** Istanze pianificate di un tipo di visita per una data e ora specifiche, con un volontario assegnato e uno stato (proposta, completa, confermata, cancellata, effettuata).
8.  **Iscrizioni (Fruitori):** Prenotazioni effettuate dagli utenti per le visite proposte, includendo il numero di partecipanti e un codice di prenotazione univoco.
9.  **Utenti e Credenziali:** Dati di accesso (username, password hashata) per le tre categorie di utenti (Configuratore, Volontario, Fruitore) e il loro ruolo.
10. **Impostazioni Globali:** Parametri come il numero massimo di persone iscrivibili con una singola prenotazione.
11. **Archivio Storico:** Dati relativi alle visite concluse (effettuate).

### (ii) Quali azioni si prevedono sulle diverse unità informative?

*   **Ambito Territoriale:** Creazione (una tantum), Visualizzazione.
*   **Luoghi:** Creazione, Visualizzazione, Modifica, Rimozione (con effetti a cascata su Tipi di Visita e associazioni Volontari).
*   **Tipi di Visita:** Creazione, Visualizzazione, Modifica (associati a Luoghi), Rimozione (con effetti a cascata su associazioni Volontari e potenziale rimozione Luoghi/Volontari).
*   **Volontari:** Creazione (implicita o diretta), Visualizzazione (con Tipi di Visita associati), Rimozione (con effetti a cascata su associazioni e potenziale rimozione Tipi di Visita/Luoghi), Gestione Credenziali (cambio password).
*   **Disponibilità Volontari:** Creazione/Aggiornamento (mensile), Utilizzo per pianificazione, Cancellazione (implicita a fine ciclo).
*   **Date Precluse:** Creazione/Aggiornamento (mensile), Utilizzo per pianificazione, Cancellazione (implicita a fine ciclo).
*   **Visite Specifiche:** Creazione (automatica tramite pianificazione mensile), Visualizzazione (filtrata per stato/utente), Aggiornamento Stato (automatico in base a iscrizioni/tempo: Proposta -> Completa -> Proposta; Proposta/Completa -> Confermata/Cancellata; Confermata -> Effettuata; Cancellata -> Rimossa logicamente o archiviata).
*   **Iscrizioni (Fruitori):** Creazione (per visite proposte), Visualizzazione (proprie iscrizioni attive; elenco iscritti per Volontario su visite confermate), Cancellazione/Disdetta (per visite proposte).
*   **Utenti e Credenziali:** Creazione (registrazione per Fruitore; predefinite per Config./Volont.), Gestione Credenziali (cambio password).
*   **Impostazioni Globali:** Creazione/Modifica.
*   **Archivio Storico:** Creazione (automatica per visite effettuate), Visualizzazione.

### (iii) A quali categorie di utenti verrà dato l'accesso alle informazioni e per compiere quali azioni?

1.  **Configuratore (Accesso Back-end):**
    *   Gestisce l'intero setup: Ambito Territoriale, Luoghi, Tipi di Visita, Volontari (CRUD completo, soggetto a versione).
    *   Imposta le Date Precluse mensilmente.
    *   Avvia il processo di pianificazione mensile delle visite (da Versione 3).
    *   Visualizza tutte le visite in ogni stato (Proposta, Completa, Confermata, Cancellata, Effettuata/Archivio).
    *   Gestisce le Impostazioni Globali.
    *   Gestisce le proprie credenziali.
2.  **Volontario (Accesso Back-end):**
    *   Visualizza i Tipi di Visita a cui è assegnato.
    *   Inserisce/Modifica la propria disponibilità mensile.
    *   Visualizza le Visite Confermate a cui è assegnato come guida, incluso l'elenco dei codici di prenotazione e numero partecipanti per controllo (da Versione 4).
    *   Gestisce le proprie credenziali (cambio password).
3.  **Fruitore (Utente - Accesso Front-end):**
    *   Visualizza le Visite Proposte, Confermate e Cancellate.
    *   Effettua l'iscrizione a una Visita Proposta (creando una prenotazione).
    *   Visualizza le proprie iscrizioni attive.
    *   Disdice le proprie iscrizioni per Visite ancora nello stato di Proposta.
    *   Gestisce le proprie credenziali (registrazione e password).

## Setup e Avvio (Istruzioni Preliminari)

1.  **Database:**
    *   Assicurarsi di avere un server MySQL/MariaDB in esecuzione.
    *   Creare un database (es. `guided_tours_db`).
    *   Creare un utente dedicato per l'applicazione (es. `guided_tours_user` con password `guided_tours_password`) e garantirgli tutti i privilegi sul database creato.
    *   Eseguire lo script `database_schema.sql` per creare le tabelle.
    *   Eseguire lo script `database_seed.sql` per popolare le tabelle con dati di esempio (N.B.: le password di esempio sono hashate da 'password123').
2.  **Configurazione PHP:**
    *   Modificare `config/database.php` con le credenziali corrette del database, se diverse da quelle di esempio.
    *   Assicurarsi che l'estensione PHP `php-mysql` (o `phpX.Y-mysql`) sia installata e abilitata.
3.  **Avvio Server Sviluppo:**
    *   Navigare nella directory `guided_tours_web_app` da terminale.
    *   Eseguire il comando: `php -S localhost:8000`
    *   Aprire il browser all'indirizzo `http://localhost:8000`.
