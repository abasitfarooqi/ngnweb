var headerFixed = function(){
        if($('body').hasClass('header_sticky')){
            var nav = $('#header');

            if( nav.length ){
                var offsetTop = nav.offset().top,
                headerHeight = nav.height(),
                injectSpace = $('<div/>', {
                    height: headerHeight
                }).insertAfter(nav);

                $(window).on('load scroll', function(){
                    if($(window).scrollTop() > offsetTop){
                        nav.addClass('is-fixed');
                        injectSpace.show();
                    }else {
                        nav.removeClass('is-fixed');
                        injectSpace.hide();
                    }

                    if($(window).scrollTop() > 300 ) {
                        nav.addClass('is-small');
                    }else {
                        nav.removeClass('is-small');
                    }
                });
            }
        };
    };
