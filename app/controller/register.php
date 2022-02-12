<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

use Firebase\JWT\JWT;

class Register extends Controller
{
    function index()
    {
        // NOTE: Model Yükleme
        $this->loadModel('Language');
        $this->loadModel('DBUsers');

        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];

        $json = $this->getJSON();
        if ($json === null) exit(json_encode($return));
        $params = ["fullname", "email", "phone", "password",];
        foreach ($params as $param)
            if (!isset($json[$param])) exit(json_encode($return));

        // E-Posta kontrolü
        if (strlen($json["email"]) < 6 || strlen($json["email"]) > 255 || !filter_var($json["email"], FILTER_VALIDATE_EMAIL)) {
            $return["message"] = $this->Language->text("loginRegister email invalid");
            exit(json_encode($return));
        }
        if (strlen($json["fullname"]) < 3 || strlen($json["fullname"]) > 255) {
            $return["message"] = $this->Language->text("loginRegister full name invalid");
            exit(json_encode($return));
        }
        if (strlen($json["phone"]) !== 10) {
            $return["message"] = $this->Language->text("loginRegister phone invalid");
            exit(json_encode($return));
        }
        if (strlen($json["password"]) < 8 || strlen($json["password"]) > 255) {
            $return["message"] = $this->Language->text("loginRegister password invalid");;
            exit(json_encode($return));
        }
        
        // Hesap bulunuyor mu?
        if ($this->DBUsers->isUser($json)) {
            $return["message"] = $this->Language->text("loginRegister already register");
            exit(json_encode($return));
        }

        // Hesabı oluştur
        if ($userId = $this->DBUsers->register($json)) {
            $json["id"] = $userId;
            unset($json["password"]);

            $payload = ["userId" => $userId];
            $token = JWT::encode($payload, App::$appConfig["jwt"], "HS256");

            $return = [
                "type" => true,
                "data" => [
                    "token" => $token,
                    "userData" => $json,
                ],
                "message" => $this->Language->text("loginRegister register successfully"),
            ];
        }
        exit(json_encode($return));
    }
}
