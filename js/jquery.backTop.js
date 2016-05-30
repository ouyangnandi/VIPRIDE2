(function($) {

    $.fn.backTop = function(options) {

        var backBtn = this;
        
        var settings = $.extend({
            'position' : 400,
            'speed' : 500,
            'color' : 'white'
        }, options);
        
        //Settings
        
        var position = settings['position'];
        var speed = settings['speed'];
        var color = settings['color'];

        backBtn.addClass(color);
        
        
        backBtn.css({
            'right' : 40,
            'bottom' : 40,
            'position' : 'fixed',
        });
        
        $(document).scroll(function(){
            var pos = $(window).scrollTop();
            if(pos >= position){
                backBtn.fadeIn(speed);
            } else{
                backBtn.fadeOut(speed);
            }
        });
        
        backBtn.click(function(){
            $("html, body").animate({ 
                scrollTop:0 
            }, 
            {
                duration: 500
            }); 
        });

    }

}(jQuery));