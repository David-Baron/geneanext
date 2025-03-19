<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

require(__DIR__ . '/../vendor/autoload.php');

header('content-type: text/html; charset=utf-8');

$root = '';
$session = new Session();
$session->start();
$request = Request::createFromGlobals();
$response = new Response();
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


/** Is it a bot? 
 * @todo completely incomplete list, 
 * moreover it becomes difficult to know them all, 
 * another solution must be found as a replacement
 */
$is_bot =  (
    isset($_SERVER['HTTP_USER_AGENT'])
    && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
);
