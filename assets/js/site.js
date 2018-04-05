var countDownGoal = new Date('2018-06-23 15:00:00');

$(function () {
    $(document).on('click', 'a.extern', function(ev){
        ev.preventDefault();
        window.open($(this).attr('href'));
    });

    $(window).on('scroll load', navBarState);

    $("button.navbar-toggle").click(function () {
        setTimeout(function () {
            var navbar = $("nav#mainNav");

            if (!$(this).hasClass("collapsed")) {
                navbar.addClass("menu-open");
                if (!navbar.hasClass("affix"))
                    navbar.addClass("affix");
            } else {
                navbar.removeClass("menu-open");
            }
        }, 100);
    });

    //CountDown Code
    updateCountDown();
    setInterval(updateCountDown, 1000);
});

function navBarState(){
    var obj = $("section#header");
    var navbar = $("nav#mainNav");
    var togglebtn = $("button.navbar-toggle");
    var top = $(window).scrollTop();
    var max = obj.height() * .90;

    if (togglebtn.is(":visible") && !togglebtn.hasClass("collapsed"))
        return;

    if (top >= max && !navbar.hasClass("affix"))
        navbar.addClass("affix");
    else if (top < max && navbar.hasClass("affix"))
        navbar.removeClass("affix");
}

function updateCountDown(){
    var $countd = $('#countdown');

    var timeToGo = Math.floor((countDownGoal - (new Date()))/1000);
    var days, hours, minutes, seconds;

    days = Math.floor(timeToGo / 86400);
    timeToGo = timeToGo % 86400;
    hours = Math.floor(timeToGo / 3600);
    timeToGo = timeToGo % 3600;
    minutes = Math.floor(timeToGo / 60);
    seconds = timeToGo % 60;

    $countd.find('.days').text(days>=10?days:"0"+days.toString());
    $countd.find('.hours').text(hours>=10?hours:"0"+hours.toString());
    $countd.find('.minutes').text(minutes>=10?minutes:"0"+minutes.toString());
    $countd.find('.seconds').text(seconds>=10?seconds:"0"+seconds.toString());
}
