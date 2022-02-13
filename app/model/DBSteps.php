<?php

class DBSteps extends Model
{

    // Adım Miktarını Güncelle
    function updateAmount($json)
    {
        $result = $this->Module->Mysql->update(
            "UPDATE steps SET amount = ? WHERE id = ?",
            [$json["amount"], $json["id"]]
        );
        return $result;
    }

    // Böyle bir adım verisi bulunuyor mu?
    function checkStep($json)
    {
        $result = $this->Module->Mysql->get(
            "SELECT * FROM steps WHERE userId = ? AND date = ?",
            [$json["userId"], $this->dateConvert($json["date"])]
        );
        return $result;
    }

    // Adım verisini güncelle
    function updateStep($json) {
        $result = $this->Module->Mysql->update(
            "UPDATE steps SET amount = ? WHERE userId = ? AND date = ?",
            [$json["amount"], $json["userId"], $this->dateConvert($json["date"])]
        );
        return $result;
    } 

    // Adım verisi oluştur
    function createStep($json) {
        $result = $this->Module->Mysql->insert(
            "INSERT INTO steps (userId, date, amount) VALUES (?, ?, ?)",
            [$json["userId"], $this->dateConvert($json["date"]), $json["amount"]]
        );
        return $result;
    }

    private function dateConvert($date) {
        return date("Y/m/d H:i:s", strtotime($date));
    }
}
