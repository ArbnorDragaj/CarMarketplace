<?php

class Car {

    private $brand;
    private $model;
    private $fuel;
    private $body;
    private $year;
    private $price;
    private $image;

    public function __construct($brand, $model, $fuel, $body, $year, $price, $image) {
        $this->brand = $brand;
        $this->model = $model;
        $this->fuel = $fuel;
        $this->body = $body;
        $this->year = $year;
        $this->price = $price;
        $this->image = $image;
    }

    
    public function getBrand() {
        return $this->brand;
    }

    public function getModel() {
        return $this->model;
    }

    public function getFuel() {
        return $this->fuel;
    }

    public function getBody() {
        return $this->body;
    }

    public function getYear() {
        return $this->year;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getImage() {
        return $this->image;
    }


    public function setPrice($price) {
        $this->price = $price;
    }


    public function getFullName() {
        return $this->brand . " " . $this->model;
    }
}

?>