<?php 

class Many_Classes {

    private $name;

    function __construct($name) {
        $this->name = $name;
    }

    function zap() {
        echo 'Zap! Hello ' . $name;
    }
}