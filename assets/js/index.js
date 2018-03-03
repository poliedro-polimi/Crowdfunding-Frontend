(function () {
    // Click on animated arrow scrolls to next section
    $("section#header .up-down-arrow").click(function () {
        window.scrollTo({
            top: $("section.first-after-header").offset().top - $("nav#mainNav").height(),
            behavior: "smooth"
        });
    })
})();