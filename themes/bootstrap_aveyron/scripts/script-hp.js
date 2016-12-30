jQuery( document ).ready(function() {  

  window.init = function() {
    
    //slideshow on en parle
	var swiper = new Swiper('.swiper-container', {       
        slidesPerView: 1,
        spaceBetween : 0,        
        autoplay : 2500,
        loop: true
    });

  }
  
  init(); // true 
});