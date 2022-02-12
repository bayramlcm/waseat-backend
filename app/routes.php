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

App::LanguageRoute('POST', [
    'tr' => '/tr/register',
    'en' => '/en/register',
], '/register');

App::LanguageRoute('POST', [
    'tr' => '/tr/login',
    'en' => '/en/login',
], '/login');


/*************************************************
 *                Araba Bilgisi                  *
 *************************************************/

App::LanguageRoute('POST', [
    'tr' => '/tr/carbon/car/years',
    'en' => '/en/carbon/car/years',
], '/carbon/car/years');

App::LanguageRoute('POST', [
    'tr' => '/tr/carbon/car/brands',
    'en' => '/en/carbon/car/brands',
], '/carbon/car/brands');

App::LanguageRoute('POST', [
    'tr' => '/tr/carbon/car/models',
    'en' => '/en/carbon/car/models',
], '/carbon/car/models');

App::LanguageRoute('POST', [
    'tr' => '/tr/carbon/car/variants',
    'en' => '/en/carbon/car/variants',
], '/carbon/car/variants');

// 404
App::LanguageRoute('GET', [
    'tr' => '/tr/404',
    'en' =>  '/en/404',
], '/notFound');
