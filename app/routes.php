<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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


/*************************************************
 *                Konum Araçları                 *
 *************************************************/

// ******** 2 Konum Arası Uzaklık **********
App::LanguageRoute('POST', [
    'tr' => '/tr/maps/travel',
    'en' => '/en/maps/travel',
], '/maps/travel');

// ********* Maps Arama Sonuçları **********
App::LanguageRoute('POST', [
    'tr' => '/tr/maps/place/autocomplete',
    'en' => '/en/maps/place/autocomplete',
], '/maps/place/autocomplete');

App::LanguageRoute('POST', [
    'tr' => '/tr/maps/place/detail',
    'en' => '/en/maps/place/detail',
], '/maps/place/detail');





/*************************************************
 *               Oturum İşlemleri                *
 *************************************************/
// Middleware
App::Middleware(['/tr/v1', '/en/v1'], function () {
    $sessionErrorMessage = $_SESSION["language"] === "tr" ? "Oturumunuz sonlandırıldı." : "Your account cannot be verified.";
    // Oturum kontrolü
    try {
        $token = App::getToken();
        $payload = JWT::decode($token, new Key(App::$appConfig["jwt"], 'HS256'));
        // Kullanıcı hesap kontrolü
        $userData = App::$appModule->Mysql->get(
            "SELECT * FROM users WHERE id = ?",
            [$payload->userId]
        );
        if (!$userData) {
            exit(json_encode([
                "status" => false,
                "message" => $sessionErrorMessage,
                "data" => null,
            ]));
        }
    } catch (\Throwable $th) {
        exit(json_encode([
            "status" => false,
            "message" => $sessionErrorMessage,
            "data" => null,
        ]));
    }
    // ************  Adım Güncelle *************    
    App::LanguageRoute('POST', [
        'tr' => '/tr/v1/step/update',
        'en' => '/en/v1/step/update',
    ], '/v1/step/update', ["userData" => $userData]);

});

/*************************************************
 *                     404                       *
 *************************************************/

App::LanguageRoute('GET', [
    'tr' => '/tr/404',
    'en' =>  '/en/404',
], '/notFound');
