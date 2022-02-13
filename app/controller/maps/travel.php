<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');


class Travel extends Controller
{
    function index()
    {
        // NOTE: Model Yükleme
        $this->loadModel('Language');
        $this->loadModel("APITravel");

        $return = [
            "type" => false,
            "data" => $this->APITravel->getAll(),
            "message" => $this->Language->text("other something went wrong"),
        ];

        $json = $this->getJSON();
        
        if ($json === null || !isset($json["from"]) || strlen($json["from"]) < 1 || strlen($json["from"]) > 255) exit(json_encode($return));
        if (!isset($json["to"]) || strlen($json["to"]) < 1 || strlen($json["to"]) > 255) exit(json_encode($return));
        
        $this->APITravel->load($json);

        if (!($travel = $this->APITravel->getAll())) exit(json_encode($return));
        
        $return = [
            "type" => true,
            "data" => $travel,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }
}
