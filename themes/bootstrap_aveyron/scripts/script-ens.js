jQuery( document ).ready(function() {


    var initMap = function(){

        // geoData to display map
        console.log("geoDataTracePoint", geoDataTracePoint);

        // IGN layer - forbiden access with this IGN key
        /*
        var map = L.map("mapid").setView([48.845,2.424],10) ;
        L.tileLayer(
        'http://wxs.ign.fr/keivyuqi9olxo91z3g5xvh5z/geoportail/wmts?service=WMTS&request=GetTile&version=1.0.0&tilematrixset=PM&tilematrix={z}&tilecol={x}&tilerow={y}&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal',
        {
                minZoom:0,
                maxZoom:18,
                tileSize:256
        }).addTo(map);
        */

        console.log("geoDataStarterPoint", geoDataStarterPoint);
        // OpenLayer layer example
        var map = L.map('map').setView(geoDataTracePoint.coordinates[0], 14);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);



        var polyline = L.polyline(
            geoDataTracePoint.coordinates,            
            {
                weight: 5,
                opacity: .7,
            }
            ).addTo(map);

    }



    window.init = function() {

        initMap();

    }

    init(); // true

});