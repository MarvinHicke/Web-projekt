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


UI Components

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