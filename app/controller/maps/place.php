<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');


class Place extends Controller
{
    function __construct()
    {
        // NOTE: Model Yükleme
        $this->loadModel('Language');
        $this->loadModel("APIMaps");
    }

    function autocomplete()
    {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];

        $json = $this->getJSON();
        if ($json === null || !isset($json["text"]) || strlen($json["text"]) > 255) exit(json_encode($return));

        if (!($autocompleteData = $this->APIMaps->placeAutocomplete($json["text"]))) exit(json_encode($return));

        $searchList = [];
        foreach ($autocompleteData["predictions"] as $prediction) {
            $searchList[] = [
                "text" => $prediction["description"],
                "place_id" => $prediction["place_id"],
            ];
        }
        
        $return = [
            "type" => true,
            "data" => $searchList,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }

    function detail() {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];

        $json = $this->getJSON();
        if ($json === null || !isset($json["place_id"]) || strlen($json["place_id"]) > 255) exit(json_encode($return));

        if (!($placeDetail = $this->APIMaps->placeDetail($json["place_id"]))) exit(json_encode($return));

        $return = [
            "type" => true,
            "data" => [
                "lat" => $placeDetail["result"]["geometry"]["location"]["lat"],
                "lng" => $placeDetail["result"]["geometry"]["location"]["lng"],
                "address" => $placeDetail["result"]["formatted_address"],
            ],
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }
}
