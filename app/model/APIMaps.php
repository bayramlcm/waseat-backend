<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class APIMaps extends Model
{
    private $client;
    private $MAP_API_KEY;

    function __construct()
    {
        $this->loadModel("Language");
        $this->MAP_API_KEY = App::$appConfig["MAPS_API_KEY"];
        $this->client = new Client([
            "base_uri" => "https://maps.googleapis.com/maps/api/"
        ]);
    }

    // İstek ve durumu kontrol edip yanıtı döndür
    private function _request($options)
    {
        try {
            $response = $this->client->request("GET", $options["path"], [
                "query" => array_merge([
                    "key" => $this->MAP_API_KEY,
                    "language" => $this->Language->text("locale"),
                    "region" => "tr",
                ], $options)
            ]);

            $json = json_decode($response->getBody(), true);
            if ($json === null) return false;
            if (!array_key_exists("status", $json)) return false;
            if ($json["status"] === "OK") return $json;
        } catch (RequestException $th) {
            return false;
        }
        return false;
    }

    function placeAutocomplete($text)
    {
        $options = [
            "path" => "place/queryautocomplete/json",
            "input" => $text,
        ];
        if (!($json = $this->_request($options))) return false;
        return $json;
    }

    function placeDetail($placeId)
    {
        $options = [
            "path" => "place/details/json",
            "place_id" => $placeId
        ];
        if (!($json = $this->_request($options))) return false;
        return $json;
    }
}
