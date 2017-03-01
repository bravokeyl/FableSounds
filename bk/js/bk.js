(function($){
  var sip = $(".bk-form .serial-input");
  var sipf = $(".bk-form .serial-input#bk_serial_key1");
  // sip.not("#bk_serial_key1").keyup(function () {
  //   console.log("Key UP");
  //   if (this.value.length == this.maxLength) {
  //     var $next = $(this).next('.serial-input');
  //     if ($next.length)
  //         $(this).next('.serial-input').focus();
  //     else
  //         $(this).blur();
  //   }
  // });
  sipf.keyup(function () {
    console.log("First add");
    if (this.value.length == this.maxLength || (24 == this.value.length) || (21 == this.value.length) || (20 == this.value.length)) {
      console.log("Max Characters",this.value);
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
      // console.log(ips,sps);
      if( 5 == ips.length){
        $("#bk_serial_key1").val(ips[0]);
        $("#bk_serial_key2").val(ips[1]);
        $("#bk_serial_key3").val(ips[2]);
        $("#bk_serial_key4").val(ips[3]);
        $("#bk_serial_key5").val(ips[4]);
      }
      // var $next = $(this).next('.serial-input');
      // if ($next.length)
      //     $(this).next('.serial-input').focus();
      // else
      //     $(this).blur();
    }
  });
  $("#clear-form").on("click",function(e){
    e.preventDefault();
    $(".bk-form .serial-input").val("");
  })
})(jQuery);
