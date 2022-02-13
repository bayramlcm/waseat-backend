<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Step extends Controller
{
    function update($data)
    {
        $this->loadModel("Language");
        $this->loadModel("DBSteps");

        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];


        $json = $this->getJSON();
        $params = ["amount", "date"];
        if ($json === null) exit(json_encode($return));
        foreach ($params as $param)
            if (!isset($json[$param])) exit(json_encode($return));

        if (!is_numeric($json["amount"]) || (int) $json["amount"] < 0) exit(json_encode($return));
        if (date("d-m-Y", strtotime($json["date"])) !== date($json["date"])) exit(json_encode($return));

        $json["userId"] = (int) $data["userData"]["id"];
        $json["amount"] = (int) $json["amount"];

        // Adım verisini kontrol et
        if ($stepData = $this->DBSteps->checkStep($json)) {
            // Adım verisi güncelle
            if (!$this->DBSteps->updateStep($json)) exit(json_encode($return));
            $json["id"] = (int) $stepData["id"];
        } else {
            // Adım verisi oluştur
            if (!($id = $this->DBSteps->createStep($json))) exit(json_encode($return));
            $json["id"] = (int) $id;
        }

        $return = [
            "type" => true,
            "data" => $json,
            "message" => $this->Language->text("step update amount"),
        ];

        exit(json_encode($return));
    }
}
