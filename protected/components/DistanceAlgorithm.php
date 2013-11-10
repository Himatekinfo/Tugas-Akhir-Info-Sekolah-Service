<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DistanceAlgorithm
 *
 * @author Syakur Rahman
 */
class DistanceAlgorithm {

    public static $PIx = 3.141592653589793;
    public static $RADIUS = 6378.16;

    public static function DistanceBetweenPlaces($lng1, $lat1, $lng2, $lat2) {
        $dlon = DistanceAlgorithm::Radians($lng2 - $lng1);
        $dlat = DistanceAlgorithm::Radians($lat2 - $lat1);

        $a = sin(($dlat / 2) * sin($dlat / 2)) + cos(DistanceAlgorithm::Radians($lat1)) * cos(DistanceAlgorithm::Radians($lat2)) * (sin($dlon / 2) * sin($dlon / 2));
        $angle = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $angle * DistanceAlgorithm::$RADIUS;
    }

    public static function Radians($x) {
        return $x * DistanceAlgorithm::$PIx / 180;
    }

    public static function DrivingDistanceBetweenPlaces($lng1, $lat1, $lng2, $lat2) {
        $url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$lat1,$lng1&destinations=$lat2,$lng2&sensor=false";
        //print($url . "<br />");
        $result = file_get_contents($url);
        $data = json_decode(utf8_encode($result), true);

        return $data["rows"][0]["elements"][0]["distance"]["value"] / 1000;
    }

}

?>
