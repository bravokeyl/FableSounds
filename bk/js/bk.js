(function($){
  var sip = $(".bk-form .serial-input");
  var sipf = $(".bk-form .serial-input#bk_serial_key1");
  var halip = $(".halion-form .serial-input");
  sip.not("#bk_serial_key1").keypress(function (e) {
    if ( e.keyCode == 45 ) {
         e.preventDefault();
        //  $(this).addClass('red-input');
         return false;
    }

    if (this.value.length == this.maxLength) {
      var $next = $(this).next('.serial-input');
      if ($next.length)
          $(this).next('.serial-input').focus();
      else
          $(this).blur();
    }
  });
  sipf.keypress(function (e) {
    if ( e.keyCode == 45 ) {
         e.preventDefault();
         if( 4 == this.value.length ){
           var $next = $(this).next('.serial-input');
           if ($next.length)
               $(this).next('.serial-input').focus();
           else
               $(this).blur();
        }
        return false;
    }
    if( 4 == this.value.length ){
      var $next = $(this).next('.serial-input');
      if ($next.length)
          $(this).next('.serial-input').focus();
      else
          $(this).blur();
    }
  });
  sipf.keyup(function(e){
    if (this.value.length == this.maxLength || (24 == this.value.length) || (21 == this.value.length) || (20 == this.value.length)) {
      var ips = [];
      if((25 == this.value.length)){
        ips = this.value.split('-');
      }
      if((24 == this.value.length)){
        ips = this.value.split('-');
      }
      if((21 == this.value.length)){
        ips[0] = this.value.substring(0,5);
        var sps = this.value.substring(5).match(/.{1,4}/g);
        ips = ips.concat(sps);
      }
      if((20 == this.value.length)){
        ips = this.value.match(/.{1,4}/g); //split at every 4 characters
      }
      if( 5 == ips.length){
        $("#bk_serial_key1").val(ips[0]);
        $("#bk_serial_key2").val(ips[1]);
        $("#bk_serial_key3").val(ips[2]);
        $("#bk_serial_key4").val(ips[3]);
        $("#bk_serial_key5").val(ips[4]);
      }
    }
  });
  $("#clear-form").on("click",function(e){
    e.preventDefault();
    $(".bk-form .serial-input").val("").removeClass('red-input');
  });
  halip.keypress(function(e){
    if ( e.keyCode == 45 ) {
         e.preventDefault();
        return false;
    }
    if( this.value.length == 4 ){
      var $next = $(this).nextAll('.serial-input').eq(0);
      if ($next.length)
          $(this).nextAll('.serial-input').eq(0).focus();
      else
          $(this).blur();
      if( 39 == this.maxLength) {
        e.preventDefault();
        return false;
      }

    }
  });
  var kpfu = function(){
    if ( (32 == this.value.length) || (39 == this.value.length)) {
      var ips = [];
      if((39 == this.value.length)){
        ips = this.value.split(' ');
        if(8 != ips.length){
          ips = this.value.split('-');
        }
      }
      if((32 == this.value.length)){
        ips = this.value.match(/.{1,4}/g); //split at every 4 characters
      }
      var cl = $(this).parent().find(".serial-input");
      if( 8 == ips.length){
        for(var i=0;i<8;i++){
          cl.eq(i).val(ips[i]);
        }
      }
    }
  };
  $("#bk_old_halion_key11").keyup(kpfu);
  $("#bk_old_halion_key21").keyup(kpfu);
  $("#bk_old_halion_key31").keyup(kpfu);



  $(document).ready(function() {
    function bk_close_accordion() {
        $('.bk-accordion-panel .bk-accordion-product-title a').removeClass('active');
        $('.bk-accordion-panel .bk-accordion-product-title a i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        $('.bk-accordion-panel .bk-accordion-product-content').slideUp(300).removeClass('open');
    }
    $('.bk-accordion-product-title').last().addClass('last-panel')
    $('.bk-accordion-product-title a').click(function(e) {
        var currentAttrValue = $(this).attr('href');
        if($(e.target).is('.active')) {
          $(this).find("i").addClass('fa-chevron-up');
          bk_close_accordion();
        }else {
            bk_close_accordion();
            $(this).addClass('active');
            console.log($(this).find("i"),$(this).find("i").addClass('fa-chevron-up'));
            $(this).find("i").removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $('.bk-accordion-panel ' + currentAttrValue).slideDown(300).addClass('open');
        }
        e.preventDefault();
    });
  });

})(jQuery);
