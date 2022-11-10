<?php
include('array.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Thankyou Registration - <?php echo $GLOBALS['SITENAME']?></title>
  <?php include 'head.php'; ?>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <style>
    .error {
      color: red;

    }

    .err {
      color: red;
    }

    .success {
      color: green;
    }

    .spinner-border {
      display: none;
    }

    h3 {
      text-align: center;
    }
	
	#overlay {
	  text-align: center;
	}

	.load_img {
		width: 80px;
	}
  </style>
<!-- Event snippet for Qi Coil Webapp Sign-up conversion page -->
<script>
  gtag('event', 'conversion', {'send_to': 'AW-989920311/BUgnCNz0_IAYELf4g9gD'});
</script>
</head> 

<body>
  <?php include 'header.php'; ?>

  <section id="payment">
    <div class="container">
      <div class="row">

        <?php //if (in_array(1, $_SESSION['category_ids'])) { ?>
          <div class="col-md-12">
            <div>
            <h3> Registration Successful </h3>
			  <div id="overlay">
		         <img src="images/gearloading.gif" alt="Loading" class="load_img" />
         		 <span class="loading">Loading...</span>
    		  </div>
    		  <div id="container" style="display:none"></div>
            </div>
          </div>
        <?php //} else { ?>
</body>

</html>
<script type="text/javascript">
$(document).ready(function () {
    // Handler for .ready() called.
    window.setTimeout(function () {
        location.href = "https://members.qicoil.com/starter-frequencies.php";
    }, 5000);
});
</script>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1996170193850834');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1996170193850834&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->