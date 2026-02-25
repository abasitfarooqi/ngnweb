  $(function() {
        var header = $(".homepagex");
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();

            if (scroll >= 100) {
                header.removeClass('homepagex').addClass("HeaderBG");
            } else {
                header.removeClass("HeaderBG").addClass('homepagex');
            }
        });
    });
