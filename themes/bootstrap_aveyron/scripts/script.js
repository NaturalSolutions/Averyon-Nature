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
});