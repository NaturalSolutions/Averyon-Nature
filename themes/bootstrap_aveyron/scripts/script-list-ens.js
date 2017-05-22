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

	window.init = function() {

		var filter;
		filter = getUrlParameter('filtre');

		sortENS(filter);

	}

	init(); // true
});



/*document.addEventListener('DOMContentLoaded', function(event) {*/

/*		var displayENS = function(thematique){

			// get number of all ENS
			var nbENS = allENS.length;

			// calc a number of ENS with different displayed in front
			var nbLongerENS = parseInt(nbENS / 5);
			// calc number of row we'll display with 3 ENS by row and the special display each 5 ENS
			var nbRowOfENS = parseInt((nbENS - nbLongerENS) / 3);


			allENS.forEach(function(ens, i) {
				var bg = ens;
				var size = 'col-sm-4';
				if(i === 0 || i === 9){
					console.log(ens);
					size = 'col-sm-8'
				}
				var ensTpl = '<div class="ens ' + size + '" >\
						<div class="bg" style="background: url(' + ens.uri + ')">\
						</div>\
						<a class="abs" href="plouf">\
							<div>\
								<h3>' + ens.title + '</h3>\
								<span class="distance"> KM</span>\
							</div>\
						</a>\
				</div>';


				$('.js-ens-container').append(ensTpl);

			});

		}

		var allENS = {{ data.ensFold2|json_encode()|raw }};
		displayENS("all");*/

	/*});*/