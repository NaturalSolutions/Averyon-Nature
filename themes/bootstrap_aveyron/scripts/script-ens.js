jQuery( document ).ready(function() {

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

        /*
        * Quiz
        */
        var quizz = function(){

            $('.js-quizz-prop').on('click', function(e){
                $(e.currentTarget).parent().parent().find('.js-quizz-prop').each(function(){
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).removeClass('btn-danger');
                })
                $(e.currentTarget).addClass('active');

            });

            var nbErros;
            var nbOk;

            $('.js-validate').on('click', function(){
                $('.js-article-quizz').each(function(){
                    var answer = $(this).attr('answer');
                    $(this).find('.js-quizz-prop').each(function(){
                        if($(this).attr('code') == answer){
                            $(this).addClass('btn-success');
                            if($(this).hasClass('active')){
                                nbOk++;
                            }
                        } else {
                            if($(this).hasClass('active')){
                                nbErros++;
                                $(this).addClass('btn-danger');
                            }
                        }
                        $(this).removeClass('active');
                    });
                });
            });

        }



        /*
        * Map
        */

        var southWest = L.latLng(43.65, 1.8);
        var northEast = L.latLng(44.9, 3.5);
        var bounds = L.latLngBounds(southWest, northEast);

        var _icon = L.icon({
          iconUrl:'../themes/bootstrap_aveyron/images/leaflet/debut_ens.png',
          iconRetinaUrl:'../themes/bootstrap_aveyron/images/leaflet/debut_ens@2x.png',
          iconAnchor: [11, 22],
          shadowUrl: '../themes/bootstrap_aveyron/images/leaflet/marker-shadow.png',
        })

        var map = function(){

            /*les deux variables de données :
            var geoDataStarterPoint
            var geoDataTracePoint*/

            /*console.log('geoDataTracePoint', geoDataTracePoint);*/

            var center = L.latLng(geoDataStarterPoint.field_start_trace_lat, geoDataStarterPoint.field_start_trace_lon)
            var map = L.map("map", { maxBounds: bounds }).setView(center, 14) ;
            L.tileLayer(
            'http://wxs.ign.fr/uxfc79ihyesfzukqvfqcev40/geoportail/wmts?service=WMTS&request=GetTile&version=1.0.0&tilematrixset=PM&tilematrix={z}&tilecol={x}&tilerow={y}&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal',
            {
                bounds: bounds,
                minZoom:9,
                maxZoom:18,
                tileSize:256,
                attribution: 'Carte IGN'
            }).addTo(map);

            var startMarker = L.marker(center, { icon: _icon })
              .bindPopup('Départ')
              .openPopup();

            var polyline = L.geoJSON(geoDataTracePoint, {
              style: function (feature) {
                return {
                  "color": "#4928d9",
                  "weight": 5,
                  "opacity": 0.8 };
                }
            }
            ).addTo(map);

            map.on('zoomend', function () {
                var currentZoom = map.getZoom();
                if (currentZoom < 12) {
                    polyline.remove(map);
                    startMarker.addTo(map);
                }
                else {
                    polyline.addTo(map);
                    startMarker.remove(map);
                }
            });

            var quizzMap;
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if($(e.currentTarget).attr('passed') === undefined && e.currentTarget.hash  === '#quizz'){
                  quizzMap = L.map('quizzMap', { maxBounds: bounds }).setView([geoDataTracePoint.coordinates[0][1],geoDataTracePoint.coordinates[0][0]], 14);
                  //L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                  L.tileLayer('http://wxs.ign.fr/uxfc79ihyesfzukqvfqcev40/geoportail/wmts?service=WMTS&request=GetTile&version=1.0.0&tilematrixset=PM&tilematrix={z}&tilecol={x}&tilerow={y}&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal', {
                      //attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                      bounds: bounds,
                      minZoom:9,
                      maxZoom:18,
                      attribution: 'Carte IGN'
                  }).addTo(quizzMap);

                  polyline.addTo(quizzMap);

                  quizzMap.on('zoomend', function () {
                      var currentZoom = quizzMap.getZoom();
                      if (currentZoom < 12) {
                          polyline.remove(quizzMap);
                          startMarker.addTo(quizzMap);
                      }
                      else {
                          polyline.addTo(quizzMap);
                          startMarker.remove(quizzMap);
                      }
                  });
                }

                $(e.currentTarget).attr('passed', 'true');
            });


        }


        // sort tab by url
        var sortTab = function(filter){

            if(filter == "especes") $("ul.nav.nav-tabs li:nth-of-type(2) > a").trigger('click');
            else if(filter == "quizz") $("ul.nav.nav-tabs li:nth-of-type(3) > a").trigger('click');

        }

        window.init = function() {

            // not used ?
            var Shuffle = window.shuffle;

            map();
            quizz();
            filterTaxonsThematic();

            var filter;
            filter = getUrlParameter('filtre');
            if(filter !== undefined) sortTab(filter);

        }

        var $ = jQuery;
        init(); // true

});
