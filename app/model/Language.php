<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Language extends Model
{
    function text($text)
    {
        if ($_SESSION["language"] == "tr") {
            $this->loadModel('Language/LanguageTR', 'LanguageTR');
            $LANG = $this->LanguageTR->LANG;
        } else if ($_SESSION["language"] == "de") {
            $this->loadModel('Language/LanguageDE', 'LanguageDE');
            $LANG = $this->LanguageDE->LANG;
        } else {
            $this->loadModel('Language/LanguageEN', 'LanguageEN');
            $LANG = $this->LanguageEN->LANG;
        }
        return array_key_exists($text, $LANG) ? $LANG[$text] : "\$\$$text\$\$";
    }
}
