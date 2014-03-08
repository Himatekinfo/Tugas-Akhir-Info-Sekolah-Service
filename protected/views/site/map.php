<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of map
 *
 * @author Syakur Rahman
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
        <meta charset="utf-8">
        <style>
            html, body, #map-canvas {
                margin: 0;
                padding: 0;
                height: 3000px;
            }

            #map-canvas {
                width: 3000px;
            }
        </style>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
        <script>
            var map;
            function initialize() {
                var mapOptions = {
                    zoom: 11,
                    center: new google.maps.LatLng(-6.594015, 106.799759),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById('map-canvas'),
                        mapOptions);
<?php $i = 0; ?>
<?php foreach (Node::model()->findAll() as $data) { ?>
                    var marker<?php echo $i; ?> = new google.maps.Marker({
                        position: new google.maps.LatLng(<?php echo $data->Latitude; ?>, <?php echo $data->Longitude; ?>),
                        map: map,
                        title: '<?php echo str_replace("'", "\'", trim(preg_replace('/\s+/', ' ', $data->Description))) ?>'
                    });

                    google.maps.event.addListener(marker<?php echo $i; ?>, 'click', function() {
                        infowindow<?php echo $i; ?>.open(map, marker<?php echo $i; ?>);
                    });

    <?php $i++; ?>
<?php } ?>
            }

            google.maps.event.addDomListener(window, 'load', initialize);

        </script>
    </head>
    <body>
        <div id="map-canvas"></div>

    </body>
</html>