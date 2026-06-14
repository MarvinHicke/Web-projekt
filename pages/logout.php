<?php
/**
 * Logout-Aktion für UC22.
 *
 * Leert die aktuelle Session und leitet den Benutzer zurück zur Login-Seite.
 */
require_once __DIR__ . '/../includes/init.php';

$_SESSION=[];
session_destroy();
header("Location: " . base_url('pages/login.php'));
exit;
