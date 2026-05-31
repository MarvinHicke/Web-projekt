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
    static $db = null;

    if ($db === null) {
        $db = new dbaccess();
        $db->connect();
    }

    return $db;
}
