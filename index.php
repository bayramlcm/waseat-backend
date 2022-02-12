<?php
date_default_timezone_set("Europe/Istanbul");

define('SECRET', '@bayramlcm');

define('PS', PATH_SEPARATOR);
define('DS', DIRECTORY_SEPARATOR);

define('ROOT', __DIR__ . DS);
define('APPPATH', ROOT . 'app' . DS);
define('SYSTEMPATH', ROOT . 'system' . DS);

session_start();

include SYSTEMPATH . 'init.php';

System::composer();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_ENV["DEBUG"]) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

define('DEFAULT_LANG', $_ENV["DEFAULT_LANG"]); // Varsayılan site dili (en, tr)
if ($_ENV["DEBUG"]) {
    $r = rand(10000000, 999999999);
    define('VERSION', "1.0.1-DEBUG-{$r}");
} else
    define('VERSION', '1.0.1');

include APPPATH . 'init.php';

App::run(@$_GET['route']);
