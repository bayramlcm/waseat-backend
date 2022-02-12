<?php
defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Functions
{
    protected $Module;

    function __construct()
    {
        // NOTE: Modülü yükle
        $this->Module = App::$appModule;
    }


    // NOTE: Model Yükle
    protected function loadModel($name, $class=null)
    {
        include_once Constant::MODELPATH . $name . '.php';
        $name = empty($class) ? $name : $class;
        $this->$name = new $name();
    }

    // NOTE: Route bilgisini getir
    function route()
    {
        return App::$appUrl;
    }

    // NOTE: Base URL
    function baseUrl($path = "")
    {
        return App::BaseUrl($path);
    }

    // NOTE: Language URL
    function languageUrl($controller)
    {
        return App::LanguageUrl($controller);
    }
}
