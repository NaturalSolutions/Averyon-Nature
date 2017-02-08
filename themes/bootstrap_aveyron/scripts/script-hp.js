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

    var checkHeader = function(){

        jQuery(window).scroll(function (event) {
            
            var scroll = jQuery(window).scrollTop();
            var btnMenu = jQuery('div.navbar-header button');

            if(parseInt(scroll) > 0 ) btnMenu.addClass('reduce');
            else if(parseInt(scroll) == 0) btnMenu.removeClass('reduce');

        });

    };


    window.init = function() {

        startGallery();
        displayLocationFold2();
        checkHeader();

    }

    init(); // true 

});