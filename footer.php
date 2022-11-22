<footer>
  <section>

    <div class="container">
      <div class="row">
        <div class="col-md-12">
            <p>© 2019-<?php echo date("Y"); ?> QICOIL.COM ALL RIGHTS RESERVED.</p>
            <p>Disclaimer: None of the products are intended as a diagnosis, treatment, cure, prevention of any disease and have not been evaluated by the FDA. You should never change or stop taking any medication unless you have discussed the situation with your medical practitioner.</p>
            <p><a href="https://members.qicoil.com/privacy-policy.php" target="_blank">Privacy Policy</a> | <a href="https://members.qicoil.com/terms-and-condition.php" target="_blank">Terms and Conditions</a> | <a href="https://members.qicoil.com/disclaimer.php" target="_blank">Disclaimer</a></p>
        </div>
    </div>
    </div>

  </section>
</footer>

<script>
$(".favorite").click(function() {
      var ele = $(this);
       var albumid = ele.attr('data-album');
         var favorite = ele.attr('data-favorite');
         if (favorite == 0) {
           var is_favorite = 1;
          ele.removeClass('no');
          ele.addClass('yes');
         } else {
          var is_favorite = 0;
          ele.removeClass('yes');
          ele.addClass('no');
         }

        $.ajax({
          url: 'post.php',
         type: 'POST',
          data: {
            favorite: 1,
            albumid: albumid,
            is_favorite: is_favorite
           },
           dataType: 'json',
          success: function(res) {
           if (res.success == true) {
               ele.attr('data-favorite', is_favorite);
               if (is_favorite == 1) {
                 // ele.removeClass('no');
                 // ele.addClass('yes');
               } else {
                 // ele.removeClass('yes');
                 // ele.addClass('no');
               }
             }
           }
         });
       });
       $(".btndrop").click(function() {
	var Key = 'accordion-filter-category';
	if ($(this).hasClass('collapsed')) {
		var Val = 'expand';
	}else{
		var Val = 'collapsed';
	}	
	setCookie(Key, Val);
});

var accordionfilter = getCookie("accordion-filter-category");
console.log(accordionfilter);
if(accordionfilter == 'collapsed'){
	$('#demobtn').removeClass('in');
  $('#demobtn').addClass('collapse');
}else{
	$('#demobtn').addClass('in');
}
$(".btndrop").addClass(accordionfilter);

function setCookie(Key, Val) {
	var expires = new Date();
	expires.setTime(expires.getTime() + (Val * 24 * 60 * 60 * 1000));
	var daysToExpire = new Date(2147483647 * 1000).toUTCString();
	document.cookie = Key + '=' + Val + ';expires=' + daysToExpire;
}

function getCookie(Key) {
	var keyValue = document.cookie.match('(^|;) ?' + Key + '=([^;]*)(;|$)');
	return keyValue ? keyValue[2] : null;
}

</script>













