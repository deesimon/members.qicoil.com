<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(2);
include('array.php');
include('constants.php');

// session_start();

if (!isset($_SESSION['email'])) {
  header('Location:index.php');
  exit;
}
  $url=FAVORITE_URL;
  $post_data="";
  $header = array('Authorization: Bearer '.$_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
  $res = curl_post($url, $post_data, $header);
  $response = json_decode($res['res']);
 $favorite=$response->favorite;
//  print_r($favorite);die;

 if($favorite->fetch_flag==-1){
  $favorite=array();
 
 }else{
  $url=ALBUMS_URL;
  $post_data="";
  $header = array();
  $res = curl_post($url, $post_data, $header);
  $response1 = json_decode($res['res']);
  $albums=$response1->album;
  $album= array();
foreach($albums as $v)
{
  $album[$v->id] = $v;
}
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Favourite  - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
</head>
<?php include 'header.php'; ?>
<body id="con_listing">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 serch_box">
          <div class="row">
            <div class="col-md-5">
              <h3 class="main-title">Favourites</h3>
            </div>
            <div class="col-md-7 form-group has-search"> <span class="form-control-feedback"> </span>
            </div>
          </div>
   <div class="row response">
   <?php if(empty($favorite)){ 
             $favorite=array();
             echo"<center><h3>You Don't Have Any Favourites Frequencies</h3></center>";
           }else{ ?>
            <?php foreach ($favorite as $value) { ?>
              <div class="col-xs-6 col-md-3">
                <div class="new">
                  <a href="inner_frequencies.php?id=<?php echo $value->id . '&category=' .(empty($album[$value->id]->categoryId) ? 1 : $album[$value->id]->categoryId) ?>">
                    <img src="<?php echo (!empty($album[$value->id]->audio_folder) ? 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $album[$value->id]->audio_folder . '/' . $album[$value->id]->image : 'images/freaquecy.png'); ?>" width="126" height="126" />
                  </a>
                  <span data-album="<?php echo $value->id;?>" data-favorite="1" class="favorite yes"></span>
                  <div class="card-body">
                    <h5 class="card-title">
                      <b><?php echo (!empty($value->title) ? $value->title : $album[$value->id]->title);?></b>
                    </h5>
                  </div>
                </div>
              </div>
              <?php } ?>
              <?php } ?>  
          </div>
        </div>
	    </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script>
    $(document).ready(function() {
  });
  </script>
<?php
include('footer.php');
?>
</body>
</html>