<?php

    $origin      = "Rivadavia, Mendoza, Argentina";
    $destination = "Lima 100, San Martín, Mendoza, Argentina";
    //$destination = "Av San Martin, Lavalle, Mendoza, Argentina";
    $key = "AIzaSyBovhkAkB7CPxVLUl8Cm27Kvwu21Dl42NY";
    $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".urlencode($origin).",IL&destination=" . urlencode( $destination) . "&sensor=false&key=" . $key;
    $jsonfile = file_get_contents($url);
    //echo $jsonfile;
    $jsondata = json_decode($jsonfile, true);
    //print_r ($jsondata);
    echo $jsondata["routes"][0]["legs"][0]["distance"]["value"];

?>
