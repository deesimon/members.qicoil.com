<?php

error_reporting(0);
include('array.php');
session_start();
if (isset($_SESSION['email'])) {
//  header('Location:index.php');
//  exit;
}
// print_r($_SESSION);
// die;

include('constants.php');
include('functions.php');

if (!empty($_POST)) {

$update_plan = 'https://apiadmin.qienergy.ai/api/frequencies?category=2';
		$response = 'curl_post'($update_plan, '', $header);
		$data = json_decode($response['res']);
		foreach ($data->frequencies as $v) {
			$album_ids[] = $v->id;
			$subcategory_ids[] = $v->subCategoryId;
		}

  if (isset($_SESSION['email'])) {

	$userid = $_SESSION['id'];
    $email = $_SESSION['email'];

  } else {

  	$userid = $_SESSION['id'];
    $name = $_POST['bname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

	$header = array('Content-Type: application/x-www-form-urlencoded');
    $data = array('name' => $name, 'email' => $email, 'password' => $password);
    $post_data = http_build_query($data);
    $url = 'https://apiadmin.qienergy.ai/api/register';
    //  print_r($data);//die;
    $res = curl_post($url, $post_data, $header);
  //print_r($res['res']);die;

    $json = json_decode($res['res']);
//	print_r($json);die;
	if ($json->user[0]->fetch_flag == 1) {
		$userid = $json->user[0]->id;
		$_SESSION['id'] = $userid;
		$_SESSION['email'] = $email;
		$_SESSION['password'] = $password;
		$_SESSION['name'] = $name;
		$_SESSION['category_ids'] = array(2);
		$_SESSION['subcategory_ids'] = $subcategory_ids;
		$_SESSION['album_ids'] = $album_ids;
	}else {
		$return = array('success' => false, 'msg' => $json->user[0]->rsp_msg);
	    echo json_encode($return);
	    die;
	}
  }

  $return = [];

  $name = $_POST['bname'];
  $cardNumber = $_POST['cardnumber'];
  $cvv = $_POST['cvc'];
  $start = explode("/", $_POST['expirydate']);
  $exYear = $start[1];
  $exMonth = $start[0];
  $cardDetails = array('cardNumber' => $cardNumber, 'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv);
  $addAmount = $_POST['add_amount'];
  if ($addAmount == '27') {
    $prduct_id = PRODUCT_QUANTUM_MONTHLY;
    $planType = 'monthly';
  } elseif ($addAmount == '197') {
    $prduct_id = PRODUCT_QUANTUM_YEARLY;
    $planType = 'yearly';
  }
// print_r($name);
// print_r($email);
// print_r($cardDetails);
// print_r($prduct_id);
//   die;
  if (empty($name) || empty($cardNumber) || empty($cvv)) {
    $return = array('success' => false, 'msg' => 'Please Enter Details');
  } else {
    $subscription_res = subscription($name, $email, $cardDetails, $prduct_id);
    $return = $subscription_res['return'];
  }
  //print_r($subscription_res);
  //die;
  if (!empty($subscription_res['description']) && !empty($subscription_res['payStatus'])) {
    $url = 'https://apiadmin.qienergy.ai/payment/add';
    $header = array('Content-Type: application/x-www-form-urlencoded');
    $add_data = array('userid' => $userid, 'name' => $name, 'amount' => $addAmount, 'payStatus' => $subscription_res['payStatus'], 'payType' => 1, 'cardNumber' => $cardNumber, 'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv, 'transactionId' => $subscription_res['transactionId'], 'balanceTransaction' => $subscription_res['balanceTransaction'], 'description' => $subscription_res['description'], 'productType' => 'quantum', 'planType' => $planType, 'category_id' => 2);
	$_SESSION['category_ids'] = array_merge($_SESSION['category_ids'], array(2));
		$_SESSION['subcategory_ids'] = array_merge($_SESSION['subcategory_ids'], $subcategory_ids);
	$_SESSION['album_ids'] = array_merge($_SESSION['album_ids'], $album_ids);

    // print_r($add_data);
    // die;
    $post_data = http_build_query($add_data);
    $res = curl_post($url, $post_data, $header);
    //print_r($res);die;
  }

  echo json_encode($return);
  die;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Qi Coil</title>
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
  </style>
</head>

<body>
  <?php include 'header.php'; ?>

  <section id="payment">
    <div class="container">
      <div class="row">

        <?php if (in_array(2, $_SESSION['category_ids'])) { ?>
          <div class="col-md-8 col-md-offset-3">
            <div class="col-md-9" id='d1'>
              <h3> You alredy purchased this quantum subscription.</h3>
            </div>
          </div>
        <?php } else { ?>
          <div class="col-md-8 col-md-offset-3 ss1">

            <div class="col-md-9" id='d1'>
              <h3> Quantum Frequencies </h3>
              <!-- <label class="save_m q_color">SAVE &nbsp;50%</label> -->
              <div class="btn_box d-flex q_border">
                <button type="button" class="btn  col-md-6 b1 year_mo q_color">Monthly</button>
                <button type="button" class="btn  col-md-6 b2 year_mo">Yearly
                <span class="save">Save Up To $127</span>
                </button>
              </div>


              <div class="price_detail p-4 col-md-12 q_color">
                <h3>$<span class="price1">27 / mo</span> </h3>
                <p><b><span class="price2"></span></b></p>
                <p>
                <h4>60 Day Money Back Guarantee Cancel Anytime</h4>
                </p>
                <p>
                  <smal>Prices in USD. </small>
                </p>
              </div>
              <!-- <div class="top_border"> </div> -->


            </div>


            <div class="col-md-9 mt-3 mb-4">

              <form action="" method="POST" id="form">
                <input type="hidden" name="add_amount" id="add_amount" value=" 27 ">
                <input type="text" class="form-control input q_boder1" placeholder="Name" name="bname">
                <?php if (!isset($_SESSION['email'])) { ?>
                  <input type="email" class="form-control input" placeholder="Email" name="email">
                  <input type="password" class="form-control input" placeholder="Password" name="password">
                <?php } ?>
                <input type="text" class="form-control input q_boder1" placeholder="Card Number" name="cardnumber"  maxlength="16">


                <div class="flex-container">

                  <div class="flex-child">
                 <label for="expirationdate">Expiry date (MM / YY)</label>
            <input id="expirationdate"   name="expirydate" class="form-control input"  placeholder="MM / YY" type="text" pattern="[0-9]*" inputmode="numeric">
                  </div>

                  <div class="flex-child">
                    <label>CV CODE</label>
                    <input type="tel" class="form-control input q_boder1" placeholder="CVC" name="cvc" maxlength="3">
                  </div>
                </div>


                <div class="flex-container">
                  <p id="payment-error"></p>
                </div>
                <!-- <span class="payment_btn col-md-12 mb-3 p1 pri q_color" name="amount"> Total = $99.99 </span> -->
                <button type="button" class="btn payment_btn col-md-12 q_color" value="submit" id="submitbtn">
                Subscribe <div class="spinner-border" role="status"></div></button>



                <div class="pmpro_submit second "style="text-align: center";>
					<!-- <div class="trusted-badges-container paypal" style="display:none;">
				<img src="/wp-content/uploads/2022/03/paypal-logo-transparent.png" width="200">
			</div> -->
			<div class="trusted-badges-container creditcard">
				<img src="images/trusted-badge-1.png" width="300" class="creditcard_badges">
			</div>
				<div class="trusted-badges-container">
			<img src="images/trusted-badge-1.jpg" width="300">
		</div>
		<p class="checkout-statement">By placing your order you agree to the terms of use <a class="checkout-link" rel="noopener" style="color: #a7a7a7;text-decoration: underline;" href="https://www.qienergy.ai/terms-of-use/" target="_blank">here</a></p>
	 <!-- <span id="pmpro_paypalexpress_checkout" style="display: none;">
			<input type="hidden" name="submit-checkout" value="1"><input type="hidden" name="javascriptok" value="1">
			<input type="image" class="pmpro_btn-submit-checkout" value="Check Out with PayPal »" src="https://www.qienergy.ai/wp-content/uploads/2022/03/paypay-activate-button.png">
		</span>  -->
  </div>




  <hr class="between-secure-checkout-testimonial">
  <div class="single-testimonial-before-checkout">
	<div class="single-testimonial-image"><img src="images/Kevin-Creegan-2.jpg" alt="Hallie Cowan"></div>
	<div class="single-testimonial-content">
		<p>"<b>This is incredibly powerful! I spent with these energy signatures twice for 2-3 hours and it gave a big surge of energy. I felt it in my body.</b> This feeling of a surge of energy was so strong that I had to urgently do a strength workout!"</p>
		<span><b>Kevin Creegan,</b> Qi Energy User</span>
	</div>
</div>





              </form>
            </div>
          </div>

        <?php } ?>
      </div>
    </div>
  </section>




  <div class="checkout-faqs">
  <h4>I can’t use a credit card to pay for the membership, can you bill me?</h4>
<p>We accept only credit or debit card or PayPal payments for subscription fees.</p>
<h4>Where can I check my membership account?</h4>
<p>Simply go to this page: <a href="https://qienergy.ai/my-account">https://qienergy.ai/my-account</a></p>
<h4>How can I obtain a copy of my receipt for payment of the subscription fee?</h4>
<p>A receipt for payment for the subscription fee was automatically sent to the email address provided during registration. Sometimes emails may land in spam folders.</p>
<h4>Can I cancel my membership anytime?</h4>
<p>Yes, there are no commitments, so you can cancel anytime. But we encourage you to use our energy signatures daily for a full 60 days to be certain that it’s not for you before you cancel. Currently, because this technology is still in its infancy, all our membership plans are at seriously discounted prices. If you cancel and decide to join back later (when we have more energy signatures available), the plans will at much higher prices.</p>
<h4>How does your 60 day money back guarantee work?</h4>
<p>We’re so confident that QiEnergy.Ai will give you back the incredible value that you have 60 days to try the technology before you decide to keep it. Most people experience amazing benefits within 7 days but If you are not satisfied in any way within a full 60 days, we will give you a full refund – no questions asked.</p>
<h4>Can I transfer my subscription to someone else?</h4>
<p>No, the subscription is not transferable.</p>
<h4>Am I locked into any contracts?</h4>
<p>No, you can cancel your membership at any time.</p>
<h4>Can I upgrade my plan later after I activate my account today?</h4>
<p>Yes, you can always upgrade or downgrade your plan at any time.</p>
</div>








  <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
  <script>

window.onload = function () {

const expirationdate = document.getElementById('expirationdate');

let cctype = null;

}

var expirationdate_mask = new IMask(expirationdate, {
    mask: 'MM{/}YY',
    groups: {
        YY: new IMask.MaskedPattern.Group.Range([0, 99]),
        MM: new IMask.MaskedPattern.Group.Range([1, 12]),
    }
});

    $(document).ready(function() {


      $('.year_mo').click(function() {

        $(this).addClass("q_color");
        $(this).siblings('button').removeClass("q_color");

        if ($(this).hasClass('b2')) {
          var amount = '197';
          var p1 = '16 / mo';
          var p2 = '$197 billed annually';
          var btn_text = 'Subscribe';
          var btn_price = 'Total = $197';
        } else {
          var amount = '27';
          var p1 = '27 / mo';
           var p2 = '';
          var btn_text = 'Subscribe';
          var btn_price = 'Total = $27';
        }

        $('.price1').html(p1);
        $('.price2').html(p2);
        $('.payment_btn').html(btn_text);
        $('#add_amount').val(amount);
        $('.pri').html(btn_price);


      });

      ///show///
      // $('.b1').click(function(){
      // $('#d1').show();
      // $('#d2').hide();
      // $(this).addClass("toggle_btn1");
      // $('.b2').removeClass("toggle_btn1");

      // });

      // ///show///
      // $('.b2').click(function(){
      // $('#d2').show();
      // $('#d1').hide();
      // $(this).addClass("toggle_btn1");
      // $('.b1').removeClass("toggle_btn1");

      // });



      $('#form').validate({
        rules: {
          bname: {
            required: true
          },
          cardnumber: {
            required: true,
            minlength: 16,
          },
          email: {
        required: true
      },

      password: {
        required: true
      },
      expirydate:{
          required: true
         },

          cvc: {
            required: true,
            minlength: 3,
          },



        },
        messages: {
         // name: 'Please enter Name.',

         bname: {
            required: 'Please Enter name.',

          },
          cardnumber: {
            required: 'Please Enter Card number.',
            rangelength: 'Card Number should be 16 digit number.'
          },
          email: {
            required: 'Please Enter Email id.',
            rangelength: '.'
          },

        password: {
            required: 'Please Enter password.',
            rangelength: '.'
          },

          expirydate:{
          required: 'Please Enter  month and year.',
            rangelength: '.'
         },

          cvc: {
            required: 'Please Enter CVC Number.',
            minlength: 'CVC should be 3 digit number.'
          },


        },
        submitHandler: function(form) {
          form.submit();
        }
      });

      $("#submitbtn").click(function(event) {
        if($('#form').valid() == true){
        var ele = $(this);
        $('#payment-error').removeClass('error').removeClass('success').html("");
        ele.prop('disabled', true);
        var data = $("#form").serializeArray();
        ele.find('.spinner-border').css('display', 'inline-block');

        $.ajax({
          url: "quantum_payment.php",
          type: "POST",
          data: data,
          dataType: 'json',
          success: function(res) {

            if (res.success == true) {
              $('#payment-error').addClass('success').html("Successfully payment");
              $("#form").find("input, textarea").val("");
              $("#start").val('<?php echo date('Y-m'); ?>');
              $("#add_amount").val('197');
			  var link = 'thankyou_payment.php';
			  window.location.assign(link);
            } else {
              $('#payment-error').addClass('error').html(res.msg);
            }
            ele.prop('disabled', false);
            ele.find('.spinner-border').hide();

          },
          error: function(xhr) {
            ele.prop('disabled', false);
            $('#payment-error').addClass('error').html("Something Wrong");
            //ele.removeClass('spinner-border');
            ele.find('.spinner-border').hide();
          },

        });


      }

      });

    });
  </script>
</body>

</html>
