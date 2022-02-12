<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Router
{

  // NOTE: Controller ı kontrol et ve çalıştır
  private static function checkFile($files)
  {
    if (count($files) > 0) {
      foreach ($files as $file) {
        include_once $file['file'];
        if (method_exists($file['class'], $file['method'])) {
          return $file;
        }
      }
    }
  }

  // NOTE: Controller ı tara
  private static function searchFiles($route, $method = Null)
  {
    $files = [];
    $routeParse = explode('/', $route);
    $filename = array_pop($routeParse);
    $path = Constant::CONTROLLLERPATH . implode(DS, $routeParse);
    if (count($routeParse) > 0 && empty($method)) {
      // NOTE: APP_URL/ex/deneme -> Controller/ex.php -> function deneme
      if (file_exists($path . '.php')) {
        $files[] = [
          'file' => $path . '.php',
          'method' => $filename,
          'class' => Basic::getClass(end($routeParse)),
        ];
      }
    }
    // NOTE: APP_URL/ex/deneme -> Controller/ex/deneme.php  -> function index
    if (file_exists($path . DS . $filename . '.php')) {
      $files[] = [
        'file' => $path . DS . $filename . '.php',
        'method' => empty($method) ? 'index' : $method,
        'class' => Basic::getClass($filename),
      ];
    }
    // NOTE: APP_URL/ex/deneme -> Controller/ex/deneme/index.php -> function index
    if (file_exists($path . DS . $filename . DS . 'index.php')) {
      $files[] = [
        'file' => $path . DS . $filename . DS . 'index.php',
        'method' => empty($method) ? 'index' : $method,
        'class' => Basic::getClass($filename),
      ];
    }
    return array_reverse($files);
  }

  // NOTE: Controller Çalıştır
  private static function run($route, $data = null, $method = Null)
  {
    $file = self::checkFile(
      self::searchFiles($route, $method),
      $method
    );
    if (empty($file)) exit('Controller bulunamadı.');
    call_user_func_array(
      [new $file['class'](), $file['method']],
      $data === null ? [] : [$data]
    );
    return True;
  }

  // NOTE: Ayrıştır
  public static function route($route)
  {
    // $route['path'] = rtrim($route['path'], '/');
    $route['methods'] = is_array($route['methods']) ? $route['methods'] : [$route['methods']];
    $route['controller'] = empty($route['controller']) ? Null : trim($route['controller'], '/');
    $route['controllerMethod'] = empty($route['controllerMethod']) ? Null : $route['controllerMethod'];
    $route['data'] = empty($route["data"]) ? null : $route['data'];

    if (in_array($_SERVER['REQUEST_METHOD'], $route['methods'])) {
      if ($route['path'] == App::$appUrl) {
        // NOTE: Controller ı başlat
        return self::run(
          empty($route['controller']) ? $route['path'] : $route['controller'],
          $route['data'],
          $route['controllerMethod']
        );
      }
    }
  }
}
