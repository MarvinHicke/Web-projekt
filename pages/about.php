<?php
// Gemeinsame Konfiguration, Hilfsfunktionen und Basisfunktionen laden.
require_once __DIR__ . '/../includes/bootstrap.php';

// Seitentitel vor dem Laden des Headers setzen.
$pageTitle = 'Über uns';

// Gemeinsamen Header mit Navigation einbinden.
require_once __DIR__ . '/../includes/header.php';
?>

    <!-- Einleitungsbereich mit Projektkontext. -->
    <section class="page-heading">
        <p class="eyebrow">Web-Technologien · Sommersemester 2026</p>
        <h1>Über Art Gallery</h1>
        <p class="lead mb-0">
            Art Gallery ist eine hypothetische Kunstplattform, die als Semesterprojekt
            im Kurs Web-Technologien [SS26] an der Technischen Hochschule Wildau entwickelt wurde.
        </p>
    </section>

    <!-- Bereich mit allgemeinen Projektinformationen. -->
    <section class="about-grid" aria-label="Projektinformationen">
        <!-- General project description. -->
        <article class="content-card">
            <h2>Das Projekt</h2>
            <p>
                Die Anwendung macht bekannte Kunstwerke digital zugänglich.
                Besucher können Kunstwerke, Künstler, Genres und Themen durchsuchen,
                Detailinformationen ansehen und eine detaillierte globale Suche nutzen.
            </p>
            <p class="mb-0">
                Zusätzlich bietet das Projekt die Möglichkeit Favoritenlisten zu erstellen sowie weitere
                Funktionen für registrierte Benutzer und Administratoren.
            </p>
        </article>

        <!-- Rahmendaten des Semesterprojekts. -->
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

    <!-- Teamübersicht mit Arbeitsverteilung. -->
    <section class="content-card mt-4">
        <p class="eyebrow">Gruppe 1</p>
        <h2>Team und Arbeitsverteilung</h2>

        <!-- Kartenlayout für die Teammitglieder. -->
        <div class="team-grid">
            <!-- Team member: data access and architecture. -->
            <article class="team-card">
                <span class="team-role">Daten &amp; Architektur</span>
                <h3>Marvin Hicke</h3>
                <p>DB-Anbindung, Repository-Grundlagen, Datenzugriff, Tests und technische Integration.</p>
            </article>

            <!-- Teammitglied mit Schwerpunkt Layout und UI-System. -->
            <article class="team-card">
                <span class="team-role">Layout &amp; System</span>
                <h3>Sehyang Na</h3>
                <p>Bootstrap-Theme, lokale Fonts, Navigation, About Us, Bild-Fallbacks, UI-Unterstützung und Dokumentationsgrafiken.</p>
            </article>

            <!-- Teammitglied mit Schwerpunkt öffentliche Inhaltsseiten. -->
            <article class="team-card">
                <span class="team-role">Öffentliche Inhalte</span>
                <h3>Lisanne Godlinski</h3>
                <p>Übersichts- und Detailseiten für Künstler, Genres und Themen.</p>
            </article>

            <!-- Teammitglied mit Schwerpunkt Startseite, Kunstwerke und Suche. -->
            <article class="team-card">
                <span class="team-role">Startseite, Kunstwerke &amp; Suche</span>
                <h3>Fatemeh Nezamolmaleki</h3>
                <p>Startseite, Suchergebnisse, Artwork-Detailseite, Widgets und Inhaltsverknüpfungen.</p>
            </article>

            <!-- Teammitglied mit Schwerpunkt Benutzerfunktionen. -->
            <article class="team-card">
                <span class="team-role">Benutzerfunktionen</span>
                <h3>Linus Meyer</h3>
                <p>Registrierung, Anmeldung/Abmeldung, Konto, Favoriten, Bewertungen, Rollen und Benutzerverwaltung.</p>
            </article>
        </div>
    </section>

<?php
// Gemeinsamen Footer einbinden.
require_once __DIR__ . '/../includes/footer.php';
?>