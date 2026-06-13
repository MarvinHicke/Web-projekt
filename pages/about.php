<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Über uns';

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-heading">
    <p class="eyebrow">Web-Technologien · Sommersemester 2026</p>
    <h1>Über Art Gallery</h1>
    <p class="lead mb-0">
        Art Gallery ist eine hypothetische Kunstplattform, die als Semesterprojekt
        im Kurs Web-Technologien [SS26] an der Technischen Hochschule Wildau entwickelt wurde.
    </p>
</section>

<section class="about-grid" aria-label="Projektinformationen">
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

<section class="content-card mt-4">
    <p class="eyebrow">Gruppe 1</p>
    <h2>Team und Arbeitsverteilung</h2>
    <div class="team-grid">
        <article class="team-card">
            <span class="team-role">Daten &amp; Architektur</span>
            <h3>Marvin Hicke</h3>
            <p>DB-Anbindung, Repository-Grundlagen, Datenzugriff, Tests und technische Integration.</p>
        </article>
        <article class="team-card">
            <span class="team-role">Layout &amp; System</span>
            <h3>Sehyang Na</h3>
            <p>Bootstrap-Theme, lokale Fonts, Navigation, About Us, Bild-Fallbacks, UI-Unterstützung und Dokumentationsgrafiken.</p>
        </article>
        <article class="team-card">
            <span class="team-role">Öffentliche Inhalte</span>
            <h3>Lisanne Godlinski</h3>
            <p>Übersichts- und Detailseiten für Künstler, Genres und Themen.</p>
        </article>
        <article class="team-card">
            <span class="team-role">Startseite, Kunstwerke &amp; Suche</span>
            <h3>Fatemeh Nezamolmaleki</h3>
            <p>Startseite, Suchergebnisse, Artwork-Detailseite, Widgets und Inhaltsverknüpfungen.</p>
        </article>
        <article class="team-card">
            <span class="team-role">Benutzerfunktionen</span>
            <h3>Linus Meyer</h3>
            <p>Registrierung, Anmeldung/Abmeldung, Konto, Favoriten, Bewertungen, Rollen und Benutzerverwaltung.</p>
        </article>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
