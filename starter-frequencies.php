<?php

error_reporting(0);
include('array.php');
include('constants.php');
$favorites = $favorite_or_not = array();
if (isset($_SESSION['email'])) {
  $header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
  $url = FAVORITE_URL;
  $res = curl_post($url, '', $header);
  $response = json_decode($res['res']);

  if ($response->favorite->fetch_flag == -1) {
    $favorite = array();
  } else {
    $favorite = $response->favorite;
  }
  foreach ($response->favorite as $v) {
    $favorite_or_not[$v->id] = $v->is_favorite;
  }
}

$url = FREE_ALBUMS_URL;
$res = curl_post($url, '', $header);
$response = json_decode($res['res']);
$free_albums = $response->free_albums;

$url = FEATURED_ALBUMS_URL;
$res = curl_post($url, '', $header);
$response = json_decode($res['res']);
$featured_albums = $response->featured_albums;

if ($_GET['sort'] == 'favourite') {

  if (!empty($favorite)) {
    foreach ($free_albums as $v) {
      $free[$v->id] = $v;
    }
    foreach ($featured_albums as $v) {
      $featured[$v->id] = $v;
    }
    foreach ($favorite as $v) {
      if (!empty($free[$v->id])) {
        $favorites[] = $v;
        $fav_ids[] = $v->id;
      }
      if (!empty($featured[$v->id])) {
        $favorites1[] = $v;
        $fav_ids1[] = $v->id;
      }
    }
  }
  // print_r($featured);
  // die;
  if (!empty($favorites)) {
    $top_fav = SortByKeyList($free, $fav_ids);
    $top_fav1 = SortByKeyList($featured, $fav_ids1);
    foreach ($favorites as $v) {
      unset($free[$v->id]);
    }
    foreach ($favorites1 as $v) {
      unset($featured[$v->id]);
    }
    $free_albums = array_merge($top_fav, $free);
    $featured_albums = array_merge($top_fav1, $featured);
    // print_r($featured_albums);
    // die;
  }
}

function sortByKeyList($array, $seq)
{
  $ret = array();
  if (empty($array) || empty($seq)) return false;
  foreach ($seq as $key) {
    $ret[$key] = $array[$key];
  }
  return $ret;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <title>Starter - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
</head>
<?php include 'header.php'; ?>
<?php if (isset($_SESSION['verified']) && $_SESSION['verified'] == 0) { ?>
  <div class="alert alert-warning" role="alert">
    <p class="verify_warning">Must verify email to play frequencies other than free ones.</p>
  </div>
<?php  } ?>

<body id="con_listing">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10 serch_box1">
          <div class="row">
            <div class="col-md-4">
              <h3 class="main-title">Free Frequencies</h3>
            </div>
            <div class="col-md-4 form-group has-search"> <span class="fa fa-search form-control-feedback"> </span>
              <form method="get" action="rife_frequencies_list.php">
                <input type="text" class="form-control" name="keyword" id="search" placeholder="Search">
              </form>
            </div>

            <div class="col-md-4 form-group drop">
              <label for="sort">Sort by:</label>
              <select name="sort" id="sort" class="form-control">
                <option value="recent" <?php echo ($_GET['sort'] == 'recent' ? 'selected' : ''); ?>>Recent</option>
                <option value="favourite" <?php echo ($_GET['sort'] == 'favourite' ? 'selected' : ''); ?>>Favourite</option>
                <option value="recommended" <?php echo ($_GET['sort'] == 'recommended' ? 'selected' : ''); ?>>Recommended</option>
              </select>
            </div>
          </div>

          <div class="row response"></div>

          <?php /*if ($_GET['sort'] == 'favourite') { ?>
            <div class="row sort-favorite">
              <?php if (empty($favorites)) {
                echo "<center><h3>You Don't Have Any Favourites Frequencies</h3></center>";
              } else { ?>
                <?php foreach ($favorites as $value) { ?>
                  <div class="col-xs-6 col-md-3">
                    <div class="new">
                      <a href="inner_frequencies.php?id=<?php echo $value->id . '&category=' . (empty($album[$value->id]->categoryId) ? 1 : $album[$value->id]->categoryId) ?>">
                        <img src="<?php echo (!empty($album[$value->id]->audio_folder) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $album[$value->id]->audio_folder . '/' . $album[$value->id]->image : 'images/freaquecy.png'); ?>" width="126" height="126" />
                      </a>
                      <span data-album="<?php echo $value->id; ?>" data-favorite="1" class="favorite yes"></span>
                      <div class="card-body">
                        <h5 class="card-title">
                          <b><?php echo (!empty($value->title) ? $value->title : $album[$value->id]->title); ?></b>
                        </h5>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          <?php }*/ ?>

          <?php if (!empty($free_albums)) { ?>
            <div class="row free-album">
              <?php foreach ($free_albums as $v) { ?>
                <div class="col-xs-6 col-md-3">
                  <div class="new">
                    <a href="inner_frequencies.php?id=<?php echo $v->id . '&category=' . $v->categoryId; ?>">
                      <img src="<?php echo (!empty($v->audio_folder) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $v->audio_folder . '/' . $v->image : 'images/freaquecy.png'); ?>" width="126" height="126" />

                    </a>

                    <?php if (isset($_SESSION['email'])) { ?>
                      <span data-album="<?php echo $v->id; ?>" data-favorite="<?php echo ($favorite_or_not[$v->id] == 1 ? 1 : 0); ?>" class="favorite <?php echo ($favorite_or_not[$v->id] == 1 ? 'yes' : 'no'); ?>"></span>

                    <?php } ?>
                    <div class="card-body">
                      <h5 class="card-title">
                        <b><?php echo $v->title; ?> </b>
                      </h5>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>

          <?php if (!empty($featured_albums)) { ?>
            <hr style="width: 100%;margin: 10px 0;">
            <div class="row featured-albums">
              <div class="col-md-12">
                <h3 class="main-title">Featured Frequencies</h3>
              </div>
              <?php foreach ($featured_albums as $v) { ?>
                <div class="col-xs-6 col-md-3">
                  <div class="new">
                    <a href="inner_frequencies.php?id=<?php echo $v->id . '&category=' . $v->categoryId; ?>">
                      <img src="<?php echo (!empty($v->audio_folder) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $v->audio_folder . '/' . $v->image : 'images/freaquecy.png'); ?>" width="126" height="126" />

                    </a>
                    <?php if (isset($_SESSION['email'])) { ?>
                      <span data-album="<?php echo $v->id; ?>" data-favorite="<?php echo ($favorite_or_not[$v->id] == 1 ? 1 : 0); ?>" class="favorite <?php echo ($favorite_or_not[$v->id] == 1 ? 'yes' : 'no'); ?>"></span>

                    <?php   } ?>

                    <div class="card-body">
                      <h5 class="card-title">
                        <b><?php echo $v->title; ?> </b>
                      </h5>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>

          <div class="row">
          <div class="col-md-12 bottom-banner">
            <a href="https://qilifestore.com/collections/qi-coils/products/qi-coil-max-transformation-system" target="_blank" class="ad-mob-image-horizontal">
              <img src="https://members.qicoil.com/images/qc-max-admob-horizontal.jpg" alt="qi-coil-max-transformation-system" class="img_banner" />
            </a>
                    </div>
          </div>

        </div>
      </div>
      <!--	</div>-->
    </div>
  </div>
  </div>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script>
    $(document).ready(function() {
      var customRenderMenu = function(ul, items) {
        var self = this;
        var categoryArr = [];

        function contain(item, array) {
          var contains = false;
          $.each(array, function(index, value) {
            if (item == value) {
              contains = true;
              return false;
            }
          });
          return contains;
        }

        $.each(items, function(index, item) {
          if (!contain(item.category, categoryArr)) {
            categoryArr.push(item.category);
          }
          // console.log(categoryArr);
        });

        $.each(categoryArr, function(index, category) {
          // console.log(category);
          if (typeof category === "undefined") {} else {
            if (category == '') var category_name = 'Rife';
            if (category == 2) var category_name = 'Quantum';
            if (category == 3) var category_name = 'Higher Quantum';
            if (category == 4) var category_name = 'Inner Circle';
            ul.append("<li class='ui-autocomplete-group'>" + category_name + "</li>");
          }
          $.each(items, function(index, item) {
            if (item.category == category) {
              self._renderItemData(ul, item);
            }
          });
        });
      };

      $("#search").autocomplete({
        minLength: 0,
        source: function(request, response) {
          var url = 'frequencies.php?search=' + request.term + '&ajax=1&limit=30';
          $.getJSON(url, {}, response);
        },
        create: function() {
          //access to jQuery Autocomplete widget differs depending
          //on jQuery UI version - you can also try .data('autocomplete')
          $(this).data('uiAutocomplete')._renderMenu = customRenderMenu;
        },
        search: function() {
          var term = this.value;
          if (term.length < 2) {
            return false;
          }
        },
        select: function(event, ui) {
          // console.log(ui.item.frequencies);
          if (ui.item.value != 'No Frequency found') {
            var category = ui.item.category;
            // console.log(category);
            var categories = '';
            if (category) categories = '&category=' + category;
            var img = 'images/freaquecy.png';
            if (ui.item.image != '' && ui.item.folder != '') {
              img = 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' + ui.item.folder + '/' + ui.item.image;
            }
            $("#search").val(ui.item.value);
            $('.response').html('');
            $('.response').append('<div class="col-xs-6 col-md-3 "><div class="new"><a href="inner_frequencies.php?id=' + ui.item.key + categories + '"><img src="' + img + '" height="126" width="126"> </a><span><div class="card-body"><h5 class="card-title"><b>' + ui.item.value + '</b> </h5></div></div></div>');

            $("#search").val('');
            $(".featured-albums, .free-album, .sort-favorite, .pagination").hide();
          }
        }
      });
      $("#demo").on("hide.bs.collapse", function() {
        $(".btn").html('<span class="glyphicon glyphicon-collapse-down"></span> Open');
      });
      $("#demo").on("show.bs.collapse", function() {
        $(".btn").html('<span class="glyphicon glyphicon-collapse-up"></span> Close');
      });

      $("#sort").change(function() {
        // alert($('option:selected', $(this)).text());
        var value = $(this).val();
        // alert(value);
        var url = window.location.href;
        var url = removeURLParameter(url, 'sort');
        // alert(url);
        if (url.indexOf("?") > -1) {
          var final_url = url + '&sort=' + value;
        } else {
          var final_url = url + '?sort=' + value;
        }
        // alert(final_url);
        window.location.href = final_url;
      });

    });



    function removeURLParameter(url, parameter) {
      //prefer to use l.search if you have a location/link object
      var urlparts = url.split('?');
      if (urlparts.length >= 2) {
        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);
        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0;) {
          //idiom for string.startsWith
          if (pars[i].lastIndexOf(prefix, 0) !== -1) {
            pars.splice(i, 1);
          }
        }
        return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
      }
      return url;
    }
  </script>

  <?php
  include('footer.php');
  ?>

</body>

</html>