var countDownGoal = new Date('2019-06-24 00:00:00');

$(function () {
    $(document).on('click', 'a.extern', function(ev){
        ev.preventDefault();
        window.open($(this).attr('href'));
    });

    $(window).on('scroll load', setNavbarTransparency);

    $("button.navbar-toggler").click(function () {
        var togglebtn = $(this);
        var navbar = $("nav#mainNav");
        setTimeout(function () {
            if (!togglebtn.hasClass("collapsed")) {
                $(".navbar-toggler .navbar-red-bullet").fadeOut();
                navbar.addClass("menu-open");
                if (navbar.hasClass("transparent"))
                    navbar.removeClass("transparent");
            } else {
                navbar.addClass("menu-open");
                setNavbarTransparency();
            }
        }, 100);
    });

    //CountDown Code
    updateCountDown();
    setInterval(updateCountDown, 1000);
});

function setNavbarTransparency() {
    var obj = $("section#header");
    var navbar = $("nav#mainNav");
    var top = $(window).scrollTop();
    var max = obj.height() * .05;

    if (top >= max && navbar.hasClass("transparent"))
        navbar.removeClass("transparent");
    else if (top < max && !navbar.hasClass("transparent"))
        navbar.addClass("transparent");
}

function updateCountDown(){
    var $countd = $('#countdown');

    var timeToGo = Math.floor((countDownGoal - (new Date()))/1000);
    var days = 0, hours = 0, minutes = 0, seconds = 0;

    if(timeToGo>0) {
        days = Math.floor(timeToGo / 86400);
        timeToGo = timeToGo % 86400;
        hours = Math.floor(timeToGo / 3600);
        timeToGo = timeToGo % 3600;
        minutes = Math.floor(timeToGo / 60);
        seconds = timeToGo % 60;
    }

    $countd.find('.days').text(days>=10?days:"0"+days.toString());
    $countd.find('.hours').text(hours>=10?hours:"0"+hours.toString());
    $countd.find('.minutes').text(minutes>=10?minutes:"0"+minutes.toString());
    $countd.find('.seconds').text(seconds>=10?seconds:"0"+seconds.toString());
}
