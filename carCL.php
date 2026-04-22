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
}

?>