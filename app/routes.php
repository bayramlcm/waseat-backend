<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');


// Route -> (METHOD, URL, CONTROLLER, DATA (opsiyonel))
// Middleware -> (URL, MIDDLEWARE FUNCTION)

$_SESSION["language"] = DEFAULT_LANG;

App::Middleware('/en', function () {
    $_SESSION["language"] = "en";
});

App::Middleware('/tr', function () {
    $_SESSION["language"] = "tr";
});


/*************************************************
 *                 Giriş/Kayıt                   *
 *************************************************/

// 404
App::LanguageRoute('GET', [
    'tr' => '/tr/404',
    'en' =>  '/en/404',
], '/notFound');
