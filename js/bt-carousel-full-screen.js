/**
 * @file
 * Display a carousel in full screnn mode.
 */
(function ($, Drupal) {
  Drupal.behaviors.btCarousel = {
    attach: function (context, settings) {
      
      var carousel_id = drupalSettings.bt_carousel.carousel_id;
      var interval = drupalSettings.bt_carousel.interval;
      
      var $item = $('#'+ carousel_id + ' .carousel-item'); 
      var $wHeight = $(window).height();
      $item.eq(0).addClass('active');
      $item.height($wHeight); 
      $item.addClass('full-screen');
      
      $('#'+ carousel_id + '.carousel img').each(function() {
        var $src = $(this).attr('src');
        var $color = $(this).attr('data-color');
        $(this).parent().css({
          'background-image' : 'url(' + $src + ')',
          'background-color' : $color
        });
        $(this).remove();
      });

      $(window).on('resize', function (){
        $wHeight = $(window).height();
        $item.height($wHeight);
      });

      $('#'+ carousel_id + '.carousel').carousel({
        interval: interval,
        pause: "false"
      });
      
    }
  };
})(jQuery, Drupal);
