<div class="col-sm-12 col-md-2">
    
    <div class="check_listing" >
    <!-- <h5><b>Filter by Category</b></h5> -->
         <button type="button" class="btn-link collapsed btndrop" data-toggle="collapse" data-target="#demobtn"><h5><b>Filter by Category</b></h5></button>
<div id="demobtn">
  <?
  // print_r($SUBCATEGORIES);
  // exit;
  //foreach($SUBCATEGORIES as $v){ 
    
  //	if($v['categoryId']==2){		
  //		$class=($_GET['subtype'] == $v['id']) ? 'active' : '';
  //   		echo '<a class="'.$class.'" href="frequencies.php?type=quantum&subtype=master">Master</a>';
  //	}
  //}
  ?>
  <?php
  if (strpos($_SERVER['REQUEST_URI'], 'starter-frequencies') !== false) {
    $free = 1;
  }
  echo '<div class="btn_rife"><a class="' . ($free == 1 ? 'active' : '') . '" href="starter-frequencies.php">Starter</a></div>';
  echo '<div class="btn_rife"><a class="' . ($_GET['category'] == '1' && $free != 1 ? 'active' : '') . '" href="frequencies.php">Rife</a></div>';
  echo '<div class="btn_rife"><a class="' . (($_GET['category'] == '2' && $_GET['subcategory'] == 1) ? 'active' : '') . '" href="frequencies.php?category=2&subcategory=1">Master</a>';

  foreach ($CATEGORIES as $kc => $category) {
    $class = ($_GET['category'] == $kc) ? 'active' : '';
    if ($kc == 1) {
      // $class = ($_GET['category'] == $kc && $_SERVER['REQUEST_URI'] != '/starter-frequencies.php') ? 'active' : '';
      // echo '<div class="btn_rife"> <a class="' . $class . '" href="frequencies.php?category=' . $kc . '">' . $category . '</a>';
      // echo '<a class="'.(($_GET['category'] == '2' && $_GET['subcategory'] == 1) ? 'active' : '').'" href="frequencies.php?category=2&subcategory=1">Master</a>';
    } elseif ($kc == 2) {
      echo '<button type="button" class="btn_new btn-link collapsed" data-toggle="collapse" data-target="#demo">' . $category . '</button>';
      $expand_collapse = ($_GET['category'] == '2' && $_GET['subcategory'] != 1) ? 'expand' : 'collapse';
      echo '<div id="demo" class="' . $expand_collapse . '">';
    } elseif ($kc == 3) {
      echo '<button type="button" class="btn_new btn-link collapsed" data-toggle="collapse" data-target="#demo1">' . $category . '</button>';
      $expand_collapse = ($_GET['category'] == '3') ? 'expand' : 'collapse';
      echo '<div id="demo1" class="' . $expand_collapse . '">';
    } elseif ($kc == 4) {
      echo '<button type="button" class="btn_new btn-link collapsed" data-toggle="collapse" data-target="#demo2">' . $category . '</button>';
      $expand_collapse = ($_GET['category'] == '4') ? 'expand' : 'collapse';
      echo '<div id="demo2" class="' . $expand_collapse . '">';
    }


  ?>
    <?php /*?><!--<button type="button" class="btn_new btn-link collapsed" data-toggle="collapse" data-target="#demo"> Quantum</button>
  <div id="demo" class="<?php echo ($_GET['category'] == '2') ? 'expand' : 'collapse'; ?>">--><?php */ ?>
  <?php
    // print_r($_SESSION);
    // die;
    foreach ($SUBCATEGORIES as $v) {
      if ($v->categoryId != 1 && $v->categoryId == $kc) {
        $class = ($_GET['subcategory'] == $v->id) ? 'active' : '';
        // if ($v->categoryId == 3 && !in_array($v->categoryId, $_SESSION['category_ids'])) {
        //   echo '<a class="' . $class . '" href="highqpayment.php">' . $v->name . '</a>';
        // } else {
        //   echo '<a class="' . $class . '" href="frequencies.php?category=' . $v->categoryId . '&subcategory=' . $v->id . '">' . $v->name . '</a>';
        // }
        if ($v->id != 1) {
          echo '<a class="' . $class . '" href="frequencies.php?category=' . $v->categoryId . '&subcategory=' . $v->id . '">' . $v->name . '</a>';
        }
      }
    }
    echo '</div>';
  }
  ?>
<div class="mt-5">
  <h5><b> Filter by Membership</b></h5>

  <div class="form-check free">
                 <input class="form-check-input" type="checkbox" value="yes" id="defaultCheck1 "
                 <?php if (strpos($_SERVER['REQUEST_URI'], 'starter-frequencies')) echo 'checked="checked"';?>>
                 <label class="form-check-label" for="defaultCheck1"  style="font-weight: normal">
                 Free
                </label>
             </div>
            <div class="form-check premium">
                 <input class="form-check-input" type="checkbox" value="yes" id="defaultCheck1"
                 <?php if(!strpos($_SERVER['REQUEST_URI'], 'starter-frequencies') && ($_GET['category'] == 1 || $_GET['category'] == 2 || $_GET['category'] == 3)) echo 'checked="checked"';?>>
                 <label class="form-check-label" for="defaultCheck1"style="font-weight: normal">
                 Premium
                </label>
             </div>
           <div class="form-check inner_circle">
                 <input class="form-check-input" type="checkbox" value="yes" id="defaultCheck1"
                 <?php if(strpos($_SERVER['REQUEST_URI'], 'frequencies') && $_GET['category'] == 4) echo 'checked="checked"';?>>
                 <label class="form-check-label" for="defaultCheck1" style="font-weight: normal">
                 Inner Circle 
                </label>
            </div>
  
</div>

<div>
    <h5 style=" display: inline-block;"><b><a href="favourites.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], 'favourites') !== false ? 'active' : 'text-dark');?>">Favourites</a></b></h5>
</div>
<div>
  <h5><b>Playlists</b></h5>
  <?php
  if (isset($_SESSION['id'])) {
    $url = 'https://apiadmin.qienergy.ai/api/getplaylist?userid=' . $_SESSION['id'];
    $post_data = '';
    $res = curl_post($url, $post_data, $header);
    // print_r($res);die;
    $playlist_res = json_decode(($res['res']));
    if ($playlist_res->playlist->rsp_msg == '') {
      $playlists = $playlist_res->playlist;
    }
    // print_r($playlists);die;  
    foreach ($playlists as $v) {
      echo '<div class="btn_rife"><a href="playlists.php?id=' . $v->id . '"> ' . $v->name . ' </a></div>';
      // echo '<div class="btn_rife"><a href="#"> '.$v->name.' </a></div>';
    }
  }
  ?>
  </div>

</div>  
</div>

 <a href="https://qilifestore.com/collections/qi-coils/products/qi-coil-max-transformation-system" target="_blank" class="ad-mob-image">
    <img src="https://members.qicoil.com/images/qc-max-300x250-admob.jpg" alt="qi-coil-max-transformation-system" width="100%"/>
</a>

</div>



<script>
  $('.free').click(function() {
    window.location.href = 'https://members.qicoil.com/starter-frequencies.php';
    return false;
  });


  $('.premium').click(function() {
    window.location.href = 'https://members.qicoil.com/frequencies.php?category=3&subcategory=8';
    return false;
  });

  $('.inner_circle').click(function() {
    window.location.href = 'https://members.qicoil.com/frequencies.php?category=4&subcategory=31';
    return false;
  });
</script>