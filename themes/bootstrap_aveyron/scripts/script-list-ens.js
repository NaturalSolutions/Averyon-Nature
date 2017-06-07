// JS file for list-ens page
jQuery( document ).ready(function() {

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

	var sortENS = function(filter){

		var $ = jQuery;

		//quick & unoptimized
		$('.js-btn-filter-ens').on('click', function(e){
			$('.js-btn-filter-ens').each(function(){
					$(this).removeClass('active');
			});

			var thematique;
			if(filter) thematique = filter;
			else thematique = $(e.currentTarget).attr('thematique');

			$(e.currentTarget).addClass('active');
			$('.js-ens-container').find('.js-ens').each(function(){
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

		if(filter) $('.js-btn-filter-ens[thematique="'+filter+'"]').trigger('click');
		filter = undefined;

	}


	function calcPositionFromLatLonENS(currentPosUser) {

			console.log(currentPosUser);

	    var lat1 = currentPosUser.coords.latitude;
	    var lon1 = currentPosUser.coords.longitude;

			var $ = jQuery;
	    // Pour chaque bloc on va calculer la distance et l'insérer et l'afficher
	    jQuery('.js-ens .js-distance').each(function(index, el) {


	        var coordEns = $(this).attr('coords');

	        var latEns = parseFloat(coordEns.split(';')[0]);
	        var lonEns = parseFloat(coordEns.split(';')[1]);

	        var distance = parseInt(getDistanceFromLatLonInKm(lat1,lon1,latEns,lonEns));

	        jQuery(this).html(distance);
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
	    console.log('plouf');
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





	window.init = function() {
		getPosUser();
		var filter;
		filter = getUrlParameter('filtre');

		sortENS(filter);

	}

	init(); // true
});

