<?php

/**
 * Checks whether a user is currently logged in.
 *
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Checks whether the current user is an administrator.
 *
 * @return bool
 */
function isAdmin(): bool
{
    return isset($_SESSION['user']) && (int)($_SESSION['user']['Type'] ?? 0) === 2;
}

/**
 * Returns the current username.
 *
 * @return string
 */
function getCurrentUsername(): string
{
    return $_SESSION['user']['UserName'] ?? '';
}
