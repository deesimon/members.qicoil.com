<?php
error_reporting(0);
include('array.php');
include('constants.php');
// session_start();

$id = $_GET['id'];
$userid = $_SESSION['id'];
// print_r($userid);die;

$frequency_ids = $header = array();
$url =GETPLAYLIST_URL.'?userid=' . $userid . '&playlist_id=' . $id;
$res = curl_post($url, '', $header);
  // print_r($res);die;
$response = json_decode($res['res']);
$playlist = $response->playlist;
$fetch_flag = $playlist->fetch_flag;
if ($fetch_flag != -1) {
$playlist_name = $playlist->name;
if (!empty($playlist->frequency_id)) {
  $mp3s_ids = explode(',', $playlist->frequency_id);

  $url=MP3_URL;
  $post_data = http_build_query(array("ids" => $mp3s_ids));
  $res = curl_post($url, $post_data, $header);
  $mp3_response = json_decode(($res['res']), true);
  // print_r($mp3_response);
  // die;

  foreach ($mp3_response as $v) {
    $frequency_ids[$v['frequency_id']] = $v['frequency_id'];
  }
  // print_r($frequency_ids);
  // die;

  $url=FREQUENCIES_URL;
  $post_data = http_build_query(array("ids" => $frequency_ids));
  $res = curl_post($url, $post_data, $header);
  $freq_response = json_decode($res['res']);
  $frequencies = $freq_response->frequencies;
  // print_r($frequencies);
  // die;
  foreach ($frequencies as $v) {
    $audio_folders[$v->id] = $v->audio_folder;
  }
  // print_r($audio_folders);
  // die;
  foreach ($mp3_response as $k => $v) {
    $mp3s[$k] = $v;
    $mp3s[$k]['audio_folder'] = $audio_folders[$v['frequency_id']];
  }
  // print_r($mp3s);
  // die;
  $first_mp3=FIRST_MP3_URL. $mp3s[0]['audio_folder'] . '/' . $mp3s[0]['filename'];
}
} else {
  $not_found = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Playlist - <?php echo $GLOBALS['SITENAME'] ?></title>
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

    #inner_detail .list_voice i.fa-close {
      background: transparent;
      color: red;
      cursor: pointer;
      padding: 0;
    }
  </style>

</head>

<body>
  <?php include 'header.php'; ?>
  <section id="inner_detail">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="col-xs-12 col-md-6 col-sm-6">
            <div class="col-xs-3 col-md-1"><a href="<?php echo (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'frequencies.php'); ?>"> <img src="images/left.png" class="left_aerrow_bg"> </a> </div>
            <?php if ($not_found == true) { ?>
              <div class="col-md-9">
                <h5>Playlist Not Found</h5>
              </div>
            <?php } else { ?>
              <div class="col-xs-5 col-md-9">
                <h5><?php echo $playlist_name; ?></h5>
              </div>
              <div class="col-xs-4 col-md-2">
                <a href="#" id="remove-playlist"> Delete <i class="fa fa-trash"></i>
                </a>
              </div>
            <?php } ?>
            </div>

          <div class="col-md-6 col-xs-12 p-0 stand">
            <div class="col-md-10 col-xs-12 p-0">
              <div class="play_box col-md-12">
                <div class="white_bg1 col-md-10 col-sm-10 offset-1" id="back_bg">
                  <div class="col-md-10 pt-5 button_left mt-3 col-xs-9">
                    <button type="button" class="stopbtn" id="stopBtn"><img src=" images/left_btn.png"></button>
                    <button type="button" class="plybtn" onClick="playNote()" id="play"> <img src="images/middle.png"></button>
                    <button type="button" id="pause"><img src="images/mute.png"></button>

                    <span class="repeate off" id="repeateBtn" data-status=0></span>
                    <span data-shuffle="0" class="shuffle_btn off"></span>
                    <div class="col-md-12 pt-3">
                      <audio id="sound">
                        <source src="<?php echo $first_mp3; ?>" type="audio/mpeg" />
                      </audio>
                      <label class="fre_number_text"><?php echo str_replace('.mp3', '', $mp3s[0]['filename']); ?></label>
                      <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                      </div>
                      <span id="duration"></span>
                      <p><img class="loading_fre hide" src="images/load.gif" width="120"></p>
                    </div>
                  </div>
                  <div class="col-md-2 col-xs-3 pt-5">
                    <div class="volume">
                      <div class="vol_up"><img src="images/ic_volume_up_24.png"></div>
                      <div class="vol_line">
                        <input type="range" orient="vertical" min="0" max="10" value="5" />
                      </div>
                      <div class="vol_stop"><img src="images/ic_volume_mute_.png"></div>
                    </div>
                  </div>
                  <canvas id="canvas" width="400"> </canvas>
                </div>
                <div class=" col-md-10 col-sm-10 offset-1 pp">
                  <ul class="list_voice">
                    <?php
                    $i = 1;
                    foreach ($mp3s as $v) {
                      //  print_r($v);die;
                      $href = "https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/" . $v['audio_folder'] . "/" . $v['filename'];
                    ?>
                      <li class="hz" data-random="<?php echo $i; ?>"> <a <?php echo ($i == 1 ? 'class="intro"' : ''); ?> href="<?php echo $href; ?>"> <?php echo str_replace('.mp3', '', $v['filename']); ?> </a>
                        <span class="pull-right" data-row-id="<?php echo $v['id'] ?>"><i class="fa fa-close rm_frequency"></i></span>
                      </li>
                    <?php $i++;
                    }
                    ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>

</body>

</html>

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

<script>
  $(document).ready(function() {

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

    $(".rm_frequency").click(function() {
      var ele = $(this);
      var frequency_id = ele.parent('span').attr('data-row-id');
      // alert(frequency_id);
      var playlist_id = '<?php echo $_GET['id']; ?>';
      // alert(playlist_id);
      if (frequency_id && playlist_id) {
        if (confirm("Are you sure you want to delete!")) {
          $.ajax({
            url: 'post.php',
            type: 'POST',
            data: {
              playlist: 1,
              method: 'remove_frequency',
              playlist_id: playlist_id,
              frequency_id: frequency_id
            },
            dataType: 'json',
            success: function(res) {
              console.log(res);
              if (res.success == true) {
                ele.parents('.hz').remove();
              } else {
                alert('Frequency not removed, something wrong');
              }
            }
          });
        }
      }
    });

    $("#remove-playlist").click(function(event) {
      event.preventDefault();
      var ele = $(this);
      var playlist_id = '<?php echo $_GET['id']; ?>';
      if (playlist_id) {
        if (confirm("Are you sure you want to delete playlist!")) {
          $.ajax({
            url: 'post.php',
            type: 'POST',
            data: {
              playlist: 1,
              method: 'remove_playlist',
              playlist_id: playlist_id,
            },
            dataType: 'json',
            success: function(res) {
              // console.log(res);
              if (res.success == true) {
                window.location.href = 'starter-frequencies.php';
              } else {
                alert('Playlist not removed, something wrong');
              }
            }
          });
        }
      }
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
</script>