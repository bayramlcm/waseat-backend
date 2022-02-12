<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class LanguageEN extends Model
{
    public $LANG = ["lang" => "en", "locale" => "en_US"];

    public function __construct()
    {
        $this->LANG = array_merge(
            $this->LANG,
            $this->notFound(),
        );
    }

    // 404
    private function notFound()
    {
        return [
            "notFound text" => "Invalid address.",
        ];
    }
}
