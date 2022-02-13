<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class LanguageEN extends Model
{
    public $LANG = ["lang" => "en", "locale" => "en_US"];

    public function __construct()
    {
        $this->LANG = array_merge(
            $this->LANG,
            $this->notFound(),
            $this->other(),
            $this->loginRegister(),
            $this->carbon(),
            $this->step(),
        );
    }

    // Step
    private function step() {
        return [
            "step update amount" => "Step data updated successfull.",
        ];
    }

    // Carbon
    private function carbon() {
        return [
            "carbon successfully brought" => "Successfully brought."
        ];
    }

    // Login / Register
    private function loginRegister() {
        return [
            "loginRegister password invalid" => "Your password must be at least 8 characters.",
            "loginRegister email invalid" => "Invalid e-mail address.",
            "loginRegister login successfully" => "You have successfully logged in.",
            "loginRegister email or password invalid" => "Your e-mail or password is incorrect.",
            "loginRegister full name invalid" => "Enter your full name correctly.",
            "loginRegister phone invalid" => "Enter your phone correctly.",
            "loginRegister already register" => "You have already registered",
            "loginRegister register successfully" => "You have successfully registered.",
        ];
    } 

    // 404
    private function notFound()
    {
        return [
            "notFound text" => "Invalid address.",
        ];
    }

    // other
    private function other()
    {
        return [
            "other day" => "day",
            "other hour" => "hour",
            "other minute" => "mins",
            "other min" => "mins",
            "other something went wrong" => "Something went wrong."
        ];
    }
}
