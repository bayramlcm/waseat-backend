<?php
defined('SECRET') && SECRET === "@bayramlcm" OR exit('Erişiminiz Engellendi');

class Constant {
  public const CONTROLLLERPATH = APPPATH . 'controller' . DS;
  public const MODELPATH = APPPATH . 'model' . DS;
  public const VIEWPATH = APPPATH . 'view' . DS;
  public const MODULEPATH = SYSTEMPATH . 'module' . DS;
  public const COMPOSERPATH = ROOT . 'vendor' . DS;
  public const TMPPATH = ROOT . 'tmp' . DS;
  public const LOGPATH = ROOT . 'logs' . DS;
}
