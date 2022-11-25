<?php 
$frequencies = $mp3s = array();
$header = array('Content-Type: application/x-www-form-urlencoded');

// Anjani code start  final

if(!empty($_SESSION)){
  if(!isset($_REQUEST['category'])){
    if(in_array(1,$_SESSION['category_ids'])){
      $lock_class_name = 'category_paid';
    }else{
      $lock_class_name = 'lock';
    }
  }
  elseif(!in_array($_REQUEST['category'],$_SESSION['category_ids'])){
    $lock_class_name = 'lock';
  }
}else{
  $lock_class_name = 'lock';
}

// Anjani code end


//if($_GET['type'] == 'rife'){
$url = 'https://apiadmin.qienergy.ai/api/frequencies';
if (!empty($_GET['id'])) {
  $post_data['id'] = $_GET['id'];
}
if (!empty($_GET['category'])) {
  $post_data['category'] = $_GET['category'];
}
//$post_data = array("id" => $_GET['id']);
$res = curl_post($url, $post_data, $header);
$response = json_decode(($res['res']));

$id = $response->frequencies[0]->id;
$audio_folder = $response->frequencies[0]->audio_folder;
if (!empty($response->frequencies[0]->image)) {
  $image = $audio_folder . '/' . $response->frequencies[0]->image;
}

$description = $response->frequencies[0]->description;
$title = $response->frequencies[0]->title;
$likes = $response->frequencies[0]->likes;
$frequenciess = $response->frequencies[0]->frequencies;
if (!empty($frequenciess)) {
  $frequencies = explode("/", $frequenciess);
  //print_r($frequenciess);die;
} else {
  $url = 'https://apiadmin.qienergy.ai/api/mp3';
  $post_data = http_build_query(array("frequency_id" => $_GET['id']));
  $res = curl_post($url, $post_data, $header);
  $mp3_response = json_decode(($res['res']), true);

  //echo '1';print_r($res);exit;
  $mp3s = $mp3_response;
  $first_mp3 = 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $audio_folder . '/' . $mp3s[0]['filename'];

  // $mp3s = array(
  //           array('name'=>'preview1','filename'=>'preview1.mp3'),
  //           array('name'=>'preview2','filename'=>'preview2.mp3'),
  //           array('name'=>'preview3','filename'=>'preview3.mp3'),
  //           array('name'=>'preview4','filename'=>'preview1.mp3'),
  //           array('name'=>'preview5','filename'=>'preview2.mp3'),
  //           array('name'=>'preview6','filename'=>'preview3.mp3'),
  //           array('name'=>'preview7','filename'=>'preview1.mp3'),
  //           array('name'=>'preview8','filename'=>'preview2.mp3'),
  //           array('name'=>'preview9','filename'=>'preview3.mp3'),
  //           array('name'=>'preview10','filename'=>'preview1.mp3'),
  //         );
  // $first_mp3 = 'https://members.qicoil.com/'.$mp3s[0]['filename'];
}

$playlists = array();
if (isset($_SESSION['id'])) {
  $url = 'https://apiadmin.qienergy.ai/api/getplaylist?userid=' . $_SESSION['id'];
  $post_data = '';
  $res = curl_post($url, $post_data, $header);
  // print_r($res);die;
  $playlist_res = json_decode(($res['res']));
  if ($playlist_res->playlist->rsp_msg == '') {
    $playlists = $playlist_res->playlist;
  }
  // print_r($playlist_res);
  // die;
}

$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
$url = 'https://apiadmin.qienergy.ai/api/favorite/get';
$res = curl_post($url, '', $header);
// print_r($res['res']);die;
$response = json_decode($res['res']);
// print_r($response);// die;
$favorites = $favorite_or_not = array();
foreach ($response->favorite as $v) {
  $favorite_or_not[$v->id] = $v->is_favorite;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Inner Frequencies - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <style>
    span.error {
      color: red;
    }

    button {
      border: 0px;
      background: none;
    }

    #pause {
      display: none;
    }

    .list_voice .intro {
      color: #409f83;
      padding: 0;
    }

    #context-menu-items {
      background-color: #ecf5f4;
    }

    #context-menu-items .dropdown-menu {
      left: -163px;
      top: 0px;
      padding: 10px;
      background-color: #ecf5f4;
    }

    #context-menu-items .dropdown-menu li {
      padding: 5px;
    }

    #context-menu-items .dropdown-menu li:first-child {
      border-bottom: 1px solid;
    }

    #context-menu-items label {
      padding: 5px;
      cursor: pointer;
      border-radius: 5px;
    }

    .dropdown-menu li {
      cursor: pointer;
    }

    .context-menu-container .dropdown-menu>li:hover {
      background-color: #c4c4c4;
    }
 
  </style>

</head>

<body>
  <?php include 'header.php'; ?>
  <section id="inner_detail">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">


        <div class="custom-container">
          <div class="back-arrow-container">
          <a href="frequencies.php"> <img src="images/left.png" class="left_aerrow_bg"> </a>
          </div>
          <div class="search-container">
          <div class="form-group has-search  offset-1" style="width:100%; float: right"> <span class="fa fa-search form-control-feedback"></span>
                <form method="get" action="rife_frequencies_list.php">
                  <input type="text" name="keyword" class="form-control col-md-12" placeholder="Search" id="search">
                </form>
           </div>
          </div>
          </div>





          <div class="col-md-6 col-sm-6">
                     <div class="col-md-11">
              <div class="col-md-12">
                <h5><?php echo $title ?></h5>
              </div>

              <div class="col-md-8">
                <img src="<?php echo (!empty($image) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $image : 'images/freaquecy.png'); ?>" width="126" height="126" class="sun">
                <?php if (isset($_SESSION['email'])) {  ?>
                  <span data-album="<?php echo $_GET['id']; ?>" data-favorite="<?php echo ($favorite_or_not[$_GET['id']] == 1 ? 1 : 0); ?>" class="favorite <?php echo ($favorite_or_not[$_GET['id']] == 1 ? 'yes' : 'no'); ?>" style=" vertical-align: top; "></span>
                <?php } ?>
              </div>

              <div class=" col-md-8"><?php 
                                          //echo $description 
                                      ?></div>

            </div>
            <div class=" col-md-12 border_bottom"> </div>
          </div>

          <div class="col-md-6 col-xs-12 p-0 stand">
            <div class="col-md-10 col-xs-12 p-0">
              
              <div class="play_box col-md-12">
                <div class="white_bg1 col-md-10 col-sm-10 offset-1 <?php echo $lock_class_name; ?>" id="back_bg" onClick="payment_redirect_function()" >
                  <div class="col-md-10 pt-5 button_left mt-3 col-xs-9">
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><button type="button" class="stopbtn" id="stopBtn" <?php echo $disabled; ?>><img src=" images/left_btn.png"></button></a> 
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><button type="button" class="plybtn" onClick="playNote()" id="play" <?php echo $disabled; ?>> <img src="images/middle.png"></button></a>
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><button type="button" id="pause"><img src="images/mute.png" <?php echo $disabled; ?>></button></a>
                    <!-- <button type="button" class="repeate" id="repeateBtn" data-status='' <?php echo $disabled; ?>> <img src="images/repeat-on.png"></button>
                  <button type="button" class="repeateoff" id="repeateoff_btn" data-status='' <?php echo $disabled; ?>><img src="images/repeat-off.png"></button> -->
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><span class="repeate off" id="repeateBtn" data-status=0 <?php echo $disabled; ?>></span></a>
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><span data-shuffle="0" class="shuffle_btn off"></span></a>
                    <div class="col-md-12 pt-3">
                      <?php if (empty($_GET['category']) || $_GET['category'] == 1) { ?>
                        <input type="hidden" class="fre_number" value="<?php echo $frequencies[0]; ?>" name="fre" id="fre" readonly />
                        <label class="fre_number_text"><?php echo $frequencies[0]; ?> Hz</label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                        <span id="duration"></span>
                      <?php } else { ?>
                        <audio id="sound">
                          <source src="<?php echo $first_mp3; ?>" type="audio/mpeg" />
                        </audio>
                        <label class="fre_number_text"><?php echo str_replace('.mp3', '', $mp3s[0]['filename']); ?></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                        <span id="duration"></span>
                        <p><img class="loading_fre hide" src="images/load.gif" width="120"></p>
                      <?php } ?>

                    </div>
                  </div>
                  <div class="col-md-2 col-xs-3 pt-5">
                    <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>"><div class="volume">
                      <div class="vol_up"><img src="images/ic_volume_up_24.png"></div>
                      <div class="vol_line">
                        <input type="range" orient="vertical" min="0" max="10" value="5" disabled />
                      </div>
                      <div class="vol_stop"><img src="images/ic_volume_mute_.png"></div>
                    </div></a> 
                  </div>
                  <canvas id="canvas" width="400"> </canvas>
                </div>
                <div class=" col-md-10 col-sm-10 offset-1 pp">
                  <div class='music_list_wrap'>



                 <a href="https://members.qicoil.com/payment.php?type=<?php echo $_GET['category']; ?>">UNLOCK </a>



                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>

  <style type="text/css">
    .music_list_wrap{
      background-color: #b59126c7;
      color: #fff;
      padding: 15px;
      font-weight: bold;
    }

    .music_list_wrap a{
      text-decoration: none;
      color: #e5e5e5;
      display: block;
    }

    /*css for lock*/
     .lock{
         position: relative;
     }
     .lock:before {
    content: '';
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0px;
    left: 0px;
    background-color: #100d0d6e;
}

     .lock:after {
    content: '\f023 ';
    font-family: 'FontAwesome';
    position: absolute;
    color: #fff;
    left: 50%;
    top: 50%;
    font-size: 40px;
    transform: translate(-50%, -50%);
     }

     /* css for lock end */

</style>

<script type="text/javascript">
  
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
      $("#search").val(ui.item.value);
      if (ui.item.value != 'No Frequency found') {
        var category = ui.item.category;

        if (category == '') category = 1;
        urlset = "frequencies.php?category=" + category + "&id=" + ui.item.key;
        window.location.href = urlset;
      }
    }
  });


  function payment_redirect_function(){
    window.location.href = '<?php echo $payment_url; ?>';
    return false;
  }



</script>