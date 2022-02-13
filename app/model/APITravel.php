<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class APITravel extends Model
{
    private $client;
    private $MAP_API_KEY;

    private $from;
    private $to;

    private $routes = [
        "walking" => [],
        "bicycling" => [],
        "driving" => [],
        "transit" => [],
        "scooter" => [],
    ];

    function __construct()
    {
        $this->loadModel("Language");
        $this->MAP_API_KEY = App::$appConfig["MAPS_API_KEY"];
        $this->client = new Client([
            "base_uri" => "https://maps.googleapis.com/maps/api/directions/json"
        ]);
    }

    public function getAll()
    {
        return $this->routes;
    }

    // İstek ve durumu kontrol edip yanıtı döndür
    private function _request($options = [])
    {
        try {
            $response = $this->client->request("GET", "", [
                "query" => array_merge([
                    "origin" => $this->from,
                    "destination" => $this->to,
                    "key" => $this->MAP_API_KEY,
                    "language" => $this->Language->text("lang"),
                    "departure_time" => "1644846904",
                    "alternatives" => "true",
                    // "traffic_model" => "optimistic",
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

    // nereden nereye konum bilgisi
    function load($json)
    {
        $this->from = $json["from"];
        $this->to = $json["to"];
        $this->getWalking();
        $this->getTransit();
        $this->getDriving();
    }

    function getWalking()
    {
        $options = [
            "mode" => "walking",
        ];
        if (!($json = $this->_request($options))) return false;

        foreach ($json["routes"] as $route) {
            if (!array_key_exists("legs", $route) || count($route["legs"]) === 0) continue;
            $leg = $route["legs"][0];
            $useTravels = [];
            $steps = [];
            foreach ($leg["steps"] as $step) {
                if (!in_array($step["travel_mode"], $useTravels)) {
                    $useTravels[] = $step["travel_mode"];
                }
                $steps[] = array_merge([
                    "co2" => 0,
                    "duration_minute" =>  ceil($step["duration"]["value"] / 60) . " " . $this->Language->text("other min"),
                    "travel" => $step["travel_mode"],
                    "duration" => $step["duration"]["text"],
                    "duration_value" => $step["duration"]["value"],
                    "distance" => $step["distance"]["text"],
                    "distance_value" => $step["distance"]["value"],
                    "end_location" => $step["end_location"],
                    "start_location" => $step["start_location"],
                ]);
            }
            $durationName = $leg["duration"]["text"];
            $durationValue = ceil($leg["duration"]["value"] / 60);
            $startTime = date('H:i');
            $endTime = date('H:i', strtotime("+{$durationValue} minutes", strtotime(date("Y-m-d H:i:s"))));
            $this->routes["walking"][] = [
                "co2" => 0,
                "duration_minute" =>  ceil($durationValue) . " " . $this->Language->text("other min"),
                "use_travels" => $useTravels,
                "duration" => $durationName,
                "duration_value" => $durationValue,
                "distance" => $leg["distance"]["text"],
                "distance_value" => $leg["distance"]["value"],
                "start_time" => $startTime,
                "end_time" => $endTime,
                "end_address" => $leg["end_address"],
                "end_location" => $leg["end_location"],
                "start_location" => $leg["start_location"],
                "start_address" => $leg["start_address"],
                "steps" => $this->groupByTravel($steps),
            ];
        }
    }

    // Kendi aracınla
    function getDriving()
    {
        $options = [
            "mode" => "driving",
        ];
        if (!($json = $this->_request($options))) return false;
        foreach ($json["routes"] as $route) {
            if (!array_key_exists("legs", $route) || count($route["legs"]) === 0) continue;
            $leg = $route["legs"][0];
            $useTravels = [];
            $drivingSteps = [];
            $bicyclingSteps = [];
            $scooterSteps = [];
            foreach ($leg["steps"] as $step) {
                if (!in_array($step["travel_mode"], $useTravels)) {
                    $useTravels[] = $step["travel_mode"];
                }
                $drivingSteps[] = array_merge([
                    "co2" => 120,
                    "duration_minute" =>  ceil($step["duration"]["value"] / 60) . " " . $this->Language->text("other min"),
                    "travel" => $step["travel_mode"],
                    "duration" => $step["duration"]["text"],
                    "duration_value" => $step["duration"]["value"],
                    "distance" => $step["distance"]["text"],
                    "distance_value" => $step["distance"]["value"],
                    "travel" => $step["travel_mode"],
                    "end_location" => $step["end_location"],
                    "start_location" => $step["start_location"],
                ]);
                // bicycling
                $durationValue = $this->floatToMinute($step["distance"]["value"] / 20000) * 60;
                $durationText = $this->secondsToTime($durationValue);
                $bicyclingSteps[] = [
                    "co2" => 0,
                    "duration_minute" =>  ceil($durationValue) . " " . $this->Language->text("other min"),
                    "duration" => $durationText,
                    "duration_value" => $durationValue,
                    "distance" => $step["distance"]["text"],
                    "distance_value" => $step["distance"]["value"],
                    "travel" => "BICYCLING",
                    "end_location" => $step["end_location"],
                    "start_location" => $step["start_location"],
                ];
                // scooter
                $durationValue = $this->floatToMinute($step["distance"]["value"] / 25000) * 60;
                $durationText = $this->secondsToTime($durationValue);
                $scooterSteps[] = [
                    "co2" => 25,
                    "duration_minute" =>  ceil($durationValue) . " " . $this->Language->text("other min"),
                    "duration" => $durationText,
                    "duration_value" => $durationValue,
                    "distance" => $step["distance"]["text"],
                    "distance_value" => $step["distance"]["value"],
                    "travel" => "SCOOTER",
                    "end_location" => $step["end_location"],
                    "start_location" => $step["start_location"],
                ];
            }
            $durationText = $leg["duration"]["text"];
            $durationValue = ceil($leg["duration"]["value"] / 60);
            if (array_key_exists("duration_in_traffic", $leg)) {
                $durationText = $leg["duration_in_traffic"]["text"];
                $durationValue = ceil($leg["duration_in_traffic"]["value"] / 60);
            }
            $startTime = date('H:i');
            $endTime = date('H:i', strtotime("+{$durationValue} minutes", strtotime(date("Y-m-d H:i:s"))));
            $this->routes["driving"][] = [
                "co2" => 1200,
                "duration_minute" =>  ceil($leg["duration"]["value"] / 60) . " " . $this->Language->text("other min"),
                "use_travels" => $useTravels,
                "duration" => $durationText,
                "duration_value" => $durationValue,
                "distance" => $leg["distance"]["text"],
                "distance_value" => $leg["distance"]["value"],
                "start_time" => $startTime,
                "end_time" => $endTime,
                "end_address" => $leg["end_address"],
                "end_location" => $leg["end_location"],
                "start_location" => $leg["start_location"],
                "start_address" => $leg["start_address"],
                "steps" => $this->groupByTravel($drivingSteps),
            ];
            if ($leg["distance"]["value"] < 1) return true;
            // bicycling
            $durationValue = $this->floatToMinute($leg["distance"]["value"] / 20000) * 60;
            $durationText = $this->secondsToTime($durationValue);
            $startTime = date('H:i');
            $endTime = date('H:i', strtotime("+{$durationValue} minutes", strtotime(date("Y-m-d H:i:s"))));
            $this->routes["bicycling"][] = [
                "use_travels" => [
                    "BICYCLING"
                ],
                "co2" => 0,
                "duration_minute" =>  ceil($durationValue) . " " . $this->Language->text("other min"),
                "start_time" => $startTime,
                "end_time" => $endTime,
                "duration" => $durationText,
                "duration_value" => $durationValue,
                "distance" => $leg["distance"]["text"],
                "distance_value" => $leg["distance"]["value"],
                "end_address" => $leg["end_address"],
                "end_location" => $leg["end_location"],
                "start_location" => $leg["start_location"],
                "start_address" => $leg["start_address"],
                "steps" => $this->groupByTravel($bicyclingSteps),
            ];
            // scooter
            $durationValue = $this->floatToMinute($leg["distance"]["value"] / 25000) * 60;
            $durationText = $this->secondsToTime($durationValue);
            $startTime = date('H:i');
            $endTime = date('H:i', strtotime("+{$durationValue} minutes", strtotime(date("Y-m-d H:i:s"))));
            $this->routes["scooter"][] = [
                "use_travels" => [
                    "SCOOTER"
                ],
                "co2" => 200,
                "duration_minute" =>  ceil($durationValue) . " " . $this->Language->text("other min"),
                "duration" => $durationText,
                "duration_value" => $durationValue,
                "distance" => $leg["distance"]["text"],
                "distance_value" => $leg["distance"]["value"],
                "start_time" => $startTime,
                "end_time" => $endTime,
                "end_address" => $leg["end_address"],
                "end_location" => $leg["end_location"],
                "start_location" => $leg["start_location"],
                "start_address" => $leg["start_address"],
                "steps" => $this->groupByTravel($scooterSteps),
            ];
        }
        return true;
    }

    // Toplu taşıma
    function getTransit()
    {
        $options = [
            "mode" => "transit",
            "transit_mode" => "bus|subway|tram"
        ];
        if (!($json = $this->_request($options))) return false;
        foreach ($json["routes"] as $route) {
            if (!array_key_exists("legs", $route) || count($route["legs"]) === 0) continue;
            $leg = $route["legs"][0];
            $useTravels = [];
            $steps = [];
            foreach ($leg["steps"] as $step) {
                if (!in_array($step["travel_mode"], $useTravels)) {
                    $useTravels[] = $step["travel_mode"];
                }
                $steps[] = array_merge([
                    "co2" => 15,
                    "duration_minute" =>  ceil($step["duration"]["value"] / 60) . " " . $this->Language->text("other min"),
                    "travel" => $step["travel_mode"],
                    "duration" => $step["duration"]["text"],
                    "duration_value" => $step["duration"]["value"],
                    "distance" => $step["distance"]["text"],
                    "distance_value" => $step["distance"]["value"],
                    "end_location" => $step["end_location"],
                    "start_location" => $step["start_location"],
                ], $step["travel_mode"] === "TRANSIT" ? [
                    "transit_type" => $step["transit_details"]["line"]["vehicle"]["name"],
                ] : []);
            }
            $this->routes["transit"][] = [
                "co2" => 250,
                "duration_minute" =>  ceil($leg["duration"]["value"] / 60) . " " . $this->Language->text("other min"),
                "use_travels" => $useTravels,
                "duration" => $leg["duration"]["text"],
                "duration_value" => $leg["duration"]["value"],
                "distance" => $leg["distance"]["text"],
                "distance_value" => $leg["distance"]["value"],
                "start_time" => isset($leg["arrival_time"]) ? $leg["arrival_time"]["text"] : "",
                "end_time" => isset($leg["departure_time"]) ? $leg["departure_time"]["text"] : "",
                "end_address" => $leg["end_address"],
                "end_location" => $leg["end_location"],
                "start_location" => $leg["start_location"],
                "start_address" => $leg["start_address"],
                "steps" => $this->groupByTravel($steps),
            ];
        }
        return true;
    }

    // Seyahat tipine göre grupla
    function groupByTravel($steps)
    {
        $newSteps = [];
        $newStep = [];
        $travel = "";
        foreach ($steps as $step) {
            if ($travel != $step["travel"]) {
                if (count($newStep) > 0) $newSteps[] = $newStep;
                $travel = $step["travel"];
                $newStep = $step;
            }
            $newStep["co2"] += $step["co2"];
            $newStep["duration_value"] += $step["duration_value"];
            $newStep["distance_value"] += $step["distance_value"];

            $newStep["duration"] = $this->secondsToTime($newStep["duration_value"]);
            $newStep["duration_minute"] = ceil($newStep["duration_value"] / 60) . " " . $this->Language->text("other min");
            $newStep["distance"] = $this->convertMeter($step["distance_value"]);
            $newStep["end_location"] = $step["end_location"];
        }
        if (count($newStep) > 0) $newSteps[] = $newStep;
        return $newSteps;
    }


    private function floatToMinute($float)
    {
        // echo sprintf('%02d:%02d', (int) $time, fmod($time, 1) * 60);
        return ceil((int) $float * 60 + fmod($float, 1) * 60);
    }

    private function convertMeter($meter)
    {
        $kilometer = round($meter / 1000, 1);
        if ($meter < 1000)
            return "{$meter} m";
        else
            return "{$kilometer} km";
    }

    private function secondsToTime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        if ((int) $dtF->diff($dtT)->format('%a') !== 0)
            return $dtF->diff($dtT)->format(
                "%a " . $this->Language->text("other day") . " " .
                    "%h " . $this->Language->text("other hour") . " " .
                    "%i " . $this->Language->text("other minute")
            );
        else if ((int) $dtF->diff($dtT)->format('%h') !== 0)
            return $dtF->diff($dtT)->format(
                "%h " . $this->Language->text("other hour") . " " .
                    "%i " . $this->Language->text("other minute")
            );
        else
            return $dtF->diff($dtT)->format(
                "%i " . $this->Language->text("other minute")
            );
    }
}
