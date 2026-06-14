<?php

/**
 * Prüft, ob aktuell ein Benutzer angemeldet ist.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Prüft, ob der aktuell angemeldete Benutzer Administratorrechte besitzt.
 *
 * @return bool
 */
function isAdmin(): bool
{
    return isset($_SESSION['user']) && (int)($_SESSION['user']['Type'] ?? 0) === 2;
}

/**
 * Gibt den Benutzernamen des aktuell angemeldeten Benutzers zurück.
 *
 * @return string
 */
function getCurrentUsername(): string
{
    return $_SESSION['user']['UserName'] ?? '';
}