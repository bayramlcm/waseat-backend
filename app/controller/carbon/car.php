<?php defined('SECRET') && SECRET === "@bayramlcm" or exit('Erişiminiz Engellendi');

class Car extends Controller
{
    function __construct()
    {
        // NOTE: Model Yükleme
        $this->loadModel('Language');
        $this->loadModel('DBCarbonCar');
    }

    // Yılları getir
    function years()
    {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];

        if (!($years = $this->DBCarbonCar->getYears())) exit(json_encode($return));

        // dictionary to array
        $years = array_map(function ($year) {
            return $year["year"];
        }, $years);

        $return = [
            "type" => true,
            "data" => $years,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }

    // Markaları getir
    function brands()
    {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];
        $json = $this->getJSON();
        if ($json === null) exit(json_encode($return));
        if (!isset($json["year"]) || strlen($json["year"]) !== 4) exit(json_encode($return));

        if (!($brands = $this->DBCarbonCar->getBrands($json))) exit(json_encode($return));
        
        // dictionary to array
        $brands = array_map(function ($brand) {
            return $brand["brand"];
        }, $brands);
        
        $return = [
            "type" => true,
            "data" => $brands,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));

    }

    // Modelleri getir
    function models()
    {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];
        $json = $this->getJSON();
        if ($json === null) exit(json_encode($return));
        if (!isset($json["year"], $json["brand"]) || strlen($json["year"]) !== 4) exit(json_encode($return));
        if (strlen($json["brand"]) < 3 || strlen($json["brand"]) > 255) exit(json_encode($return));

        if (!($models = $this->DBCarbonCar->getModels($json))) exit(json_encode($return));

        // dictionary to array
        $models = array_map(function ($model) {
            return $model["model"];
        }, $models);

        $return = [
            "type" => true,
            "data" => $models,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }

    // Varyantları getir
    function variants()
    {
        $return = [
            "type" => false,
            "data" => null,
            "message" => $this->Language->text("other something went wrong"),
        ];
        $json = $this->getJSON();
        if ($json === null) exit(json_encode($return));
        if (!isset($json["year"], $json["brand"]) || strlen($json["year"]) !== 4) exit(json_encode($return));
        if (strlen($json["brand"]) < 3 || strlen($json["brand"]) > 255) exit(json_encode($return));
        if (strlen($json["model"]) < 3 || strlen($json["model"]) > 255) exit(json_encode($return));

        if (!($variants = $this->DBCarbonCar->getVariants($json))) exit(json_encode($return));

        $return = [
            "type" => true,
            "data" => $variants,
            "message" => $this->Language->text("carbon successfully brought"),
        ];

        exit(json_encode($return));
    }

    
}
