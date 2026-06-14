<?php
// Load shared application helpers, configuration, session handling, and utility functions.
require_once __DIR__ . '/../includes/bootstrap.php';

// Set the page title before loading the shared header.
$pageTitle = 'Über uns';

// Render the shared page header and navigation.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Page introduction section with project context. -->
    <section class="page-heading">
        <p class="eyebrow">Web-Technologien · Sommersemester 2026</p>
        <h1>Über Art Gallery</h1>
        <p class="lead mb-0">
            Art Gallery ist eine hypothetische Kunstplattform, die als Semesterprojekt
            im Kurs Web-Technologien [SS26] an der Technischen Hochschule Wildau entwickelt wurde.
        </p>
    </section>

    <!-- Main project information area. -->
    <section class="about-grid" aria-label="Projektinformationen">
        <!-- General project description. -->
        <article class="content-card">
            <h2>Das Projekt</h2>
            <p>
                Die Anwendung macht Reproduktionen bekannter Kunstwerke zugänglich.
                Besucher können Kunstwerke, Künstler, Genres und Themen durchsuchen,
                Detailinformationen ansehen und die globale Suche verwenden.
            </p>
            <p class="mb-0">
                Zusätzlich bietet das Projekt sitzungsbasierte Favoriten sowie geschützte
                Funktionen für registrierte Benutzer und Administratoren.
            </p>
        </article>

        <!-- Key project facts and technologies. -->
        <article class="content-card">
            <h2>Rahmendaten</h2>
            <dl class="about-facts mb-0">
                <dt>Kurs</dt>
                <dd>Web-Technologien [SS26]</dd>
                <dt>Semester</dt>
                <dd>Sommersemester 2026</dd>
                <dt>Gruppe</dt>
                <dd>Gruppe 1</dd>
                <dt>Technologien</dt>
                <dd>PHP 8, MySQL, Bootstrap 5, HTML5 und CSS</dd>
            </dl>
        </article>
    </section>

    <!-- Team overview and responsibility distribution. -->
    <section class="content-card mt-4">
        <p class="eyebrow">Gruppe 1</p>
        <h2>Team und Arbeitsverteilung</h2>

        <!-- Responsive team card layout. -->
        <div class="team-grid">
            <!-- Team member: data access and architecture. -->
            <article class="team-card">
                <span class="team-role">Daten &amp; Architektur</span>
                <h3>Marvin Hicke</h3>
                <p>DB-Anbindung, Repository-Grundlagen, Datenzugriff, Tests und technische Integration.</p>
            </article>

            <!-- Team member: layout and UI system. -->
            <article class="team-card">
                <span class="team-role">Layout &amp; System</span>
                <h3>Sehyang Na</h3>
                <p>Bootstrap-Theme, lokale Fonts, Navigation, About Us, Bild-Fallbacks, UI-Unterstützung und Dokumentationsgrafiken.</p>
            </article>

            <!-- Team member: public browsing content. -->
            <article class="team-card">
                <span class="team-role">Öffentliche Inhalte</span>
                <h3>Lisanne Godlinski</h3>
                <p>Übersichts- und Detailseiten für Künstler, Genres und Themen.</p>
            </article>

            <!-- Team member: homepage, artworks, and search. -->
            <article class="team-card">
                <span class="team-role">Startseite, Kunstwerke &amp; Suche</span>
                <h3>Fatemeh Nezamolmaleki</h3>
                <p>Startseite, Suchergebnisse, Artwork-Detailseite, Widgets und Inhaltsverknüpfungen.</p>
            </article>

            <!-- Team member: authentication, favorites, reviews, and administration. -->
            <article class="team-card">
                <span class="team-role">Benutzerfunktionen</span>
                <h3>Linus Meyer</h3>
                <p>Registrierung, Anmeldung/Abmeldung, Konto, Favoriten, Bewertungen, Rollen und Benutzerverwaltung.</p>
            </article>
        </div>
    </section>

<?php
// Render the shared page footer.
require_once __DIR__ . '/../includes/footer.php';
?>