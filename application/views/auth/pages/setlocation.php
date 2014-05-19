<html>
<head>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
     <!--[if lte IE 8]>
         <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.ie.css" />
     <![endif]-->

    <?php echo $this->ag_asset->load_css('font-awesome.min.css');?>
    <?php echo $this->ag_asset->load_css('leaflet.awesome-markers.css');?>
    <?php echo $this->ag_asset->load_css('MarkerCluster.css');?>
    <?php echo $this->ag_asset->load_css('MarkerCluster.Default.css');?>
    <?php echo $this->ag_asset->load_css('Leaflet.Coordinates-0.1.4.css');?>


    <!--[if lte IE 8]>
        <?php echo $this->ag_asset->load_css('MarkerCluster.Default.ie.css');?>
        <?php echo $this->ag_asset->load_css('Leaflet.Coordinates-0.1.4.ie.css');?>
    <![endif]-->

    <?php echo $this->ag_asset->load_css('l.geosearch.css');?>

    <?php echo $this->ag_asset->load_script('jquery-1.7.1.min.js');?>

    <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script>

    <?php echo $this->ag_asset->load_script('leaflet.awesome-markers.min.js');?>
    <?php echo $this->ag_asset->load_script('leaflet.polylineDecorator.min.js');?>
    <?php echo $this->ag_asset->load_script('leaflet.markercluster.js');?>

    <?php echo $this->ag_asset->load_script('lsearch/l.control.geosearch.js');?>
    <?php echo $this->ag_asset->load_script('lsearch/l.geosearch.provider.openstreetmap.js');?>
    <?php echo $this->ag_asset->load_script('lsearch/l.geosearch.provider.google.js');?>
    <?php echo $this->ag_asset->load_script('Leaflet.Coordinates-0.1.4.min.js');?>



    <style type="text/css">
    .awesome-marker i {
        color: #333;
        margin-top: 2px;
        display: inline-block;
        font-size: 10px;
    }

    body{
        font-family: Calibri,Helvetica, sans-serif;
    }

    dt{
        font-weight: bold;
        float: left;
        width:50%;
    }

    dd{
        clear: right;
    }

    .use-loc{
        cursor: pointer;
    }

    </style>

    <script>
        var asInitVals = new Array();

        CM_ATTRIB = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
                '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                'Imagery © <a href="http://cloudmade.com">CloudMade</a>';

        CM_URL = 'http://{s}.tile.cloudmade.com/bc43265d42be42e3bfd603f12a8bf0e9/997/256/{z}/{x}/{y}.png';

        OSM_URL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        OSM_ATTRIB = '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        var new_marker;

        var map;

        function setupMap(lat, lon){

            var deflat = (lat != null)?lat:-6.17742;
            var deflon = (lon != null)?lon:106.828308;

            map = L.map('map').setView([deflat,deflon], 13);

            var lineWeight = 4;

            L.tileLayer(OSM_URL, {
                attribution: OSM_ATTRIB,
                maxZoom: 18
            }).addTo(map);

            new L.Control.GeoSearch({
                provider: new L.GeoSearch.Provider.Google(),
                position: 'topcenter',
                showMarker: false
            }).addTo(map);

            L.control.coordinates({
                position:'bottomleft', //optional default "bootomright"
                decimals:4, //optional default 4
                decimalSeperator:'.', //optional default "."
                labelTemplateLat:'Latitude: <input value="{y}" id="inLat">', //optional default "Lat: {y}"
                labelTemplateLng:'Longitude: <input value="{x}" id="inLon">', //optional default "Lng: {x}"
                enableUserInput:true, //optional default true
                useDMS:false, //optional default false
                useLatLngOrder: true //ordering of labels, default false-> lng-lat
            }).addTo(map);


            if(lat != null && lon != null){
                new_marker = new L.marker([lat,lon],{ draggable: true});
                new_marker.on('dragend',function(e){
                    var marker = e.target;  // you could also simply access the marker through the closure
                    var result = marker.getLatLng();
                    console.log(result);

                    $('#latitude').val(result.lat);
                    $('#longitude').val(result.lng);
                }).addTo(map);
            }

            map.on('click',function(e){
                console.log(e.latlng.lat + ',' + e.latlng.lng);

                $('#latitude').val(e.latlng.lat);
                $('#longitude').val(e.latlng.lng);

                if(typeof(new_marker)==='undefined')
                {
                    new_marker = new L.marker(e.latlng,{ draggable: true});
                    new_marker.on('dragend',function(e){
                        var marker = e.target;  // you could also simply access the marker through the closure
                        var result = marker.getLatLng();
                        console.log(result);

                        $('#latitude').val(result.lat);
                        $('#longitude').val(result.lng);
                    }).addTo(map);
                }
                else
                {
                    new_marker.setLatLng(e.latlng);
                }

            });



        }

        function setMarker(lat, lon){
                var latlng = { lat:lat, lng:lon };
                if(typeof(new_marker)==='undefined')
                {
                    new_marker = new L.marker(latlng,{ draggable: true});
                    new_marker.on('dragend',function(e){
                        var marker = e.target;  // you could also simply access the marker through the closure
                        var result = marker.getLatLng();
                        console.log(result);

                        $('#latitude').val(result.lat);
                        $('#longitude').val(result.lng);
                    }).addTo(map);
                }
                else
                {
                    new_marker.setLatLng(e.latlng);
                }

                map.setView([lat,lon], 13)

        }

        function submitlocation(){

            $.post('<?php print site_url('ajax/setbuyerloc');?>',
                {   id : '<?php print $id;?>',
                    latitude: $('#latitude').val(),
                    longitude: $('#longitude').val(),
                    type: '<?php print $type;?>' },
                function(data) {
                    if(data.result == 'OK'){

                        parent.$('#setloc_dialog').dialog('close');
                        parent.refreshTab();

                    }else{
                        alert('Failed to set Location');
                    }
                    //alert(data.status);
                },'json');

        }


        $(document).ready(function(){

            <?php if($latitude != '' && $longitude != '') : ?>
                setupMap(<?php print $latitude;?>,<?php print $longitude;?>);
            <?php else : ?>
                setupMap();
            <?php endif ?>

            $('.use-loc').on('click',function(){
                var lat = $(this).data('lat');
                var lon = $(this).data('lon');
                $('#latitude').val(lat);
                $('#longitude').val(lon);

                setMarker(lat,lon);
            });

        });
    </script>
</head>
<body>
    <table style="width:100%;border:0;margin:0;">
        <tr>
            <td style="width:300px;vertical-align:top">
                <div id="map" style="width:300px;height:350px;display:block;border:thin solid grey;"></div>
            </td>
            <td style="width:300px;vertical-align:top">
                Buyer Info :
                <dl>
                    <?php foreach ($buyer as $key => $value):?>
                        <dt><?= mt($key)?></dt>
                        <dd><?=($value == '')?'-':$value?></dd>
                    <?php endforeach; ?>
                    <dt>Latitude</dt>
                    <dd><input id='latitude' name='latitude' value='<?=$latitude?>'></dd>
                    <dt>Longitude</dt>
                    <dd><input id='longitude' name='longitude' value='<?=$longitude?>'></dd>
                </dl>
            </td>
            <td style="vertical-align:top;overflow-y:auto;">
                Similar Locations :
                <ol>
                    <?php foreach ($suggestions as $val):?>
                        <li>
                            <?= $val['buyer_name']?><br />
                            <i><?= $val['shipping_address']?></i><br />
                            <b><?= $val['latitude'].','.$val['longitude'] ?></b>
                            <span class="use-loc" data-lat="<?=$val['latitude'] ?>" data-lon="<?=$val['longitude'] ?>" >use</span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </td>
        </tr>
    </table>
</body>

<?php

function mt($t){
    return ucwords(str_replace('_', ' ', $t));
}

?>
