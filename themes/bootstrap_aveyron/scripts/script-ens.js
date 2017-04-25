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
        //I could use a framework
        var quizz = function(){

            $('.js-quizz-prop').on('click', function(e){
                $(e.currentTarget).parent().parent().find('.js-quizz-prop').each(function(){
                    $(this).removeClass('active');
                    $(this).removeClass('btn-success');
                    $(this).removeClass('btn-danger');
                })
                $(e.currentTarget).addClass('active');

            });

            var nbErrors = 0;
            var nbOk = 0;
            var nbQuestions = 0;

            $('.js-validate').on('click', function(){
                var empty = true;

                $('.js-article-quizz').each(function(){
                    $(this).find('.js-quizz-prop').each(function(){
                        if($(this).hasClass('active')){
                            empty = false;
                        }
                    });
                });
                if(empty){
                    return;
                }

                $('.js-article-quizz').each(function(){
                    nbQuestions++;
                    var answer = $(this).attr('answer');
                    $(this).find('.js-quizz-prop').each(function(){
                        if($(this).attr('code') == answer){
                            $(this).addClass('btn-success');
                            if($(this).hasClass('active')){
                                nbOk++;
                            }
                        } else {
                            if($(this).hasClass('active')){
                                nbErrors++;
                                $(this).addClass('btn-danger');
                            }
                        }
                        $(this).removeClass('active');
                    });
                });


                $('#popup').fadeIn('fast');
                var title = $('.js-title').html('');
                var subTitle = $('.js-sub-title').html('');
                if (nbErrors > (nbQuestions/3)){
                    title.html('Vous pouvez encore vous améliorer!');
                } else  {
                    if(!nbErrors){
                        title.html('Bravo!');
                        subTitle.html('Toutes les réponses sont exactes!');
                    } else {
                        title.html('Félicitation!');
                        subTitle.html('Vous avez passé le quizz!');
                    }
                }

            });

            var close = function(){
                nbErrors = 0;
                nbOk = 0;
                nbQuestions = 0;
                $('#popup').fadeOut();
            };

            $('js-inner-popup').on('click', function(e){
                console.log(e);
                if($(e.target).hasClass('js-inner-popup')){
                    close();
                }
            }); 

            $('.js-btn-close').on('click', function(){
                close();
            }); 

            setTimeout(function(){
                close();
            }, 1000);

        }



        /*
        * Map
        */
        var map = function(){

            /*les deux variables de données :
            var geoDataStarterPoint
            var geoDataTracePoint*/

            /*console.log('geoDataTracePoint', geoDataTracePoint);*/


            var map = L.map("map").setView([geoDataTracePoint.coordinates[0][1],geoDataTracePoint.coordinates[0][0]], 14) ;
            L.tileLayer(
            'http://wxs.ign.fr/uxfc79ihyesfzukqvfqcev40/geoportail/wmts?service=WMTS&request=GetTile&version=1.0.0&tilematrixset=PM&tilematrix={z}&tilecol={x}&tilerow={y}&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal',
            {
                minZoom:0,
                maxZoom:18,
                tileSize:256
            }).addTo(map);

            var polyline = L.polyline(
                geoDataTracePoint.coordinates,
                {
                    weight: 5,
                    opacity: 0.7,
                }
            ).addTo(map);

            /* Function of Céline & Vincent
            onMapModelReady() {
                let center = L.latLng(this.mapModel.center.lat, this.mapModel.center.lng)
                this._map = L
                  .map(this.mapEl, {
                    minZoom: this.mapModel.cacheZoom - 1,
                    maxZoom: this.mapModel.cacheZoom,
                  })
                  .setView(center, this.mapModel.cacheZoom)

                this.mapModel.tileLayer.addTo(this._map);

                console.log(this._tourStart);

                L.marker(center, { icon: this._icon })
                  .bindPopup('Départ')
                  .openPopup()
                  .addTo(this._map);

                console.log(this._trace);
                this._trace = L.geoJSON(this._trace, {
                  style: function (feature) {
                    return {
                      "color": "#4928d9",
                      "weight": 8,
                      "opacity": 0.8 };
                  }//,
                  // onEachFeature: function (feature, layer) {
                  //     layer.bindPopup(this.tours[0].title);
                  // }
                }).addTo(this._map)
                //this._map.fitBounds(this._trace.getBounds());

                console.log(this.platform.platforms())
                if (this.platform.is('cordova')) {
                  this.watchConnection();
                }
              }
            */



            /* OpenLayer init map
            var map = L.map('map').setView(geoDataTracePoint.coordinates[0], 14);
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var polyline = L.polyline(
                geoDataTracePoint.coordinates,
                {
                    weight: 5,
                    opacity: 0.7,
                }
            ).addTo(map);
            */




            var quizzMap;
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if($(e.currentTarget).attr('passed') === undefined && e.currentTarget.hash  === '#quizz'){
                  quizzMap = L.map('quizzMap').setView([geoDataTracePoint.coordinates[0][1],geoDataTracePoint.coordinates[0][0]], 14);
                  //L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                  L.tileLayer('http://wxs.ign.fr/uxfc79ihyesfzukqvfqcev40/geoportail/wmts?service=WMTS&request=GetTile&version=1.0.0&tilematrixset=PM&tilematrix={z}&tilecol={x}&tilerow={y}&layer=GEOGRAPHICALGRIDSYSTEMS.MAPS&format=image/jpeg&style=normal', {
                      //attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                      attribution: ''
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


        }


        // sort tab by url
        var sortTab = function(filter){

            if(filter == "especes") $("ul.nav.nav-tabs li:nth-of-type(2) > a").trigger('click');
            else if(filter == "quizz") $("ul.nav.nav-tabs li:nth-of-type(3) > a").trigger('click');

        }

        window.init = function() {

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