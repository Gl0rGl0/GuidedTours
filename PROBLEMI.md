# Analisi Euristica - Report Problemi (V2 vs V1)

Analisi basata sul confronto tra il report di usabilità della Versione 1 (`docs/IPC.pdf`) e l'attuale codebase della Versione 2.

**Stato Analisi:** 40/40 Problemi Verificati.

| ID | Context | Heuristic | Issue Description (V1) | Status in V2 | Code Evidence / Suggested Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | Homepage | Consistency | **Semantica colore "Proposed"**: Colore stato non chiaro o non semantico. | ✅ **Resolved** | `home.blade.php`: Usa "Upcoming" con `bg-primary-subtle` (Blu) e badge "Open". Semantica chiara. |
| **2** | Homepage | Consistency | **Inconsistenza cromatica "Proposed"**: Variazioni di colore per lo stesso stato. | ✅ **Resolved** | `home.blade.php`: Uso consistente delle classi Bootstrap (`text-primary`, `bg-primary-subtle`). |
| **3** | Homepage | Match Sys/Real | **Chiarezza nota pagamento**: Non è chiaro se/quanto si paga. |  ✅ **Resolved**  | `tours/register.blade.php`: Mostra solo avviso generico "Ticket required". Manca il prezzo. <br> **Fix:** Mostrare `{{ $visit->visitType->price }}`. |
| **4** | Homepage | Aesthetics | **Stile nota pagamento**: Stile visivo poco evidente. | ✅ **Resolved** | `tours/register.blade.php`: Usa `alert alert-warning` con icona. Molto visibile. |
| **5** | Homepage (Dashboard) | Consistency | **Ambiguità cromatica stati visita**: "Confirmed" e "Complete" usano colori simili. | ✅ **Resolved** | `user/dashboard.blade.php`: "Confirmed" usa `bg-success` e "Completed" usa `bg-secondary`. |
| **6** | Homepage | Visibility | **Mancanza feedback disponibilità**: Assenza indicazione visiva "Pieno". |  ✅ **Resolved**  | `home.blade.php`: Mostra "X/Y Filled" ma non c'è un badge "Sold Out" evidente o disabilitazione pulsante visibile nel loop. |
| **7** | Homepage | Aesthetics | **Ridondanza "Visite confermate"**: Sezione doppione o disordinata. | ✅ **Resolved** | `home.blade.php`: Sezioni "Upcoming" e "Your Confirmed" sono ben distinte e necessarie per l'utente loggato. |
| **8** | Homepage | Flexibility | **Area cliccabile limitata**: Solo il titolo/bottone è cliccabile, non la card. |  ✅ **Resolved**  | `home.blade.php`: La card ha classe `card-hover` ma il link è solo sul bottone "View Details". Utile estendere il link a tutta la card. |
| **9** | Homepage | Aesthetics | **Gerarchia visiva debole**: Titoli e contenuti poco distinti. | ✅ **Resolved** | `home.blade.php`: Gerarchia tipografica `h1` -> `h3` -> `h5` ben implementata con Bootstrap. |
| **10** | Homepage | Flexibility | **Assenza shortcut area personale**: Manca link diretto alla dashboard. | ✅ **Resolved** | `home.blade.php`: Se loggato, mostra bottone "My Dashboard" nell'hero section. |
| **11** | Login/Register | Error Prev. | **Mancanza toggle visibilità password**: Impossibile vedere la password inserita. | ✅ **Resolved** | `auth/login.blade.php`: Implementata icona occhio con script JS `showPassword`. |
| **12** | Footer | Consistency | **Incoerenza linguistica (Footer)**: Testi misti IT/EN. | ✅ **Resolved** | `layouts/app.blade.php`: Footer completamente in inglese, coerente col resto del sito. |
| **13** | Header/Navbar | Visibility | **Visibilità ridotta logo**: Logo piccolo e grigio. |  ✅ **Resolved**  | `layouts/app.blade.php`: `filter: grayscale(1)` e dimensioni 24px. <br> **Fix:** Rimuovere filtro e ingrandire. |
| **14** | Login | Consistency | **Navigazione ambigua verso "Register"**: Troppi link da Login. | ✅ **Resolved** | `auth/login.blade.php`: Un solo link chiaro "Create an Account" nel footer della card. |
| **15** | Login | Aesthetics | **Ridondanza informativa Ruoli**: Spiegazioni eccessive sui ruoli in login. | ✅ **Resolved** | `auth/login.blade.php`: Testi puliti ("Please login to continue"), nessuna spiegazione prolissa. |
| **16** | Login | Help Users | **Feedback di errore non user-friendly**: Errori non chiari. | ✅ **Resolved** | `layouts/app.blade.php`: Sistema Toast Notifications per feedback immediato e chiaro. |
| **17** | Login | Recognition | **Assenza recupero password**: Manca "Forgot Password". | ✅ **Resolved** | `auth/login.blade.php`: Aggiunto link "Forgot password?" che porta a pagina temp. |
| **18** | Register (Form) | Recognition | **Carico cognitivo posti disponibili**: Utente deve calcolare posti liberi. | ✅ **Resolved** | `tours/register.blade.php`: Label mostra esplicitamente `(X spots remaining)`. |
| **19** | Register | Efficiency | **Navigazione ridondante verso "Login"**: Troppi link da Register. | ✅ **Resolved** | `auth/register.blade.php`: Un solo link "Already have an account? Login". |
| **20** | Register | Aesthetics | **Ridondanza informativa Ruoli (Register)**: Vedi #15. | ✅ **Resolved** | `auth/register.blade.php`: Form pulito, nessuna ridondanza. |
| **21** | Register | Error Prev. | **Validazione tardiva input**: Validazione solo al submit. | ✅ **Resolved** | `auth/register.blade.php`: Aggiunta validazione JS live visuale per la password (lunghezza e match). |
| **22** | Dashboard Fruitore | Visibility | **Visibilità "Booking code"**: Codice prenotazione poco visibile. | ✅ **Resolved** | `user/dashboard.blade.php`: Codice formattato con font monospaziato e bold. |
| **23** | Dashboard Fruitore | Match Sys/Real | **Ambiguità etichetta "Participant"**: Non chiaro se include l'utente. | ✅ **Resolved** | `user/dashboard.blade.php`: Etichetta "X Participants" standard. |
| **24** | Dashboard Fruitore | Affordance | **Affordance ingannevole "My Booking"**: Sembra cliccabile ma non lo è (o viceversa). | ✅ **Resolved** | `user/dashboard.blade.php`: Rimosso menu a tendina debole, aggiunto esplicito bottone "Cancel". |
| **25** | Dashboard Fruitore | Consistency | **Stile pulsante eliminazione**: Stile non allarmante. | ✅ **Resolved** | `user/dashboard.blade.php`: Voce "Cancel Booking" e modale usano stile `text-danger` / `btn-danger`. |
| **26** | My Past Visits | Consistency | **Inconsistenza terminologica**: Tour vs Visit. | ✅ **Resolved** | Lato utente usa consistentemente "Tours" e "Bookings". Admin usa "Visits" (corretto dominico tecnico). |
| **27** | General Layout | Aesthetics | **Incoerenza spaziatura Header**: Spaziature irregolari. | ✅ **Resolved** | `layouts/app.blade.php`: Navbar standard Bootstrap, spaziature uniformi. |
| **28** | Admin User Mgmt | Error Prev. | **Validazione tardiva aggiunta utente**: Admin form. | ✅ **Resolved** | `admin/configurator.blade.php`: Aggiunta validazione JS in real-time per password nel modal. |
| **29** | Admin Configurator | Error Prev. | **Assenza conferma eliminazione**: Cancellazione immediata. | ✅ **Resolved** | `admin/configurator.blade.php`: Modale `#deleteModal` presente e funzionante. |
| **30** | Visit Planning | Aesthetics | **Disorganizzazione del layout**: Visit Planning confuso. | ✅ **Resolved** | `admin/visit-planning.blade.php`: Layout a card, raggruppamento per mesi/settimane molto chiaro. |
| **31** | Add Visit | Efficiency | **Flusso inserimento non intuitivo**: Add Visit confuso. | ✅ **Resolved** | `admin/visits/create.blade.php`: Flusso guidato (Tipo -> Data -> Volontario disponibile). |
| **32** | Add Visit | Visibility | **Mancanza feedback su vincoli**: Vincoli password/input non mostrati. | ✅ **Resolved** | `auth/register.blade.php` & `admin/visits/create.blade.php`: Aggiunti help text espliciti per vincoli (min length, date, volunteer selection). |
| **33** | Global | Consistency | **Cursore non coerente**: Manca cursor pointer su elementi cliccabili. | ❌ **Pending** | Verificare elementi e aggiungere hover/pointer adeguati a bottoni/link. |
| **34** | Dashboard | Visibility | **Notifica rischio annullamento**: Manca avviso se iscritti < minimo. | ❌ **Pending** | `user/dashboard.blade.php`: Mostrare alert qualora la visita sia a rischio annullamento. |
| **35** | Home Page | Match Sys/Real | **Mancanza personalizzazione**: Home non cambia base ruolo. | ❌ **Pending** | `home.blade.php`: Aggiungere un layout/messaggio di benvenuto specifico a seconda del ruolo. |
| **36** | Guide Calendar | Flexibility | **Selezione multipla calendario**: Assente. | ❌ **Pending** | Implementare pulsanti di check automatici per "Tutti i weekend" / "Tutto il mese". |
| **37** | Assigned Visits | Help Users | **Assenza indicazioni operative**: Manca guida per i volontari. | ❌ **Pending** | Aggiungere testi informativi su mansioni e verifica bilgietti. |
| **38** | Assigned Visits | Aesthetics | **Ridondanza nome volontario**: Mostrato nome anche se assegnato a me. | ❌ **Pending** | Nascondere l'informazione ridondante del nome se sono io. |
| **39** | Dashboard | Recognition | **Messaggio stato vuoto senza Call to Action**: "No tours booked" secco. | ❌ **Pending** | `user/dashboard.blade.php`: Se vuoto, usare CTA con bottone "Sfoglia nuovi tour" -> Home. |
| **40** | Pagination / General | User Control | **Scroll inaspettato al cambio pagina**: Salta in cima erroneamente. | ❌ **Pending** | Gestione dello scroll o rimozione di `scrollIntoView()` aggressivi indesiderati. |

## Note Tecniche per i Fix Prioritari

### Fix Chiarezza Prezzo (Issue 3)
In `resources/views/tours/register.blade.php`:
```html
<li class="mb-2 d-flex">
    <i class="bi bi-tag me-2 text-primary"></i>
    <strong>Price:</strong> &nbsp; {{ $visit->visitType->price > 0 ? '€' . $visit->visitType->price : 'Free' }}
</li>
```

### Fix Visibilità Logo (Issue 13)
In `resources/views/layouts/app.blade.php`:
```html
<img src="/images/unibslogo_micro.svg" alt="UniBS Logo" class="me-2" style="width: 40px; height: 40px;">
<!-- Removed filter: grayscale(1) -->
```

### Fix Area Cliccabile (Issue 8)
In `resources/views/home.blade.php`, avvolgere la card (o usare stretched-link):
```html
<a href="{{ route('visits.register.form', ...) }}" class="stretched-link"></a>
<!-- Ensure parent .card has position: relative -->
```
