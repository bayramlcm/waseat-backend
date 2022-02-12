<?php

class DBCarbonCar extends Model
{

    // Yılları Getir
    function getYears()
    {
        $result = $this->Module->Mysql->getAll(
            "SELECT DISTINCT year FROM carbonCar",
        );
        return $result;
    }

    // Markaları getir
    function getBrands($json) {
        $result = $this->Module->Mysql->getAll(
            "SELECT DISTINCT brand FROM carbonCar WHERE year = ?",
            [$json["year"]]
        );
        return $result;
    }

    // Modelleri getir
    function getModels($json) {
        $result = $this->Module->Mysql->getAll(
            "SELECT DISTINCT model FROM carbonCar WHERE year = ? AND brand = ?",
            [$json["year"], $json["brand"]]
        );
        return $result;
    }

    // Varyant getir
    function getVariants($json) {
        $result = $this->Module->Mysql->getAll(
            "SELECT id, year, brand, model, variant FROM carbonCar WHERE year = ? AND brand = ? AND model = ?",
            [$json["year"], $json["brand"], $json["model"]]
        );
        return $result;
    }
}
