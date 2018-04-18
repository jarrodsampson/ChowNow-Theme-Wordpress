var customScripts = {
  smoothScroll: function () {

    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html, body').animate({
            scrollTop: (target.offset().top - 48)
          }, 1000, "easeInOutExpo");
          return false;
        }
      }
    });

  },
  srcollSpy: function () {

    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
      target: '#mainNav',
      offset: 54
    });

  },
  opacticNav: function () {

    $(window).scroll(function() {
        
        if ($(window).scrollTop() > 500 ){
          
          $('.navbar').addClass('show');
          
        } else {
          
          $('.navbar').removeClass('show');
          
        };    
      });


  },
  init: function () {
    customScripts.opacticNav();
    customScripts.smoothScroll();
    customScripts.srcollSpy();

  }
};
$('document').ready(function () {
  customScripts.init();
});