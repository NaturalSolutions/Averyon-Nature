jQuery( document ).ready(function() {

    /*
    * toggle focus pictures
    */
    var sortTab = function(filter){

        if(filter == "faune") $(".filter-btns button:nth-of-type(2)").trigger('click');
        else if(filter == "flore") $(".filter-btns button:nth-of-type(3)").trigger('click');
        else if(filter == "paysage") $(".filter-btns button:nth-of-type(4)").trigger('click');
        else if(filter == "patrimoine") $(".filter-btns button:nth-of-type(5)").trigger('click');
        else if(filter == "video") $(".filter-btns button:nth-of-type(6)").trigger('click');

    }

    var onClickPicture = function(){


        //to display two galerie

        $('.js-figure:not(.hide, .video-figure)').vanillabox({

            closeButton: false,
            loop: true,
            repositionOnScroll: true,
            type: 'image',
            adjustToWindow: 'both'

        });

        $('.js-figure.video-figure:not(.hide)').vanillabox({

            closeButton: false,
            loop: true,
            repositionOnScroll: true,
            type: 'iframe',
            adjustToWindow: 'both'

        });



        /*$('.js-figure:not(.hide)').vanillabox({

            closeButton: false,
            loop: true,
            repositionOnScroll: true,
            type: 'image',
            type: 'iframe',
            adjustToWindow: 'both'

        });*/



        /*$(".js-figure").click(function(event) {

            BootstrapDialog.show({

                message: function(dialog) {
                    var $message = $('<div class="swiper-container">'+
                                        '<div class="swiper-wrapper">'+
                                            '<div class="swiper-slide">Slide 1</div>'+
                                            '<div class="swiper-slide">Slide 2</div>'+
                                            '<div class="swiper-slide">Slide 3</div>'+
                                        '</div>'+
                                        '<div class="swiper-button-prev"></div>'+
                                        '<div class="swiper-button-next"></div>'+
                                    '</div>');

                    var pageToLoad = dialog.getData('pageToLoad');
                    $message.load(pageToLoad);

                    return $message;

                }

                //
                //callback: function(result) {
                //    // result will be true if button was click, while it will be false if users close the dialog directly.
                //    alert('Result is: ');
                 //   var mySwiper = new Swiper('.swiper-container', {
                  //      speed: 400,
                   //     spaceBetween: 100
                   // });
               // }


            });

        });*/

    }

    var filterTaxonsThematicGal = function(){

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

            // Need to remove old event
            $('.js-figure').off();

            console.log('thematique', thematique);

            if(thematique == "video"){
                $('.js-figure.video-figure:not(.hide)').vanillabox({

                    closeButton: false,
                    loop: true,
                    repositionOnScroll: true,
                    type: 'iframe',
                    adjustToWindow: 'both'

                });
            }else{
                $('.js-figure:not(.hide, .video-figure)').vanillabox({

                    closeButton: false,
                    loop: true,
                    repositionOnScroll: true,
                    type: 'image',
                    adjustToWindow: 'both'

                });
            }

            // Reload vanillaBox with correct filter setted
            /*$('.js-figure:not(.hide, .video-figure)').vanillabox({

                closeButton: false,
                loop: true,
                repositionOnScroll: true,
                type: 'image',
                adjustToWindow: 'both'

            });

            $('.js-figure.video-figure:not(.hide)').vanillabox({

                closeButton: false,
                loop: true,
                repositionOnScroll: true,
                type: 'iframe',
                adjustToWindow: 'both'

            });*/


        });

    }

    var displayImgVideos = function(){

        // Get thulbnail - https://api.dailymotion.com/videos?ids=x5f5olp,x2c5umz&limit=30&fields=id,thumbnail_url,title,tiny_url
        var urlApiDayli = "https://api.dailymotion.com/videos?ids=";
        var allVideos = "";

        // First contruct URl to attack to get all info I need to build DOM VIDEO
        videos.forEach(function(video) {

           allVideos = allVideos.concat(video.field_video_ens_value+",");

        });

        // Clean URl
        allVideos = allVideos.substring("", allVideos.length -1 );

        // Final build URL
        urlApiDayli = urlApiDayli.concat(allVideos+"&limit=100&fields=thumbnail_url,title,id");

        // Attack API daylimotion to get data informations
        $.getJSON(urlApiDayli, function(videosApi) {

            // for each videos id get with api with put the correct background-image
            videosApi.list.forEach(function(videoApi) {

                // Add background image
                $("."+videoApi.id).css("backgroundImage","url(themes/bootstrap_aveyron/images/icon_play_video.png), url("+videoApi.thumbnail_url+")");

                // Add figcaption
                $("."+videoApi.id).next().find('figcaption, .credit').text(videoApi.title);

            });

        });

    }

    window.init = function() {

        filterTaxonsThematicGal();

        var filter;
        filter = getUrlParameter('filtre');
        if(filter !== undefined) sortTab(filter);

        displayImgVideos();
        onClickPicture();

    }

    var $ = jQuery;
    init(); // true

});