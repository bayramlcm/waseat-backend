<?php
defined('SECRET') && SECRET === "@bayramlcm" OR exit('Erişiminiz Engellendi');

class Basic {
  // NOTE: Sınıf ismine çevir
  public static function getClass($route) {
    $class = explode('/', $route);
    return ucfirst(end($class));
  }
  // Starts With
  public static function startsWith($string, $startString) { 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
  } 
}
