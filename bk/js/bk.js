(function($){
  $(".bk-form .serial-input").keyup(function () {
    if (this.value.length == this.maxLength) {
      var $next = $(this).next('.serial-input');
      if ($next.length)
          $(this).next('.serial-input').focus();
      else
          $(this).blur();
    }
  });
})(jQuery);
