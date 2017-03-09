jQuery( document ).ready(function() {


    var startGallery = function(){
    //slideshow on en parle
        var galleryTopHp = new Swiper('.swiper-container', {
            spaceBetween : 0,
            slidesPerView: 1,
            effect : 'fade',
            autoplay : 5000,
            loopedSlides : 3,
            touchRatio : 0,
            simulateTouch :false,
            loop: true,

            onSlideChangeStart(galleryTopHp){
                jQuery('.progressBar').css('width','0');
                jQuery('.progressBar').animate({ width : '100%' }, 5000, function(){});
            }
        });
    }

    function calcPositionFromLatLonENS(currentPosUser) {

        var lat1 = currentPosUser.coords.latitude;
        var lon1 = currentPosUser.coords.longitude;

        // Pour chaque bloc on va calculer la distance et l'insérer et l'afficher
        jQuery('.latLon').each(function(index, el) {

            var coordEns = jQuery(this);
            coordEns = coordEns.html();

            var latEns = parseFloat(coordEns.split(';')[0]);
            var lonEns = parseFloat(coordEns.split(';')[1]);

            var distance = parseInt(getDistanceFromLatLonInKm(lat1,lon1,latEns,lonEns));

            jQuery(this).html(distance).parent().removeClass('hidden');

        });


        function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
            var R = 6371; // Radius of the earth in km
            var dLat = deg2rad(lat2-lat1);  // deg2rad below
            var dLon = deg2rad(lon2-lon1);
            var a =
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon/2) * Math.sin(dLon/2)
            ;
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180)
        }

    }

    function getPosUser(){

        var apiGeolocationSuccess = function(position) {
            console.log("API geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
        };

        var tryAPIGeolocation = function() {
            jQuery.post( "https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyDCa1LUe1vOczX1hO_iGYgyo8p_jYuGOPU", function(success) {
                calcPositionFromLatLonENS({coords: {latitude: success.location.lat, longitude: success.location.lng}});
            })
            .fail(function(err) {
                console.log("API Geolocation error! \n\n"+err);
            });
        };

        var browserGeolocationSuccess = function(position) {
            console.log("Browser geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
            calcPositionFromLatLonENS({coords: {latitude: position.coords.latitude, longitude: position.coords.longitude}});
        };

        var browserGeolocationFail = function(error) {
            switch (error.code) {
                case error.TIMEOUT:
                    console.log("Browser geolocation error !\n\nTimeout.");
                break;
                case error.PERMISSION_DENIED:
                    if(error.message.indexOf("Only secure origins are allowed") == 0) {
                        tryAPIGeolocation();
                    }
                break;
                case error.POSITION_UNAVAILABLE:
                    console.log("Browser geolocation error !\n\nPosition unavailable.");
                break;
            }
        };

        var tryGeolocation = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    browserGeolocationSuccess,
                    browserGeolocationFail,
                    {maximumAge: 50000, timeout: 20000, enableHighAccuracy: true}
                );
            }
        };

        tryGeolocation();

    }

    var displayLocationFold2 = function(){

        getPosUser();

    }



    var toggleShowHideEnsFold3 = function(){

        // Show an only random ENS when the page load
        var nbEns = parseInt(jQuery('div.fold3 div.descroZone > div').length) - 1;
        var ensShowed = Math.floor((Math.random() * nbEns) + 0);

        jQuery("div.fold3 div.descroZone div.index" + ensShowed).removeClass('hidden');

        // Toggle show/hide when hover ENS on Map Fold3
        var ensHovered = 'nidEns_'+ensShowed;
        jQuery("div.fold3 div.map div.pointEns").hover(function() {

            // if the focus ens changed
            if( ensHovered != jQuery(this).attr('class').split(' ')[2].split('_')[1] ){

                // Get id ens hovered
                ensHovered = jQuery(this).attr('class').split(' ')[2].split('_')[1];

                // Hide the current Ens showed
                jQuery("div.fold3 div.descroZone div.index" + ensShowed).addClass('hidden');

                // Show the new Ens Hovered
                jQuery("div.fold3 div.descroZone div.index" + ensHovered).removeClass('hidden');

                ensShowed = ensHovered;

            }

        });

    }

    var toggleMobileDisplay = function(isMobile, fromResize){

        if(isMobile){

            jQuery("div.fold3 div.map").addClass('hidden');
            jQuery("div.fold3 h2").text("Découvrir");

            /*
                Random show/hide ens on fold3
            */
            // Count nb ENS
            var nbEns = parseInt(jQuery('div.fold3 div.descroZone > div').length) - 1;

            if(!fromResize){

                setInterval(function(){

                    // Get id ens showed
                    var ensShowed = jQuery("div.fold3 div.descroZone > div").not(".hidden").attr('class');
                    var ensToShow = 'index'+Math.floor((Math.random() * nbEns) + 0);

                    if( ensToShow != ensShowed ){

                        // Hide the current Ens showed
                        jQuery("div.fold3 div.descroZone div." + ensShowed).addClass('hidden');
                        jQuery("div.fold3 div.descroZone div." + ensToShow).removeClass('hidden');

                    }

                }, 5000);
            }
        }
        else{

            jQuery("div.fold3 div.map").removeClass('hidden');

        }
    }

    var startAutoFocusThematikFold4 = function(){

        var cpt = 1;

        setInterval(function(){
            if(cpt > 3) cpt = 0;

                jQuery('div.fold4 div.textZone div.blockInTextZone').each(function(index, el) {
                    if(index == cpt){
                        jQuery('div.fold4 div.textZone div.blockInTextZone.isHovered').removeClass('isHovered');
                        jQuery(this).addClass('isHovered');
                    }
                });

            cpt++;
        }, 3000);

    }

    var checkWidthDevice = function(){

        //detect the width on page load
        var current_width = jQuery(window).width();
        var isMobile;

        //do something with the width value here!
        if(current_width <= 750){

            console.log('probably-mobile on load');
            toggleMobileDisplay(isMobile = true, fromResize = false);
            startAutoFocusThematikFold4();

        }else{
            console.log('not probably-mobile on load');
            toggleMobileDisplay(isMobile = false, fromResize = false);
        }

        //update the width value when the browser is resized (useful for devices which switch from portrait to landscape)
        jQuery(window).resize(function(){
            var current_width = jQuery(window).width();
            //do something with the width value here!
            if(current_width <= 750){
                toggleMobileDisplay(isMobile = true, fromResize = true);
                console.log('probably-mobile');
            }else{
                toggleMobileDisplay(isMobile = false, fromResize = true);
                console.log('not probably-mobile');
            }
        });
    }

    var toggleShowHideFold4 = function(){

        jQuery('div.fold4 div.blockInTextZone').hover(function() {

            // if hover an hidding block
            if( !jQuery(this).hasClass('isHovered') ){

                // Remove the status hoverd of old block
                jQuery('div.fold4 div.blockInTextZone.isHovered').removeClass('isHovered');

                // Specify hovered status for the hovered block
                jQuery(this).addClass('isHovered');

                // Get name of the thematique hovered
                var nameThematik = jQuery(this).attr('class').split(' ')[2].split('row')[1];

                // Change image on left zone with the correct thematik hovered
                jQuery('div.fold4 div.imageZone img').not('.hidden').addClass('hidden');
                jQuery('div.fold4 div.imageZone img.img'+nameThematik).removeClass('hidden');
            }

        }, function() {


        });

    }

    window.init = function() {

        startGallery();
        displayLocationFold2();
        toggleShowHideEnsFold3();
        checkWidthDevice();
        toggleShowHideFold4();

    }

    init(); // true

});