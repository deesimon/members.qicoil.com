
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

</script>













