<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Controller extends Functions
{
  // NOTE: View Yükle
  protected function loadView($name, $vars = [])
  {
    ini_restore('include_path');
    extract(
      (array) $vars
      // compact((array) $vars)
      // EXTR_PREFIX_ALL,
      // "data"
    );
    include_once Constant::VIEWPATH . $name . '.php';
  }

  // NOTE: Yönlendir
  function redirect($url)
  {
    return App::Redirect($url);
  }

  // NOTE: Gelen JSON datasını çevirir
  function getJSON()
  {
    $json = file_get_contents('php://input');
    return $json !== null ? json_decode($json, TRUE) : [];
  }
}
