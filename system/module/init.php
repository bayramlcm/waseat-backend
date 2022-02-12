<?php
defined('SECRET') && SECRET === "@bayramlcm" OR exit('Erişiminiz Engellendi');

class ModuleInit {

  function __construct($appModules) {
    // NOTE: Modülleri listele
    foreach($appModules as $appModule) {
      include_once Constant::MODULEPATH . $appModule . '.php';
      $appModuleClass = Basic::getClass($appModule);
      $this->$appModuleClass = new $appModuleClass();
    }
  }

}
