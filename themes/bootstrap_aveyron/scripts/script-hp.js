jQuery( document ).ready(function() {  

  window.init = function() {
    
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
  
  init(); // true 
});