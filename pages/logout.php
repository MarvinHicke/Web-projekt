<?php
require_once 'includes/init.php';

session_start();
$SESSION=[];
session_destroy();
header("Location: login.php");
exit;