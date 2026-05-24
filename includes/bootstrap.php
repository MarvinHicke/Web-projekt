<?php

require_once __DIR__ . '/../config/appconfig.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/dbaccess.php';

function base_url(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

function db()
{
    static $pdo = null;

    if ($pdo === null) {
        $db = new dbaccess();
        $pdo = $db->getPdo();
    }

    return $pdo;
}