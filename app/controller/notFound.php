<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class NotFound extends Controller
{
    function index()
    {
        // NOTE: Model Yükleme
        $this->loadModel('Language');

        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("notFound text"),
        ];

        echo json_encode($return);
    }
}
