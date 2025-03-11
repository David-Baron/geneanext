<?php

use Symfony\Component\HttpFoundation\Session\Session;

require(__DIR__ . '/../vendor/autoload.php');
$root = '';
$session = new Session();
$session->start();

/* if (!file_exists(__DIR__ . '/../.env.local.php')) {
    header('Location: /install.php');
    exit;
} */
