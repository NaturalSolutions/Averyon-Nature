jQuery( document ).ready(function() {

  var toggleNavbar = function(){

  	jQuery('div.main-container').click(function(event) {
  		/* Act on the event */
  		jQuery(".navbar-collapse.collapse.in").collapse('hide');
  	});

  	jQuery('button.close').click(function(event) {
  		/* Act on the event */
  		jQuery(".navbar-collapse.collapse.in").collapse('hide');

  	});
    
    
    //jQuery('button.close').collapse('toggle');
  };

  window.init = function() {
    toggleNavbar();
  }
  
  init(); // true 
});