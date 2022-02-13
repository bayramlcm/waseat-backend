<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class App extends System
{

  // NOTE: Tüm Routelar
  public static $appRoutes = [];
  // Note: Tüm Middlewarelar
  private static $appMiddlewares = [];
  // NOTE: Tüm Modüller
  public static $appModule = null;
  // NOTE: Tüm Ayarlar
  public static $appConfig = [
    'url' => 'http://localhost',
    'mysql' => [
      'host' => '',
      'user' => '',
      'pass' => '',
      'db' => '',
    ],
    'jwt' => '',
    'cors' => TRUE,
  ];
  public static $languageRoutes = [];
  // NOTE: Gelen URL
  public static $appUrl = '';

  // NOTE: Dinamik Route Ekle
  public static function LanguageRoute($method, $languagePaths, $controller, $data = null, $func = null)
  {
    foreach ($languagePaths as $language => $path) {
      if (!array_key_exists($language, self::$languageRoutes)) {
        self::$languageRoutes[$language] = [];
      }
      self::$languageRoutes[$language][$controller] = $path;
      self::Route($method, $path, $controller, $data, $func);
    }
  }

  // NOTE: Tüm route'ları ekle
  public static function Route($method, $path, $controller, $data = null, $func = null)
  {
    self::$appRoutes[] = [
      'methods' => $method,
      'path' => '/' . trim($path, '/'),
      'controller' => $controller,
      'data' => $data,
      'func' => $func,
    ];
  }

  // NOTE: Header JSON
  public static function json()
  {
    header('Content-Type: application/json');
  }

  // NOTE: CORS
  public static function cors()
  {
    if (!self::$appConfig['cors']) {
      if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
      }

      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
      }
    }
  }

  // NOTE: Get Token
  public static function getToken()
  {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
      $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization'])) {
        $headers = trim($requestHeaders['Authorization']);
      }
    }
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
      }
    }
    return $headers;
  }

  // NOTE: Get IP
  public static function getIP()
  {
    //whether ip is from the share internet  
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from the proxy  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from the remote address  
    else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  // NOTE: IP Log
  public static function ipLog()
  {
    $ip = self::getIP();
    $date = date('Y-m-d H:i:s');
    $route = @$_GET["route"];
    $data = '[' . $date . '] IP: ' . $ip . ' Route: ' . $route . PHP_EOL;
    $fp = fopen(Constant::LOGPATH . 'iplogs.txt', 'a');
    fwrite($fp, $data);
  }

  // NOTE: IP Ban
  public static function ipBlock()
  {
    $ip = self::getIP();
    $date = date('Y-m-d H:i:s');
    $route = @$_GET["route"];
    $data = '[' . $date . '] IP: ' . $ip . ' Route: ' . $route . PHP_EOL;
    $fp = fopen(Constant::LOGPATH . 'ipblock.txt', 'a');
    fwrite($fp, $data);
    http_response_code(404);
    exit;
  }

  // Note: Middleware (Ara katman)
  public static function Middleware($path, $func = null)
  {
    $paths = is_array($path) ? $path : [$path];
    foreach ($paths as $path) {
      self::$appMiddlewares[] = [
        'path' => '/' . trim($path, '/'),
        'func' => $func,
      ];
    }
  }

  // NOTE: Modül Yükle
  public static function addModule($appModule = [])
  {
    $appModule = is_array($appModule) ? $appModule : [$appModule];
    self::$appModule = new ModuleInit($appModule);
  }

  // NOTE: Ayarları yükle
  public static function setConfig($appConfig)
  {
    self::$appConfig = $appConfig;
  }

  // NOTE: App'i çalıştır
  public static function run($appUrl = '/')
  {
    $appUrl = Basic::startsWith($appUrl, '/') ? $appUrl : '/' . $appUrl;
    $appUrl = strlen($appUrl) === 1 ? $appUrl : rtrim($appUrl, '/');
    self::$appUrl = $appUrl;

    
    // NOTE: IP Kaydı oluştur
    if (isset($_ENV["IPLOG"]) && $_ENV["IPLOG"]) {
      if ($appUrl == '/project/qrx-dashboard/call/adjust') {
        self::ipBlock();
      }
      self::ipLog($appUrl);
    }

    // NOTE: Middleware çalıştır
    $middleware = Null;
    foreach (self::$appMiddlewares as $appMiddleware) {
      if ($appUrl === $appMiddleware["path"]) {
        $middleware = $appMiddleware["func"]();
      } else if (Basic::startsWith($appUrl, $appMiddleware["path"] . '/')) {
        $middleware = $appMiddleware["func"]();
      } else {
      }
    }
    if ($middleware !== null) {
      if (!$middleware) exit("Erişiminiz engellendi");
      else if ($middleware === 404) exit(self::page404());
    }
    // NOTE: Route'ları çalıştır
    foreach (self::$appRoutes as $appRoute) {
      // NOTE: Route'u bulursa sonlandır
      if (self::Router($appRoute)) exit;
    }
    self::page404();
  }

  // Base URL
  public static function BaseUrl($path = "")
  {
    $url = App::$appConfig["url"];
    $path = ltrim($path, '/');
    $url = rtrim($url, '/');
    return "{$url}/{$path}";
  }

  // Language URL
  public static function LanguageUrl($controller = "")
  {
    $language = $_SESSION["language"];
    $path = null;
    if (array_key_exists($language, self::$languageRoutes)) {
      if (array_key_exists($controller, self::$languageRoutes[$language])) {
        $path = self::$languageRoutes[$language][$controller];
      }
    }
    if ($path === null)
      return self::LanguageUrl("/notFound");
    return self::BaseUrl($path);
  }

  // Redirect
  public static function Redirect($url)
  {
    header("Location: {$url}");
    exit;
  }

  public static function page404()
  {
    http_response_code(404);
    self::Redirect(self::LanguageUrl("/notFound"));
  }
}
