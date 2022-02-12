<?php
defined('SECRET') && SECRET === "@bayramlcm" OR exit('Erişiminiz Engellendi');

class System {

  protected static $route;

  // NOTE: Route Kontrolü
  protected static function Router($route) {
    return Router::route($route);
  }

  
  // NOTE: Composer Başlat
  public static function composer()
  {
    require_once Constant::COMPOSERPATH . 'autoload.php';
  }

}
