<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

require(__DIR__ . '/../vendor/autoload.php');

header('content-type: text/html; charset=utf-8');

require(__DIR__ . '/Engine/UserPermission.php');

$root = '';
$session = new Session();
$session->start();
$userPermission = new UserPermission($session);

$request = Request::createFromGlobals();
$response = new Response();

/* if (!file_exists(__DIR__ . '/../.env.local.php')) {
    header('Location: /install.php');
    exit;
} */

if (file_exists(__DIR__ . '/../.env.local.php')) {
    $_ENV = require(__DIR__ . '/../.env.local.php');
}

if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'dev') {
    error_reporting(E_ALL);

    function dd(mixed $variable)
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        exit;
    }
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

/** @deprecated */
// $is_windows = substr(php_uname(), 0, 7) == "Windows" ? true : false;

$langue = 'FR';
$langue_min = 'fr';
$locale = 'fr_FR';

require(__DIR__ . '/Model/GeneralModel.php');


$generalModel = new GeneralModel();
$settings = $generalModel->findFirst();

function IS_AUTHENTICATED()
{
    global $userPermission;
    return $userPermission->isAuthenticated();
}

function IS_GRANTED(string $code)
{
    global $userPermission;
    return $userPermission->isGranted($code);
}