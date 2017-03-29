jQuery( document ).ready(function() {




        // geoData to display map

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

        var $ = jQuery; 
        var quizzMap;
        
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


        //dirty
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if($(e.currentTarget).attr('passed') === undefined && e.currentTarget.hash  === '#quizz'){
              quizzMap = L.map('quizzMap').setView(geoDataTracePoint.coordinates[0], 14);
              L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
              }).addTo(quizzMap);


              var polyline = L.polyline(
                  geoDataTracePoint.coordinates,            
                  {
                      weight: 5,
                      opacity: .7,
                  }
              ).addTo(quizzMap);
            }
            $(e.currentTarget).attr('passed', 'true');
        });






});