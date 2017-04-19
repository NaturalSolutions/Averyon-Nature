jQuery( document ).ready(function() {

  var checkHeader = function(){

      jQuery(window).scroll(function (event) {

          var scroll = jQuery(window).scrollTop();
          var btnMenu = jQuery('div.navbar-header button');

          if(parseInt(scroll) > 0 ) btnMenu.addClass('reduce');
          else if(parseInt(scroll) == 0) btnMenu.removeClass('reduce');

      });

  };

  var toggleNavbar = function(){

  	jQuery(document).click(function(event) {

      var clickedObject = jQuery(event.target);

      //Si on clic en dehors du menu collaspible
      if(clickedObject.context.className != 'region region-navigation-collapsible') jQuery(".navbar-collapse.collapse.in").collapse('hide');

      //S'il existe des class sur l'élément cliqué
      if(clickedObject.attr('class') != undefined) {

        //Si l'élement cliqué possède la classe close
        if( clickedObject.hasClass('close') ) jQuery(".navbar-collapse.collapse.in").collapse('hide');

      }


  	});


  };

  window.init = function() {
    toggleNavbar();
    checkHeader();
  }

  init(); // true

  /*
  * Common uselfull global fonction
  */
  var $ = jQuery;
  /*
  * Filter Taxons by thematic
  */
  filterTaxonsThematic = function(){

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
  * Get url attribute
  */
  getUrlParameter = function getUrlParameter(sParam) {
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




});