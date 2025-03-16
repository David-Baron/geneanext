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

if (file_exists(__DIR__ . '/../.env.local.php')) {
    $_ENV = require(__DIR__ . '/../.env.local.php');
}

function dd(mixed $variable)
{
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit;
}

require(__DIR__ . '/ressources/fonctions.php'); // compatibility only