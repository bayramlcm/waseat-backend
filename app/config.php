<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

App::setConfig(
  [
    'url' => $_ENV["URL"],
    'mysql' => [
      'host' => $_ENV["MYSQL_HOST"],
      'user' => $_ENV["MYSQL_USER"],
      'pass' => $_ENV["MYSQL_PASS"],
      'db' => $_ENV["MYSQL_DB"],
    ],
    'jwt' => $_ENV["JWT"],
    'cors' => $_ENV["CORS"],
  ]
);


// NOTE: İstenen modüller
$modules = [];
if ($_ENV["MYSQL"])
  $modules[] = 'mysql';
if ($_ENV["MINIFY"])
  $modules[] = 'minify';

App::addModule($modules);

App::json();
App::cors();
