<?php
include('array.php');
// session_start();
if(!empty($_GET['keyword'])){
	$post_data['keyword'] = $_GET['keyword'];

		if (!empty($_GET['ajax'])) {

		  $post_data['ajax'] = $_GET['ajax'];
		  $post_data['limit'] = $_GET['limit'];
		}

		$post_data = http_build_query($post_data);
	//	 print_r($post_data);
	//	 die;
		$url = 'https://apiadmin.qienergy.ai/api/frequencies';
		$res = curl_post($url . '?' . $post_data, '', $header);
		$response = json_decode($res['res']);
		$frequencies = $response->frequencies;
		// print_r($frequencies);
		// echo '<br><br><b>URL:</b> ' . $url . "\n\n<br/><br><b>POST DATA:</b> " . $post_data . "\n\n<br/><br><b>RES:</b>" . $res['res'];
		// exit;
		if (!empty($_GET['ajax'])) {

			echo $res['res'];
		  die;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Rife Frequencies List</title>
<?php include 'head.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
<style>
.form-control {
	max-width: 500px;
	margin: 0 auto;
}
/* .container {
	padding-top: 50px;
} */
</style>
</head>

<body>
	<?php include 'header.php'; ?>

	<div class="container">
	<div class="row">
		<div class="col-md-12 form-group"><!-- <span class="fa fa-search form-control-feedback"> </span> -->
		<form method="get" action="">

				<input type="text" name="keyword" id="search" placeholder="Search Frequencies" value="<?php echo $_GET['keyword'];?>" class="form-control ui-autocomplete-input">

		</form>
		</div>
		</div>

			<div class="row response">
				<?php
					foreach ($frequencies as $v) {
						$id = $v->id;
						if (empty($v->categoryId)) $category = 1;
						else $category = $v->categoryId;
						$title = $v->title;
						if ($v->image != '' && $v->audio_folder != '') {
							$img = 'https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' . $v->audio_folder . '/' . $v->image;
						}else {
							$img = 'images/freaquecy.png';
						}
					?>

				<div class="col-xs-6 col-md-3">
					<div class="new">
						<a href="inner_frequencies.php?id=<?php echo $id; ?>&amp;category=<?php echo $category; ?>"> <img src="<?php echo $img; ?>" height="126" width="126"> </a>
						<div class="card-body">
						  <h5 class="card-title"><b><?php echo $title;?> </b> </h5>
						  <!-- <p class="card-text">Rife Frequencies for Abdominal Cramps</p> -->
						</div>
					</div>
				</div>
				<?php }?>
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
				  img = 'https://members.qicoil.com/createimg.php?height=126&width=126&img=https://www.qicoilapi.ingeniusstudios.com/storage/app/public/uploads/' + ui.item.folder + '/' + ui.item.image;
				}
				$("#search").val(ui.item.value);
				$('.response').html('');
				$('.response').append('<div class="col-xs-6 col-md-4 "><div class="new"><a href="inner_frequencies.php?id=' + ui.item.key + categories + '"><img src="' + img + '"> </a><div class="card-body"><h5 class="card-title"><b>' + ui.item.value + '</b> </h5></div></div></div>');
				// $('.response').append('<div class="card_box"> <a href="inner_frequencies.php?id=' + ui.item.key + categories + '"> <img src="' + img + '"></a><span><img src="images/inner_heart.png"> </span> <div class="card-body"> <h5 class="card-title"><b>' + ui.item.value + '</b></h5> </div> </div>');
				$("#search").val('');
			  }
			}
		  });

		});
</script>

  <?php include 'footer.php'?>
</body>
</html>
