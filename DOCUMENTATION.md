# Dokumentation des Projektes:

- maximal 10 Seiten
- Deckblatt mit Team Namen + Gruppennummer
- Inhaltsverzeichnis
- Seitenzahlen

- Fortschritt Tabelle für Use Cases
  - Use Case Nummer
  - verantwortliches Team Mitglied
  - Status (in Bearbeitung, Fertig, Testen etc.)
  - aufgetretene Probleme
- Software Design
  - TechStack
  - Architektur Übersicht
  - UML-Diagramme (Fokus auf Klassen, Assoziationen/Verwendungen)
  - Sitemap
  - Verzeichnisstruktur
- Datenbankdesign
  - ER-Diagramm
  - relevante SQL-Abfragen
  - Funktionen / Trigger
- maximal 5 kommentierte Screenshots (visueller Überblick)
- Beschreibung der Arbeitsaufteilung unter den Teammitgliedern
- Reflexion (Erfahrungen, aufgetretene Probleme etc.)

#UI Komponenten

page-header.php
- $title (required)
- $subtitle (optional)

artist-card.php
- $artist (artist object)

artwork-card.php
- $artwork (artwork object)

button.php
- $buttonText
- $buttonHref
- $buttonVariant (optional)
alert-box.php
- $alertMessage
- $alertType (optional)

sort-bar.php
- $sortOptions
- $currentSort (optional)


# Art Gallery - Projektdokumentation

## Projektdaten

| Angabe | Wert |
| --- | --- |
| Kurs | Web-Technologien [SS26] |
| Semester | Sommersemester 2026 |
| Gruppe | Gruppe 1 |
| Projekt | Art Gallery |
| Technologie | PHP 8, MySQL, Bootstrap 5, HTML5, CSS |

Art Gallery ist eine hypothetische Plattform für Reproduktionen bekannter
Kunstwerke. Die Anwendung wurde als Semesterprojekt für den Kurs
Web-Technologien [SS26] an der Technischen Hochschule Wildau entwickelt.

## Team und Arbeitsverteilung

| Teammitglied | Verantwortungsbereich |
| --- | --- |
| Marvin Hicke | Datenbankzugriff, Repositorys, Architektur, Tests und technische Integration |
| Sehyang Na | Bootstrap-Theme, lokales Font-System, Navigation, About Us, Bild-Fallbacks, UI-Unterstützung, Sitemap, Verzeichnisübersicht und Screenshots |
| Lisanne Godlinski | Browse- und öffentliche Detailseiten für Künstler, Genres und Subjects |
| Fatemeh Nezamolmaleki | Startseite, Suche, Suchergebnisse, Artwork-Detailseite und Home-Widgets |
| Linus Meyer | Registrierung, Login/Logout, Konto, Favoriten, Reviews, Rollen und Benutzerverwaltung |

## Fortschritt aller Use Cases

Die folgende Tabelle bildet den Stand der Anwendung anhand des vorhandenen
Quellcodes ab. `Teilweise` bedeutet, dass eine sichtbare oder technische
Grundlage vorhanden ist, aber noch Anforderungen des Use Cases fehlen.

| Use Case | Bezeichnung | Verantwortlich | Status | Umsetzung und offene Punkte |
| --- | --- | --- | --- | --- |
| UC01 | Bootstrap Theme and Site Design | Sehyang Na | Fertig | Bootstrap 5 sowie Google Sans und Manrope werden lokal eingebunden. Der externe Font-Import wurde entfernt und das Design global vereinheitlicht. |
| UC02 | Home Page | Fatemeh Nezamolmaleki | Testen | Startseite mit Hero, Artwork-Carousel und drei ausgelagerten Datenboxen ist vorhanden. Die Auswahl der Carousel-Bilder und die Mindestanzahl der Bewertungen müssen mit vollständigen DB-Daten abschließend geprüft werden. |
| UC03 | Navigation | Sehyang Na | Fertig | Hauptnavigation, Utility-Menü und globale Suche sind überall über den Header verfügbar. Subjects sowie zustandsabhängige Konto-, Login-, Logout- und Admin-Links sind enthalten. |
| UC04 | About Us | Sehyang Na | Fertig | Die Seite beschreibt das hypothetische Semesterprojekt, Kurs, Semester, Gruppe, Teammitglieder und Arbeitsverteilung. |
| UC05 | Browse Artists | Lisanne Godlinski  | Teilweise | Künstlerliste, Bilder, Fallbacks, Detailverlinkung und Favoriten-Schaltfläche sind vorhanden. Die geforderte sichtbare Sortierung nach Namen auf- und absteigend fehlt noch. |
| UC06 | Browse Artworks | Fatemeh Nezamolmaleki  | Fertig | Kunstwerke werden mit Bildern und Detaillinks angezeigt. Sortierung nach Titel, Künstler oder Jahr sowie auf- und absteigende Richtung sind umgesetzt. |
| UC07 | Browse Genres | Lisanne Godlinski | Teilweise | Genre-Liste mit Bildern und Fallbacks ist vorhanden. Die Sortierung nach Epoche und Name sowie die Verlinkung auf eine noch fehlende Einzelansicht müssen fertiggestellt werden. |
| UC08 | Browse Subjects | Lisanne Godlinski | Teilweise | Subject-Liste mit Bildern und Fallbacks ist vorhanden. Die Sortierung nach SubjectName sowie die Verlinkung auf eine noch fehlende Einzelansicht müssen fertiggestellt werden. |
| UC09 | Simple Search | Fatemeh Nezamolmaleki  | Fertig | Das Suchfeld ist global verfügbar, verlangt mindestens drei Zeichen und sucht per Präfixsuche nach Künstlernachnamen und Kunstwerktiteln. |
| UC10 | Search Results | Fatemeh Nezamolmaleki  | Teilweise | Künstler und Kunstwerke werden mit Bild und Detaillink getrennt dargestellt. Vollständige Sortieroptionen mit Richtung sowie Add-to-Favorites in den Ergebnissen fehlen noch. |
| UC11 | Display Single Artist | Lisanne Godlinski  | Testen | Künstlerdaten, Bild, Biografie, Detailtabelle, Favoritenstatus und verlinkte Kunstwerke sind vorhanden. Fehlerweiterleitung und vollständige Ausgabe aller DB-Felder müssen abschließend geprüft werden. |
| UC12 | Display Single Artwork | Fatemeh Nezamolmaleki  | Testen | Artwork-, Künstler-, Galerie-, Genre-, Subject-, Rating- und Review-Daten werden angezeigt. Bootstrap-Modal, Accordion, Favoriten und Review-Formular sind integriert; Endtest mit mehreren Datensätzen steht aus. |
| UC13 | Display Single Genre | Lisanne Godlinski  | Offen | `single-genre.php` ist noch nicht vorhanden. Genre-Informationen und zugehörige Kunstwerke müssen noch umgesetzt werden. |
| UC14 | Display Single Subject | Lisanne Godlinski | Offen | `single-subject.php` ist noch nicht vorhanden. Subject-Informationen und zugehörige Kunstwerke müssen noch umgesetzt werden. |
| UC15 | Missing Images | Sehyang Na | Fertig | Gemeinsame PHP-Helper prüfen mehrere Bildgrößen und liefern für fehlende Artworks, Artists, Genres und Subjects `images/placeholder.jpg`. |
| UC16 | Add A Review | Linus Meyer | Fertig | Angemeldete Benutzer können Bewertungen mit Rating und Kommentar speichern. Pflichtfelder, Ratingbereich und einmalige Bewertung pro Artwork werden geprüft. |
| UC17 | Delete A Review | Linus Meyer | Fertig | Nur Administratoren können Bewertungen per POST nach Bestätigungsdialog löschen; anschließend wird die Artwork-Seite erneut geladen. |
| UC18 | Add To Favorites List | Linus Meyer | Fertig | Künstler und Kunstwerke werden getrennt und ohne Duplikate in der PHP-Session gespeichert. |
| UC19 | View Favorites List | Linus Meyer  | Fertig | Favorisierte Künstler und Kunstwerke werden in getrennten Bereichen angezeigt und können wieder entfernt werden. |
| UC20 | Register User | Linus Meyer  | Testen | Registrierung, Pflichtfeldprüfung, E-Mail-Prüfung, Passwort-Hashing und Prüfung auf bestehende Benutzernamen sind vorhanden. Vollständiger DB-Integrationstest steht aus. |
| UC21 | Manage Users | Linus Meyer | Offen | Admin-Zugriffsschutz ist vorhanden, die eigentliche Liste und Bearbeitung von Benutzern ist jedoch noch nicht implementiert. |
| UC22 | Login as User | Linus Meyer  | Fertig | Datenbankgestützter Login, Passwortprüfung, Session-Aufbau, Fehlermeldungen, Benutzeranzeige und Logout sind umgesetzt. |
| UC23 | My Account | Linus Meyer  | Testen | Angemeldete Benutzer können Profil- und Passwortdaten bearbeiten. Validierung und Zugriffsschutz sind vorhanden; Sonderfälle müssen abschließend getestet werden. |
| UC24 | Advanced Search | Fatemeh Nezamolmaleki  | Offen | Die Seite ist als Navigationseintrag und Platzhalter vorhanden. Kriterien für Artist- und Artwork-Suche sowie Ergebnisübergabe fehlen noch. |
| UC25 | Map to Museum | Fatemeh Nezamolmaleki  | Offen | Im Galerie-Accordion ist noch keine OpenStreetMap-Karte mit Marker eingebunden. |
| UC26 | Global Error Page | Sehyang Na | Fertig | Apache-Fehler 400, 401, 403, 404 und 500 werden über `.htaccess` an die globale Fehlerseite weitergeleitet; Directory Listing ist deaktiviert. |

## Funktionsbereiche nach Use Cases

### Öffentlicher Bereich

Der öffentliche Bereich umfasst Startseite, About Us, Navigation, Browse-Seiten,
einfache Suche, Suchergebnisse sowie die Einzelansichten von Künstlern und
Kunstwerken. Die Bilder werden unabhängig von der aufrufenden Seite über
gemeinsame Fallback-Helper geladen.

### Session- und Benutzerbereich

Favoriten werden ausschließlich in der aktuellen PHP-Session gespeichert.
Registrierung, Anmeldung und Kontobearbeitung greifen über das
`customerRepository` auf die vorhandenen Kundentabellen zu. Rolleninformationen
steuern die Sichtbarkeit und Erreichbarkeit administrativer Funktionen.

### Reviews und Administration

Bewertungen sind an Artwork und Benutzer gebunden. Die Anwendung verhindert eine
zweite Bewertung desselben Kunstwerks durch denselben Benutzer. Das Löschen von
Bewertungen ist Administratoren vorbehalten. Die vollständige Benutzerverwaltung
aus UC21 ist noch offen.

### Erweiterte Funktionen

Die erweiterte Suche und die Museumskarte sind noch nicht implementiert. Die
globale Apache-Fehlerseite ist dagegen fertig konfiguriert.

## konkreter Arbeitsbeitrag

**Sehyang Na** verantwortet ausschließlich die anwendungsweite Präsentationsschicht
und die zugehörige Projektdokumentation. Dazu gehören:

- lokale Einbindung von Bootstrap und den Schriftfamilien Google Sans und Manrope
- Entfernung externer Google-Font-Anfragen
- globaler Header, Footer, Hauptnavigation, Utility-Menü und Suchfeld
- Ergänzung von Subjects im Browse-Menü
- zustandsabhängige Darstellung von Login, Logout, Konto und Admin-Link
- About-Us-Seite mit Projekt- und Teaminformationen
- zentrale Bild-Fallback-Helfer und Fallback-Darstellung in Karten
- visuelle Unterstützung für Home, Search Results, Single Artwork, Favorites und Login
- Sitemap, Verzeichnisübersicht und Vorbereitung der Dokumentationsscreenshots



## Softwaredesign

### Architekturübersicht

Die Anwendung verwendet eine einfache Schichtenstruktur:

1. `pages/` und `index.php` bilden die Seiten- und Präsentationsschicht.
2. `components/` und `includes/` liefern wiederverwendbare UI- und Layoutbausteine.
3. `repositories/` kapselt Datenbankabfragen.
4. `model/` repräsentiert fachliche Objekte.
5. `config/` und `includes/dbaccess.php` stellen Konfiguration und Datenbankzugriff bereit.

Die Präsentationsschicht greift über Repositorys auf Daten zu. Globale Funktionen
wie URL-Erzeugung, HTML-Escaping und Bild-Fallbacks werden zentral in Includes
bereitgestellt.

### Sitemap

```text
Startseite
|-- Über uns
|-- Erweiterte Suche
|-- Suche
|   `-- Suchergebnisse
|-- Durchsuchen
|   |-- Kunstwerke
|   |   `-- Einzelnes Kunstwerk
|   |-- Künstler
|   |   `-- Einzelner Künstler
|   |-- Genres
|   `-- Subjects
`-- Utility-Menü
    |-- Favoriten
    |-- Registrieren (abgemeldet)
    |-- Anmelden (abgemeldet)
    |-- Mein Konto (angemeldet)
    |-- Abmelden (angemeldet)
    `-- Benutzer verwalten (nur Administrator)
```

### Verzeichnisstruktur

```text
Web-projekt/
|-- index.php                 Startseite
|-- assets/
|   |-- css/                  Bootstrap, Theme und Projekt-CSS
|   |-- fonts/                lokal eingebettete Google Fonts
|   `-- js/                   lokales Bootstrap-Bundle
|-- components/               wiederverwendbare UI-Komponenten
|-- config/                   Anwendungs- und DB-Konfiguration
|-- images/                   Kunstbilder und Placeholder
|-- includes/                 Layout, Initialisierung, Auth- und Hilfsfunktionen
|   `-- boxes/                Datenboxen der Startseite
|-- model/                    Domain-Objekte
|-- pages/                    öffentliche und geschützte Seiten
|-- repositories/             Datenzugriff pro Domain
`-- tests/                    Repository- und Helper-Tests
```

## Bild-Fallback-Konzept

Alle Bilder werden serverseitig über zentrale Helper aufgelöst. Die Helper prüfen
zuerst den gewünschten Größenordner und anschließend alternative vorhandene
Größen. Ist keine Datei vorhanden oder der Dateiname leer, wird
`images/placeholder.jpg` ausgegeben. Dieses Verfahren wird für Kunstwerke,
Künstler, Genres und Subjects verwendet und benötigt keine Änderung am
Datenbankschema.

## Screenshot

Für die Dokumentation werden höchstens fünf kommentierte Screenshots verwendet:

1. Startseite: globaler Header, Suche, Hero und Carousel.
2. Browse-Ansicht: Kartenraster und sichtbare Bild-Fallbacks.
3. Suchergebnisse: getrennte Künstler- und Kunstwerkspalten.
4. Einzelnes Kunstwerk: Bild, Detailtabelle, Accordion und Reviews.
5. Login oder Favoriten: konsistentes Formular- bzw. Kartenlayout.

Die Bilder sollen in Schwarzweiß verständlich bleiben. Kommentare markieren
Navigation, lokale Gestaltung, Fallback-Verhalten und wiederverwendbare
Bootstrap-Komponenten.

## Reflexion

Sehyang

Die größte Herausforderung war die Vereinheitlichung bereits unterschiedlich
aufgebauter Seiten, ohne Zuständigkeiten anderer Teammitglieder zu überschreiben.
Deshalb wurden globale CSS-Regeln, gemeinsame Komponenten und Helper bevorzugt.
