<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class LanguageTR extends Model
{
    public $LANG = ["lang" => "tr", "locale" => "tr_TR"];

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
            "notFound text" => "Geçersiz bağlantı adresi.",
        ];
    }
}
