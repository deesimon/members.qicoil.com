<?php
error_reporting(0);
include('array.php');
include('constants.php');
// session_start();
if (empty($_GET['category'])) $_GET['category'] = 1;
//if (in_array($_GET['id'], $free_albums)) {
//} else {
if (!isset($_SESSION['email'])) {
  header('Location:register.php');
  exit;
}
//}

if (!empty($_GET['search'])) {
  $header = array('Content-Type: application/x-www-form-urlencoded');
  $url=FREQUENCIES_URL;
  $post_data = http_build_query(array('keyword' => $_GET['search'], 'ajax' => $_GET['ajax']));
  $res = curl_post($url . '?' . $post_data, '', $header);
  // $res = json_decode($res['res']);
  // $frequencies = $res->frequencies;
  echo $res['res'];
  die;
}

$url=FREE_ALBUMS_URL;
$res = curl_post($url, '', $header);
$response = json_decode($res['res']);
foreach ($response->free_albums as $v) {
  $free_albums[] = $v->id;
}

if ($_GET['id'] != '5964') {
  if (in_array($_GET['id'], $free_albums)) {
    $disabled = '';
  } else {
    $disabled = 'disabled';
    if (empty($_GET['category']) || $_GET['category'] == 1) {
      if (in_array($_GET['category'], $_SESSION['category_ids'])) {
        $disabled = '';
      } else {
        $payment_url = RIFE_PAYMENT_URL;
      }
    } else {
      if ($_GET['category'] == 2) {
        $payment_url = QUANTUM_PAYMENT_URL;
      } elseif ($_GET['category'] == 3) {
        $payment_url = HIGHER_QUANTUM_PAYMENT_URL;
      } else {
        $payment_url = INNER_CIRCLE_PAYMENT_URL;
      }
      if (in_array($_GET['id'], $_SESSION['album_ids'])) {
        $disabled = '';
        $payment_url = '';
      }
    }
  }
}
if (!isset($_SESSION['email'])) {
  if ($_GET['id'] != '5964' && in_array($_GET['id'], $free_albums)) {
    $payment_url = 'register.php';
  }
  // else{
  //   $payment_url = '';
  // }
}
//  echo $payment_url;
//  die;

if (!empty($payment_url) && $disabled != '') {
  header('Location:' . $payment_url);
  exit;
}

//print_r($_SESSION);
$frequencies = $mp3s = array();
$header = array('Content-Type: application/x-www-form-urlencoded');
//if($_GET['type'] == 'rife'){
$url =FREQUENCIES_URL;
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
  $url=MP3_URL;
  $post_data = http_build_query(array("frequency_id" => $_GET['id']));
  $res = curl_post($url, $post_data, $header);
  $mp3_response = json_decode(($res['res']), true);

  //echo '1';print_r($res);exit;
  $mp3s = $mp3_response;
  $first_mp3=FIRST_MP3_URL. $audio_folder . '/' . $mp3s[0]['filename'];
}
// print_r($mp3s);die;

$playlists = array();
if (isset($_SESSION['id'])) {
  $url=GETPLAYLIST_URL.'?userid=' . $_SESSION['id'];
  $post_data = '';
  $res = curl_post($url, $post_data, $header);
  // print_r($res);die;
  $playlist_res = json_decode(($res['res']));
  if ($playlist_res->playlist->rsp_msg == '') {
    $playlists = $playlist_res->playlist;
  }
  // print_r($playlists);die;
}

$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
$url=FAVORITE_URL;
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
  <title>Inner Frequencies - Qi Coil WebApp (BETA) </title>
  <?php include 'head.php'; ?>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
</head>
<body>
  <?php include 'header.php'; ?>
  <?php if (isset($_SESSION['verified']) && $_SESSION['verified'] == 0 && !in_array($_GET['id'], $free_albums)) { ?>
    <div class="alert alert-warning" role="alert">
      <p class="verify_warning">Must verify email to play frequencies.</p>
    </div>
  <?php } else { ?>
    <section id="inner_detail">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6 col-sm-6">
              <div class="col-md-1"><a href="<?php echo (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'frequencies.php'); ?>"> <img src="images/left.png" class="left_aerrow_bg"> </a> </div>
              <div class="col-md-12">
                <div class="col-md-12">
                  <h1 class="freq-title"><?php echo $title ?></h1>
                </div>

                <div class="col-md-5">
                  <img src="<?php echo (!empty($image) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $image : 'images/freaquecy.png'); ?>" width="126" height="126" class="sun">
                  <?php if (isset($_SESSION['email'])) {  ?>
                    <span data-album="<?php echo $_GET['id']; ?>" data-favorite="<?php echo ($favorite_or_not[$_GET['id']] == 1 ? 1 : 0); ?>" class="inner-player-fave favorite <?php echo ($favorite_or_not[$_GET['id']] == 1 ? 'yes' : 'no'); ?>" style=" vertical-align: top; "></span>
                  <?php } ?>
                </div>
                <div class="col-md-7"> <?php  echo nl2br($description)?></div>

              </div>
              <div class="col-md-12 border_bottom"> </div>
            </div>
            <div class="col-md-6 col-xs-12 p-0 stand">
              <div class="col-md-10 col-xs-12 p-0">
                <div class="form-group has-search  offset-1" style="width:100%"> <span class="fa fa-search form-control-feedback"></span>
                  <form method="get" action="rife_frequencies_list.php">
                    <input type="text" name="keyword" class="form-control col-md-12" placeholder="Search" id="search">
                  </form>
                </div>
                <div class="play_box col-md-12">
                  <div class="white_bg1 col-md-12 col-sm-12" id="back_bg">
                    <div class="col-md-12 pt-5 mt-3 col-xs-12 b_btn">
                      <button type="button" class="stopbtn" id="stopBtn" <?php echo $disabled; ?>><img src=" images/left_btn.png"></button>
                      <button type="button" class="plybtn" onClick="playNote()" id="play" <?php echo $disabled; ?>> <img src="images/middle.png"></button>
                      <button type="button" id="pause"><img src="images/mute.png" <?php echo $disabled; ?>></button>
                      <!-- <button type="button" class="repeate" id="repeateBtn" data-status='' <?php echo $disabled; ?>> <img src="images/repeat-on.png"></button>
                    <button type="button" class="repeateoff" id="repeateoff_btn" data-status='' <?php echo $disabled; ?>><img src="images/repeat-off.png"></button> -->
                      <span class="repeate off" id="repeateBtn" data-status=0 <?php echo $disabled; ?>></span>
                      <span data-shuffle="0" class="shuffle_btn off"></span>
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
            
                    <canvas id="canvas" width="400"> </canvas>
                  </div>
                  <div class=" col-md-12 col-sm-12 pp">
                    <ul class="list_voice">
                      <?php
                      $i = 1;
                      if (empty($_GET['category']) || $_GET['category'] == '1') {
                        foreach ($frequencies as $v) {

                          // echo  $v->likes;
                      ?>
                          <li class="hz" data-random="<?php echo $i; ?>">

                            <?php if (!empty($disabled)) { ?>
                              <a href="<?php echo $payment_url; ?>" <?php echo ($i == 1 ? 'style=" color: #409f83; "' : ''); ?>>
                                <h3><?php echo $v . ' Hz' ?></h3>
                              </a>
                            <?php } else { ?>
                              <h3 <?php echo ($i == 1 ? 'class="intro"' : ''); ?> data-fre="<?php echo $v; ?>"><?php echo $v . ' Hz' ?>
                                <!-- <span class="context-menu pull-right" data-container-id="context-menu-items" data-row-id="<?php echo $v['id']; ?>"></span> -->
                              </h3>
                            <?php } ?>
                          </li>
                        <?php $i++;
                        }
                      } else {
                        foreach ($mp3s as $v) {
                          if (!empty($disabled)) {
                            $href = $payment_url;
                          } else {
                            $href = "https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/" . $audio_folder . "/" . $v['filename'];
                            // $href = 'https://members.qicoil.com/'.$v['filename'];
                          }
                        ?>
                          <li class="hz" data-random="<?php echo $i; ?>"> <a <?php echo ($i == 1 ? 'class="intro"' : ''); ?> href="<?php echo $href; ?>"> <?php echo str_replace('.mp3', '', $v['filename']); ?> </a>
                            <span class="context-menu pull-right" data-container-id="context-menu-items" data-row-id="<?php echo $v['id']; ?>"></span>
                          </li>
                      <?php $i++;
                        }
                      } ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
  <?php } ?>
  <?php if (!empty($disabled)) { ?>
    <script>
      $("#back_bg").css('cursor', 'pointer');
      $("#back_bg").click(function() {
        window.location.href = '<?php echo $payment_url; ?>';
        return false;
      });
    </script>
  <?php } ?>
  <?php if (isset($_GET['category']) && $_GET['category'] != 1) { ?>
    <script>
      $(".list_voice li a").click(function(event) {
        event.preventDefault();
        var mp3 = $(this).attr('href');
        var mp3_name = $(this).text();
        $(this).parents('.list_voice').find('a').removeClass("intro");
        $(this).addClass("intro");

        $("#sound source").prop('src', mp3);
        $(".fre_number_text").text(mp3_name);

        var sound = $('audio[id^="sound"]')[0];
        $("#play").show();
        $("#pause").hide();
        sound.load();
        $('#duration').hide();

        return false;
      });

      $('#play, #pause, #stopBtn').click(function() {
        var ele = $(this);
        var sound = $('audio[id^="sound"]')[0];
        // console.log(sound);
        if (ele.hasClass('plybtn')) {
          playAudio(sound);
        } else if (ele.hasClass('stopbtn')) {
          stopAudio(sound);
        } else {
          pauseAudio(sound);
        }
      });

      AutomaticPlay(sound);

      function playAudio(sound) {
        $("#play").hide();
        $("#pause").show();
        $('.loading_fre').removeClass('hide');
        $('#duration').html('');
        sound.play();
        $(sound).bind('playing', function() {
          $('.loading_fre').addClass('hide');
        });
        showDuration(sound);
        $('#duration').show();
        $('.progress-bar').addClass('progress-bar-animated');
      }
      function pauseAudio(sound) {
        $("#play").show();
        $("#pause").hide();
        $('.progress-bar').removeClass('progress-bar-animated');
        sound.pause();
      }
      function stopAudio(sound) {
        $("#play").show();
        $("#pause").hide();
        sound.pause();
        sound.currentTime = 0; /*reset timer*/
        $('#duration').hide();
      }
      //Time Duration
      function showDuration(sound) {
        $(sound).bind('timeupdate', function() {
          //Get hours and minutes
          var s = parseInt(sound.currentTime % 60);
          var m = parseInt((sound.currentTime / 60) % 60);
          //Add 0 if seconds less than 10
          if (s < 10) {
            s = '0' + s;
          }
          var TotalDuration = '';
          if (!isNaN(sound.duration)) {
            var ts = parseInt(sound.duration % 60);
            var tm = parseInt((sound.duration / 60) % 60);
            ts = ('0' + ts).slice(-2);
            tm = ('0' + tm).slice(-2);
            TotalDuration = ' / ' + tm + ':' + ts;
          } else {
            TotalDuration = ' / 0:00';
          }

          $('#duration').html(m + ':' + s + TotalDuration);
          var value = 0;
          if (sound.currentTime > 0) {
            value = Math.floor((100 / sound.duration) * sound.currentTime);
          }
          $('.progress-bar').css('width', value + '%');
        });
      }

      function AutomaticPlay(sound) {
        sound.addEventListener('ended', function() {
          var mp3 = mp3_name = '';
          var shuffle = $('.shuffle_btn').attr('data-shuffle');
          var repeate = $('.repeate').attr('data-status');
          if (repeate == 2) {
            $('.list_voice li').each(function() {
              if ($(this).find('a').hasClass("intro")) {
                mp3 = $(this).find('a').attr('href');
                mp3_name = $(this).find('a').text();
              }
            });
          } else if (shuffle == 1) {
            var ran = random_mp3('mp3');
            mp3 = $('.list_voice li[data-random="' + ran + '"]').find('a').attr('href');
            mp3_name = $('.list_voice li[data-random="' + ran + '"]').find('a').text();
            // console.log(ran+'---'+mp3+'---'+mp3_name);
            $('.list_voice li').find('a').removeClass("intro");
            $('.list_voice li[data-random="' + ran + '"]').find('a').addClass("intro");
          } else {
            $('.list_voice li').each(function() {
              if ($(this).find('a').hasClass("intro")) {
                mp3 = $(this).next('li').find('a').attr('href');
                mp3_name = $(this).next('li').find('a').text();
                $(this).find('a').removeClass("intro");
                $(this).next('li').find('a').addClass("intro");
                if (typeof mp3 === "undefined") {
                  if (repeate == 1) {
                    mp3 = '<?php echo $first_mp3; ?>';
                    mp3_name = '<?php echo $mp3s[0]['filename']; ?>';
                    $(this).parents('ul').find('li:first').find('a').addClass("intro");
                  }
                }
                return false;
              }
            });
          }
          // console.log(mp3+'---'+mp3_name);
          var sound = $('audio[id^="sound"]')[0];
          sound.load();
          if (typeof mp3 === "undefined") {
            stopAudio(sound);
            $('.list_voice').find('li:first').find('a').addClass("intro");
            $("#sound source").prop('src', '<?php echo $first_mp3; ?>');
            $(".fre_number_text").text('<?php echo $mp3s[0]['filename']; ?>');
          } else {
            playAudio(sound);
            $("#sound source").prop('src', mp3);
            $(".fre_number_text").text(mp3_name);
          }
        });
      }

      //Volume Control
      $('.volume input').change(function() {
        var sound = $('audio[id^="sound"]')[0];
        sound.volume = parseFloat(this.value / 10);
      });

      function playNote() {}
    </script>
  <?php } else { ?>

    <script>
      // create web audio api context
      //var AudioContexts;
      var audioCtx = new(window.AudioContext || window.webkitAudioContext)();
      var os = '';
      var counter = 10;
      var timeout = '';
      var timeDisplay = document.getElementById("timer");
      var susres = document.getElementById("pause");
      //susres.setAttribute("disabled", "disabled");
      function playNote() {
        // alert(1)
        //create Oscillator node
        //susres.removeAttribute("disabled");
        var oscillator = os = audioCtx.createOscillator();
        var frequency = document.getElementById('fre').value;
        oscillator.type = 'sine';
        //oscillator.type = 'sine';
        oscillator.frequency.value = frequency; // value in hertz
        //oscillator.frequency.setValueAtTime(440, audioCtx.currentTime); // value in hertz
        oscillator.connect(audioCtx.destination);
        oscillator.start();
        var duration = 180000;
        timeout = setTimeout(
          function() {
            oscillator.stop();
            var fre = '';
            var shuffle = $('.shuffle_btn').attr('data-shuffle');
            var repeate = $('.repeate').attr('data-status');
            if (repeate == 2) {
              $('.list_voice li').each(function() {
                if ($(this).find('h3').hasClass("intro")) {
                  fre = $(this).find('h3').attr('data-fre');
                }
              });
            } else if (shuffle == 1) {
              var ran = random_mp3('rife');
              fre = $('.list_voice li[data-random="' + ran + '"]').find('h3').attr('data-fre');
              // console.log(ran+'---'+fre);
              $('.list_voice li').find('h3').removeClass("intro");
              $('.list_voice li[data-random="' + ran + '"]').find('h3').addClass("intro");
            } else {
              $('.list_voice li').each(function() {
                if ($(this).find('h3').hasClass("intro")) {
                  fre = $(this).next('li').find('h3').attr('data-fre');
                  $(this).find('h3').removeClass("intro");
                  $(this).next('li').find('h3').addClass("intro");
                  // timeDisplay.textContent = '';
                  // displayTime();
                  if (typeof fre === "undefined") {
                    if (repeate == 1) {
                      fre = '<?php echo $frequencies[0]; ?>';
                      $(this).parents('ul').find('li:first').find('h3').addClass("intro");
                    }
                  }
                  return false;
                }
              });
            }
            // console.log(fre);
            if (typeof fre === "undefined") {
              $("#fre").val('<?php echo $frequencies[0]; ?>');
              $(".fre_number_text").text('<?php echo $frequencies[0]; ?> Hz');
              $('.list_voice').find('li:first').find('h3').addClass("intro");
              $("#pause").trigger("click");
            } else {
              $("#fre").val(fre);
              $(".fre_number_text").text(fre + ' Hz');
              $(".plybtn").trigger("click");
              //clearTimeout(timeout);
            }
          }, duration);

      }
      function stopNote() {
        os.stop();
      }
      function resume() {
        //susres.onclick = function () {
        if (audioCtx.state === "running") {
          audioCtx.suspend().then(function() {
            // susres.textContent = "Resume Audio";
          });
        } else if (audioCtx.state === "suspended") {
          audioCtx.resume().then(function() {
            //susres.textContent = "Suspend Audio";
          });
        }
        //};
      }
      function displayTime() {
        if (audioCtx && audioCtx.state !== "closed") {
          timeDisplay.textContent = audioCtx.currentTime.toFixed(2) + ' Seconds';
        } else {
          timeDisplay.textContent = "Frequency not started";
        }
        requestAnimationFrame(displayTime);
      }
      var playTimeout;

      $("#duration").on("play", function(e) {
        playTimeout = setTimeout(function() {
          $("duration").pause();
          $("duration").setCurrentTime(0); // Restarts video
        }, 180000); // 3 minutes in ms
      });
      $("#duration").on("pause", function(e) {
        clearTimeout(playTimeout);
      });
      //playNote("502",10000);
    </script>
    <script>
      $(document).ready(function() {
        $(".list_voice li h3").click(function() {
          $("#pause").trigger("click");
          $('#canvas').show();
          $(this).parents('.list_voice').find('h3').removeClass("intro");
          $(this).addClass("intro");
          $("#fre").val($(this).attr('data-fre'));
          $(".fre_number_text").text($(this).attr('data-fre') + 'Hz');
          $("#pause").hide();
          $("#play").show();
        });

        $("#play").click(function() {
          $(this).hide();
          $("#pause").show();
          // $(this).onload = init();
          $('#canvas').show();
          // displayTime();
          // $("#back_bg").addClass("white_bg_active");
        });
        $("#pause").click(function() {
          clearTimeout(timeout);
          //resume();
          stopNote();
          $(this).hide();
          $("#play").show();
          $('#canvas').hide();
          $("#back_bg").removeClass("white_bg_active");
        });
        $("#stopBtn").click(function() {
          clearTimeout(timeout);

          $("#fre").val('<?php echo $frequencies[0]; ?>');
          $(".fre_number_text").text('<?php echo $frequencies[0]; ?>Hz');
          $('.list_voice').find('li').find('h3').removeClass("intro");
          $('.list_voice').find('li:first').find('h3').addClass("intro");
          $("#play").show();
          $("#pause").hide();
          $("#back_bg").removeClass("white_bg_active");
          stopNote();
          // $(this).onload = init().hide();
          //$("#pause").trigger("click");
          return false;
        });
      });
    </script>
    <script type="text/javascript">
      function showAxes(ctx, axes) {
        var width = ctx.canvas.width;
        var height = ctx.canvas.height;
        var xMin = 0;
        ctx.beginPath();
        ctx.strokeStyle = "rgb(60,179,113)";
        // X-Axis
        ctx.moveTo(xMin, height / 2);
        ctx.lineTo(width, height / 2);
        // Y-Axis
        ctx.moveTo(width / 2, 0);
        ctx.lineTo(width / 0, height);
        // Starting line
        ctx.moveTo(0, 0);
        ctx.lineTo(0, height);
        ctx.stroke();
      }
      function drawPoint(ctx, y) {
        var radius = 0;
        ctx.beginPath();
        // Hold x constant at 4 so the point only moves up and down.
        ctx.arc(4, y, radius, 0, 2 * Math.PI, false);
        ctx.fillStyle = 'white';
        ctx.fill();
        ctx.lineWidth = 3;
        ctx.stroke();
      }
      function plotSine(ctx, xOffset, yOffset) {
        var width = ctx.canvas.width;
        var height = ctx.canvas.height;
        var scale = 20;
        ctx.beginPath();
        ctx.lineWidth = 2;
        ctx.strokeStyle = "rgb(60,179,113)";
        // console.log("Drawing point...");
        // drawPoint(ctx, yOffset+step);
        var x = 4;
        var y = 0;
        var amplitude = 40;
        var frequency = 20;
        //ctx.moveTo(x, y);
        ctx.moveTo(x, 50);
        while (x < width) {
          y = height / 2 + amplitude * Math.sin((x + xOffset) / frequency);
          ctx.lineTo(x, y);
          x++;
          // console.log("x="+x+" y="+y);
        }
        ctx.stroke();
        ctx.save();
        //console.log("Drawing point at y=" + y);
        drawPoint(ctx, y);
        ctx.stroke();
        ctx.restore();
      }

      function draw() {
        var canvas = document.getElementById("canvas");
        var context = canvas.getContext("2d");
        context.clearRect(0, 0, 500, 500);
        showAxes(context);
        context.save();
        plotSine(context, step, 50);
        context.restore();
        step += 4;
        window.requestAnimationFrame(draw);
      }
      function spirograph() {
        var canvas2 = document.getElementById("canvas2");
        var context = canvas2.getContext("2d");
        showAxes(context);
        context.save();
        // var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        var step = 4;
        for (var i = -4; i < canvas.height; i += step) {
          // context.putImageData(imageData, 0, 0);
          plotSine(context, i, 54 + i);
        }
      }
      function init() {
        window.requestAnimationFrame(draw);
        spirograph();
      }
      var step = -4;
      // Original JavaScript code by Chirp Internet: chirpinternet.eu
      // Please acknowledge use of this code by including this header.
      function SoundPlayer(audioContext, filterNode) {
        this.audioCtx = audioContext;
        this.gainNode = this.audioCtx.createGain();
        if (filterNode) {
          // run output through extra filter (already connected to audioContext)
          this.gainNode.connect(filterNode);
        } else {
          this.gainNode.connect(this.audioCtx.destination);
        }
        this.oscillator = null;
      }

      SoundPlayer.prototype.setFrequency = function(val, when) {
        if (this.oscillator !== null) {
          if (when) {
            this.oscillator.frequency.setValueAtTime(val, this.audioCtx.currentTime + when);
          } else {
            this.oscillator.frequency.setValueAtTime(val, this.audioCtx.currentTime);
          }
        }
        return this;
      };

      SoundPlayer.prototype.setVolume = function(val, when) {
        if (when) {
          this.gainNode.gain.exponentialRampToValueAtTime(val, this.audioCtx.currentTime + when);
        } else {
          this.gainNode.gain.setValueAtTime(val, this.audioCtx.currentTime);
        }
        return this;
      };
      SoundPlayer.prototype.setWaveType = function(waveType) {
        this.oscillator.type = waveType;
        return this;
      };
      SoundPlayer.prototype.play = function(freq, vol, wave, when) {
        this.oscillator = this.audioCtx.createOscillator();
        this.oscillator.connect(this.gainNode);
        this.setFrequency(freq);
        if (wave) {
          this.setWaveType(wave);
        }
        this.setVolume(1 / 1000);
        if (when) {
          this.setVolume(1 / 1000, when - 0.02);
          this.oscillator.start(when - 0.02);
          this.setVolume(vol, when);
        } else {
          this.oscillator.start();
          this.setVolume(vol, 0.02);
        }
        return this;
      };

      SoundPlayer.prototype.stop = function(when) {
        if (when) {
          this.gainNode.gain.setTargetAtTime(1 / 1000, this.audioCtx.currentTime + when - 0.05, 0.02);
          this.oscillator.stop(this.audioCtx.currentTime + when);
        } else {
          this.gainNode.gain.setTargetAtTime(1 / 1000, this.audioCtx.currentTime, 0.02);
          this.oscillator.stop(this.audioCtx.currentTime + 0.05);
        }
        return this;
      };
    </script>
  <?php } ?>
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
          $("#search").val(ui.item.value);
          if (ui.item.value != 'No Frequency found') {
            var category = ui.item.category;

            if (category == '') category = 1;
            urlset = "frequencies.php?category=" + category + "&id=" + ui.item.key;
            window.location.href = urlset;
          }
        }
      });

      $(".repeate").click(function() {
        if ($(this).hasClass('off') == true) {
          $(this).removeClass('off');
          $(this).addClass('on');
          $('.repeate').attr('data-status', 1);
        } else if ($(this).hasClass('on') == true) {
          $(this).removeClass('on');
          $(this).addClass('once');
          $('.repeate').attr('data-status', 2);
        } else if ($(this).hasClass('once') == true) {
          $(this).removeClass('once');
          $(this).addClass('off');
          $('.repeate').attr('data-status', 0);
        }
        return false;
      });
      $(".list_voice i.bar").click(function() {
        var ele = $(this);
        // alert(1);
        // return false;
      });
      $(".shuffle_btn").click(function() {
        var ele = $(this);
        var shuffle = ele.attr('data-shuffle');
        if (shuffle == 0) {
          var is_shuffle = 1;
          ele.removeClass('off');
          ele.addClass('on');
        } else {
          var is_shuffle = 0;
          ele.removeClass('on');
          ele.addClass('off');
        }
        ele.attr('data-shuffle', is_shuffle);
      });
    });
    function random_mp3(type = null) {
      var random_value = [];
      $('.list_voice li').each(function() {
        if (type == 'rife') {
          if ($(this).find('h3').hasClass("intro")) {} else {
            var value = $(this).attr("data-random")
            random_value.push(value);
          }
        } else {
          if ($(this).find('a').hasClass("intro")) {} else {
            var value = $(this).attr("data-random")
            random_value.push(value);
          }
        }
      });
      var randoms = Math.floor(Math.random() * random_value.length);
      // console.log(random_value[randoms]);
      return random_value[randoms];
    }
    $(document).bind("contextmenu", function(e) {
      return false;
    });
    document.onkeydown = function(e) {
      if (e.keyCode == 123) {
        return false;
      }
      if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
        return false;
      }
      if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
        return false;
      }
      if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
        return false;
      }

      if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
        return false;
      }
    }
    $(document).bind('keydown', function(e) {
      if (e.ctrlKey && (e.which == 83)) {
        e.preventDefault();
        return false;
      }
    });
    function play() {
      audio.src = $(this).next('li').find('a').attr('href');
      audio.play();
    }
  </script>

</body>
</html>
<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script> -->
<script src="js/context-menu.js"></script>
<link href="css/context-menu.css" rel="stylesheet">

<script>
  $(document).ready(function() {
    var tableContextMenu = new ContextMenu("context-menu-items", menuItemClickListener);
  });

  function menuItemClickListener(menu_item, parent) {}
  $(document).on('click', '.playlist_add', function() {
    // alert(111);
    var ele = $(this);
    var playlist_name = $('#playlist_name').val();
    $('#playlist_form').find('span.error').addClass('hide');
    if (playlist_name == '') {
      $('#playlist_form').find('span.error').removeClass('hide');
      return false;
    } else {
      ele.prop('disabled', true);
      $.ajax({
        url: 'post.php',
        type: 'POST',
        data: {
          playlist: 1,
          method: 'add',
          playlist_name: playlist_name
        },
        dataType: 'json',
        success: function(res) {
          // console.log(res);
          ele.prop('disabled', false);
          location.reload();
        }
      });
    }
  });

  $(document).on('click', '.add_to_playlist', function() {
    // alert(111);
    var ele = $(this);
    var playlist_id = ele.attr('data-id');
    var frequency_id = ele.attr('data-frequency-id');
    // alert(playlist_id +'--'+ frequency_id);
    if (playlist_id && frequency_id) {
      $.ajax({
        url: 'post.php',
        type: 'POST',
        data: {
          playlist: 1,
          method: 'add_frequency',
          playlist_id: playlist_id,
          frequency_id: frequency_id
        },
        dataType: 'json',
        success: function(res) {
          var msg = res.msg;
          $('#ResponseModal .response-msg').html(msg);
          $('#ResponseModal').modal('show');
        }
      });
    }
  });
  $(document).on('click', '.context-menu', function() {
    var ele = $(this);
    var frequency_id = ele.attr('data-row-id');
    // alert(frequency_id);
    if (frequency_id) {
      $('.add_to_playlist').attr('data-frequency-id', frequency_id);
    }
  });

</script>

<div class="context-menu-container" id="context-menu-items">
  <ul class="dropdown">
    <label class="dropdown-toggle dropdown" data-toggle="dropdown" aria-expanded="false" data-value="1">Add to Playlist</label>
    <ul class="dropdown-menu">
      <li data-toggle="modal" data-target="#PlaylistModal">Create Playlist</li>
      <?php foreach ($playlists as $v) {
        echo '<li class="add_to_playlist" data-id="' . $v->id . '">' . $v->name . '</li>';
      } ?>
    </ul>
    <!-- <li data-value="2" class="favorite">Add to Favorites</li> -->
    <!-- <li data-value="3">Redownload</li> -->
  </ul>
</div>

<div class="modal fade" id="PlaylistModal" tabindex="-1" role="dialog" aria-labelledby="PlaylistModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PlaylistModalLabel">Create New Playlist</h5>
      </div>
      <div class="modal-body">
        <div id="playlist_form">
          <div class="form-group">
            <label for="playlist_name" class="col-form-label">Playlist Name:</label>
            <input type="text" name="playlist_name" class="form-control" id="playlist_name">
            <span class="error hide">Please enter playlist name</span>
          </div>
    </div>
        <p style="color:#059f83">*You will have to again add frequency after creating playlist.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary playlist_add">Create</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ResponseModal" tabindex="-1" role="dialog" aria-labelledby="ResponseModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      </div>
      <div class="modal-body">
        <div class="response-msg"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


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
