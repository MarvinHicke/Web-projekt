<?php
require_once __DIR__ . '/../includes/init.php';

$_SESSION=[];
session_destroy();
header("Location: " . base_url('pages/login.php'));
exit;
