<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class LanguageTR extends Model
{
    public $LANG = ["lang" => "tr", "locale" => "tr_TR"];

    public function __construct()
    {
        $this->LANG = array_merge(
            $this->LANG,
            $this->notFound(),
            $this->other(),
            $this->loginRegister(),
            $this->carbon(),
        );
    }

    // Carbon
    private function carbon() {
        return [
            "carbon successfully brought" => "Başarıyla getirildi."
        ];
    }

    // Login / Register
    private function loginRegister()
    {
        return [
            "loginRegister password invalid" => "Şifreniz en az 8 karakter olmalıdır.",
            "loginRegister email invalid" => "Geçersiz E-Posta adresi.",
            "loginRegister login successfully" => "Başarıyla giriş yaptınız.",
            "loginRegister email or password invalid" => "E-Posta veya şifreniz hatalı.",
            "loginRegister full name invalid" => "Adı Soyadı alanını doğru girin..",
            "loginRegister phone invalid" => "Geçersiz telefon numarası.",
            "loginRegister already register" => "Zaten kayıtlı hesabınız bulunuyor.",
            "loginRegister register successfully" => "Başarıyla kayıtlı oldunuz.",
        ];
    }

    // 404
    private function notFound()
    {
        return [
            "notFound text" => "Geçersiz bağlantı adresi.",
        ];
    }

    // other
    private function other()
    {
        return [
            "other something went wrong" => "Bir şeyler yanlış gitti."
        ];
    }
}
