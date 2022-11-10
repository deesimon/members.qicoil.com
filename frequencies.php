<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include('array.php');
include('constants.php');
// session_start();

$favorites = $favorite_or_not = array();
if (isset($_SESSION['email'])) {
  $header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
  $url = FAVORITE_URL;
  $res = curl_post($url, '', $header);
  // print_r($res['res']);die;
  $response = json_decode($res['res']);
  // print_r($response);// die;
  if ($response->favorite->fetch_flag == -1) {
    $favorite = array();
  } else {
    $favorite = $response->favorite;
  }
  foreach ($response->favorite as $v) {
    // $favorites[] = $v->id;
    $favorite_or_not[$v->id] = $v->is_favorite;
  }
}

$header = array('Content-Type: application/x-www-form-urlencoded');

if (empty($_GET['category'])) $_GET['category'] = 1;

if (!empty($_GET['id'])) {
  $post_data['id'] = $_GET['id'];
}

if (!empty($_GET['category'])) {
  $post_data['category'] = $_GET['category'];
}

if (!empty($_GET['page'])) {
  $post_data['page'] = $_GET['page'];
} else $post_data['page'] = 1;

if ($_GET['page'] > 9) {
  $page_pagination = $post_data['page'] + 1;
} else $page_pagination = 11;

if (!empty($_GET['subcategory'])) {
  $post_data['subcategory'] = $_GET['subcategory'];
} else {
  $post_data['limit'] = 9;
}

if (!empty($_GET['search'])) {
  $post_data['keyword'] = $_GET['search'];
  $post_data['ajax'] = $_GET['ajax'];
  $post_data['limit'] = $_GET['limit'];
}

$post_data = http_build_query($post_data);
// print_r($post_data);
// die;
$url = FREQUENCIES_URL;
$res = 'curl_post'($url . '?' . $post_data, '', $header);
$response = json_decode($res['res']);
$frequencies = $response->frequencies;
//print_r($frequencies);
// echo '<br><br><b>URL:</b> ' . $url . "\n\n<br/><br><b>POST DATA:</b> " . $post_data . "\n\n<br/><br><b>RES:</b>" . $res['res'];
// exit;
foreach ($SUBCATEGORIES as $v) { //print_r($v);
  if ($v->id == $_GET['subcategory']) {
    $subcategory = $v->name;
  }
}
if (!empty($_GET['search'])) {
  echo $res['res'];
  die;
}

if ($_GET['sort'] == 'favourite') {

  if (!empty($favorite)) {
    foreach ($frequencies as $v) {
      $arr1[$v->id] = $v;
    }
    foreach ($favorite as $v) {
      if (!empty($arr1[$v->id])) {
        $favorites[] = $v;
        $fav_ids[] = $v->id;
      }
    }
  }
  if (!empty($favorites)) {
    $top_fav = SortByKeyList($arr1, $fav_ids);
    foreach ($favorites as $v) {
      unset($arr1[$v->id]);
    }
    $frequencies = array_merge($top_fav, $arr1);
    // print_r($frequencies);
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
  <title>Frequencies - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
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
        <div class="col-md-9 serch_box1">
          <div class="row">
            <div class="col-md-4">
              <h3 class="main-title"><?php echo ((!empty($_GET['subcategory']) ? ucfirst($subcategory) : ucfirst($CATEGORIES[$_GET['category']]))); ?> Frequencies</h3>
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

          <div class="row response">
            <?php //print_r($frequencies);
            $i = 0;
            foreach ($frequencies as $v) { //print_r($v);die;
              $i++;
            ?>
              <div class="col-xs-6 col-md-4 ">
                <div class="new">
                  <a href="inner_frequencies.php?id=<?php echo $v->id;
                                                    if (!empty($_GET['category'])) echo '&category=' . $_GET['category']; ?>">

                    <img src="<?php echo (!empty($v->audio_folder) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $v->audio_folder . '/' . $v->image : 'images/freaquecy.png'); ?>" width="126" height="126"> </a>
                  <?php if (isset($_SESSION['email'])) { ?>
                    <span data-album="<?php echo $v->id; ?>" data-favorite="<?php echo ($favorite_or_not[$v->id] == 1 ? 1 : 0); ?>" class="favorite <?php echo ($favorite_or_not[$v->id] == 1 ? 'yes' : 'no'); ?>"></span>
                  <?php   } ?>
                  <div class="card-body">
                    <h5 class="card-title"><b>
                        <?php
                        echo $v->title;
                        ?>
                      </b> </h5>
                    <!-- <p class="card-text"><?php echo $GLOBALS['CATEGORIES'][$_GET['category']]; ?> Frequencies for <?php echo $v->title; ?></p> -->
                  </div>
                </div>
              </div>
            <?php
            }
            ?>
          </div>

          <?php if (empty($response)) { ?>
            <div class="col-md-12">
              <h5>No Record Found</h5>
            </div>
          <?php } ?>
          <?php if (($_GET['category'] == '1' || empty($_GET['category'])) && empty($_GET['id'])) { ?>
            <div class="col-md-12">
              <nav aria-label="Page navigation example">
                <ul class="pagination">
                  <?php if ($_GET['page'] > 1) { ?>
                    <li class="page-item active"><a class="page-link" href="?page=<?php echo $_GET['page'] - 1 ?>&category=<?php echo $_GET['category']; ?>&subcategory=<?php echo $_GET['subcategory']; ?>&sort=<?php echo $_GET['sort']; ?>">Prev</a></li>
                  <?php } ?>
                  <?php for ($i = 0; $i < 10; $i++) {
                    $pagination = $page_pagination - 10 + $i;
                    if (empty($response) && $i >= $_GET['page']) break;
                  ?>
                    <li class="page-item<?php echo ($_GET['page'] == $pagination || ($i == 0 && empty($_GET['page']))) ? ' active disabled' : '';  ?>"><a class="page-link" href="?page=<?php echo $pagination . '';
                                                                                                                                                                                        '' ?>&category=<?php echo $_GET['category']; ?>&subcategory=<?php echo $_GET['subcategory']; ?>&sort=<?php echo $_GET['sort']; ?>"><?php echo $pagination; ?></a></li>
                  <?php } ?>
                  <?php if (!empty($response)) { ?>
                    <?php if (count($response->frequencies) == 9) {
                    ?>
                      <li class="page-item<?php echo ($_GET['page'] == $pagination) ? ' active' : '';  ?>"><a class="page-link" href="?page=<?php echo $page_pagination; ?>&category=<?php echo $_GET['category']; ?>&subcategory=<?php echo $_GET['subcategory']; ?>&sort=<?php echo $_GET['sort']; ?>">Next</a></li>
                    <?php } ?>
                  <?php } ?>
                </ul>
              </nav>
            </div>
          <?php } ?>

          <a href="https://qilifestore.com/collections/qi-coils/products/qi-coil-max-transformation-system" target="_blank" class="ad-mob-image-horizontal">
            <img src="https://members.qicoil.com/images/qc-max-admob-horizontal.jpg" alt="qi-coil-max-transformation-system"/>
          </a>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
  </div>
  </div>

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
            $('.response').append('<div class="col-xs-6 col-md-4 "><div class="new"><a href="inner_frequencies.php?id=' + ui.item.key + categories + '"><img src="' + img + '" height="126" width="126"> </a><div class="card-body"><h5 class="card-title"><b>' + ui.item.value + '</b> </h5></div></div></div>');
            $("#search").val('');
          }
        }
      });

      $("#demo").on("hide.bs.collapse", function() {
        $(".btn").html('<span class="glyphicon glyphicon-collapse-down"></span> Open');
      });
      $("#demo").on("show.bs.collapse", function() {
        $(".btn").html('<span class="glyphicon glyphicon-collapse-up"></span> Close');
      });

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