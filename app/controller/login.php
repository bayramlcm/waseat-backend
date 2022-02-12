<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

use Firebase\JWT\JWT;

class Login extends Controller
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
        $params = ["email", "password"];
        if ($json === null) exit(json_encode($return));
        foreach ($params as $param)
            if (!isset($json[$param])) exit(json_encode($return));

        // E-Posta kontrolü
        if (strlen($json["email"]) < 4 || strlen($json["email"]) > 255) {
            $return["message"] = $this->Language->text("loginRegister email invalid");
            exit(json_encode($return));
        }

        if (strlen($json["password"]) < 8 || strlen($json["password"]) > 255) {
            $return["message"] = $this->Language->text("loginRegister password invalid");
            exit(json_encode($return));
        }

        // Giriş Yap
        if ($userData = $this->DBUsers->login($json)) {
            unset($userData["password"]);

            $payload = ["userId" => $userData["id"]];
            $token = JWT::encode($payload, App::$appConfig["jwt"], "HS256");

            $return = [
                "type" => true,
                "data" => [
                    "token" => $token,
                    "userData" => $userData,
                ],
                "message" => $this->Language->text("loginRegister login successfully"),
            ];
        } else {
            $return["message"] = $this->Language->text("loginRegister email or password invalid");;
        }
        exit(json_encode($return));
    }
}
