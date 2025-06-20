@extends('layouts.app')

@section('title', 'Lavora con noi')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <h1 class="text-center"><i>Lavora con noi</i></h1>

    <h3 class="text-center"><i>"L’innovazione nasce dove la passione incontra la competenza."</i></h3>

    <p>
        Sebbene il nostro team sia nato come gruppo di progetto universitario per il corso di Programmazione Web presso
        l’<strong>Università degli Studi di Brescia</strong>, il percorso che abbiamo intrapreso ha fatto emergere una forte sinergia e interesse condiviso
        verso lo sviluppo software moderno, la progettazione pulita e la collaborazione su progetti open source.
    </p>

    <p>
        Il nostro obiettivo con <strong>GuidedTours</strong> non era solo quello di rispettare i requisiti didattici, ma anche di costruire
        un’applicazione modulare, accessibile e realmente utilizzabile, che potesse adattarsi a contesti reali di gestione eventi e visite guidate.
    </p>

    <h4>Chi siamo?</h4>
    <ul>
        <li><strong>Giorgio Felappi</strong> – Backend Developer, architettura e gestione stato<br>
            GitHub: <a href="https://github.com/Gl0rGl0" target="_blank">github.com/Gl0rGl0</a>
        </li>
        <li><strong>Daniel Barbetti</strong> – Frontend Designer & UI/UX<br>
            GitHub: <a href="https://github.com/barbetti3" target="_blank">github.com/barbetti3</a>
        </li>
        <li><strong>Leonardo Folgoni</strong> – Data Integration & Testing Automation<br>
            GitHub: <a href="https://github.com/f0lg0" target="_blank">github.com/f0lg0</a>
        </li>
    </ul>

    <h4>Perché collaborare con noi?</h4>
    <p>
        Il nostro background accademico ci ha fornito solide basi teoriche, ma è nel lavoro di squadra che abbiamo maturato
        competenze reali: versionamento avanzato con Git, test-driven development con JUnit, refactoring in ambienti legacy e progettazione di interfacce intuitive con Laravel e JavaFX.
    </p>

    <h4>Disponibilità</h4>
    <p>
        Siamo sempre aperti a nuove opportunità, progetti collaborativi open source o anche a sfide lanciate da startup emergenti.
        Per contattarci, puoi visitare i nostri profili GitHub oppure consultare i <a href="{{ route('terms') }}">Termini di Servizio</a>.
    </p>

    <p class="text-center mt-4">
        Il codice del progetto è disponibile al seguente indirizzo:<br>
        <a href="https://github.com/Gl0rGl0/GuidedTours" target="_blank">github.com/Gl0rGl0/GuidedTours</a>
    </p>
</div>
@endsection
