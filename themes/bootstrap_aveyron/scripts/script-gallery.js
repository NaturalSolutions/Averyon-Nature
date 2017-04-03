jQuery( document ).ready(function() {

    /*
    * toggle focus pictures
    */
    var sortTab = function(filter){

        if(filter == "faune") $(".filter-btns button:nth-of-type(2)").trigger('click');
        else if(filter == "flore") $(".filter-btns button:nth-of-type(3)").trigger('click');

    }

    window.init = function() {

        filterTaxonsThematic();

        var filter;
        filter = getUrlParameter('filtre');
        if(filter !== undefined) sortTab(filter);

    }

    var $ = jQuery;
    init(); // true

});