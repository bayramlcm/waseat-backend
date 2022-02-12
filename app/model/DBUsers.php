<?php

class DBUsers extends Model
{

    // Giriş Yap
    function login($data)
    {
        $result = $this->Module->Mysql->get(
            "SELECT * FROM users WHERE (email = ? OR phone = ?)  AND password = ?",
            [$data["email"], $data["email"], $this->passwordCrypt($data["password"])]
        );
        return $result;
    }

    // Kayıt ol
    function register($data) {
        $result = $this->Module->Mysql->insert(
            "INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)",
            [$data["fullname"], $data["email"], $data["phone"], $this->passwordCrypt($data["password"])]
        );
        return $result;
    }

    // Kullanıcı kontrol
    function isUser($data) {
        $result = $this->Module->Mysql->get(
            "SELECT * FROM users WHERE email = ? OR phone = ?",
            [$data["email"], $data["phone"]]
        );
        return $result;
    }

    // Şifre bilgisini kilitle
    private function passwordCrypt($password)
    {
        return md5(md5($password) . ("@bayramlcm"));
    }
}
