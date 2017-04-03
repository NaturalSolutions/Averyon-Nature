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
        * Filter Taxons by thematic
        */
        var filterTaxonsThematic = function(){

            $('.js-btn-filter').on('click', function(e){
                $('.js-btn-filter').each(function(){
                    $(this).removeClass('active');
                });

                $(e.currentTarget).addClass('active');

                var thematique = $(e.currentTarget).attr('thematique');
                $('.js-figures').find('.js-figure').each(function(){
                    if(thematique == 'all'){
                        $(this).removeClass('hide');
                        return;
                    }
                    if($(this).attr('thematique') === thematique){
                        $(this).removeClass('hide');
                    } else {
                        $(this).addClass('hide');
                    }
                });
            });

        }



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
        var map = function(){

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

            var quizzMap;
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

        }

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

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