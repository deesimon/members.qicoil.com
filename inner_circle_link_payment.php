<?php

error_reporting(0);
include('array.php');
// session_start();
if (!isset($_SESSION['email'])) {
  header('Location:login.php');
  exit;
}

include('constants.php');
include('functions.php');

if (!empty($_POST)) {

  $userid = $_SESSION['id'];
  $email = $_SESSION['email'];
  $return = [];

  $name = $_POST['bname'];
  $cardNumber = $_POST['cardnumber'];
  $cvv = $_POST['cvc'];
  $start = explode("-", $_POST['start']);
  $exYear = $start[0];
  $exMonth = $start[1];
  $cardDetails = array('cardNumber' => $cardNumber, 'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv);
  $addAmount = $_POST['add_amount'];
  if ($addAmount == '14.99') {
    $prduct_id = PRODUCT_QUANTUM_MONTHLY;
    $planType = 'monthly';
  } elseif ($addAmount == '99.99') {
    $prduct_id = PRODUCT_QUANTUM_YEARLY;
    $planType = 'yearly';
  }

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
    $header = array();
    $add_data = array('userid' => $userid, 'name' => $name, 'amount' => $addAmount, 'payStatus' => $subscription_res['payStatus'], 'payType' => 1, 'cardNumber' => $cardNumber,    'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv, 'transactionId' => $subscription_res['transactionId'], 'balanceTransaction' => $subscription_res['balanceTransaction'], 'description' => $subscription_res['description'], 'productType' => 'quantum', 'planType' => $planType);
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
<title>inner_circle_link_payment - Qi Coil WebApp (BETA) </title>
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
     .a{ font-size:17px; font-weight:bold; color:#059f83; text-decoration:underline;}
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

        <!-- <div class="col-md-10"> <img src="images/ic_highlight_of.png" style="float:right;"> </div> -->
        <div class="col-md-8 col-md-offset-2">

          <div class="col-md-12 text-center">
            <h3> Inner Circle Frequencies </h3>
          
           <a class="a" href="https://info.qicoil.com/inner-circle/">Apply For Inner Circle</a>

        </div>

</div>
</div>
      </div>
    </div>
  </section>
  <script>
    // $(document).ready(function() {


    //   $('.year_mo').click(function() {

    //     $(this).addClass("q_color");
    //     $(this).siblings('button').removeClass("q_color");

    //     if ($(this).hasClass('b2')) {
    //       var p1 = '14.99';
    //       var p2 = '14.99 billed monthly';
    //       var btn_text = 'Purchase Month';
    //       var btn_price = 'Total = $14.99';
    //     } else {
    //       var p1 = '8.25 / mo';
    //       var p2 = '99.99 billed annually';
    //       var btn_text = 'Purchase Year';
    //       var btn_price = 'Total = $99.99';
    //     }
    //     $('.price1').html(p1);
    //     $('.price2').html(p2);
    //     $('.payment_btn').html(btn_text);
    //     $('#add_amount').val(p1);
    //     $('.pri').html(btn_price);


    //   });

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



    //   $('#form').validate({
    //     rules: {
    //       bname: {
    //         required: true
    //       },
    //       cardnumber: {
    //         required: true,
    //         minlength: 16,
    //       },
    //       cvc: {
    //         required: true,
    //         minlength: 3,
    //       },



    //     },
    //     messages: {
    //       name: 'Please enter Name.',
    //       start: {
    //         required: 'Please Select The Opton.',

    //       },
    //       cardnumber: {
    //         required: 'Please Enter Cardnumber.',
    //         rangelength: 'Card Number should be 16 digit number.'
    //       },

    //       cvc: {
    //         required: 'Please Enter cvc Number.',
    //         minlength: 'CVC should be 3 digit number.'
    //       },


    //     },
    //     submitHandler: function(form) {
    //       form.submit();
    //     }
    //   });

    //   $("#submitbtn").click(function(event) {

    //     var ele = $(this);
    //     $('#payment-error').removeClass('error').removeClass('success').html("");
    //     ele.prop('disabled', true);
    //     var data = $("#form").serializeArray();
    //     ele.find('.spinner-border').css('display', 'inline-block');

    //     $.ajax({
    //       url: "inner_circle_link_payment.php",
    //       type: "POST",
    //       data: data,
    //       dataType: 'json',
    //       success: function(res) {

    //         if (res.success == true) {
    //           $('#payment-error').addClass('success').html("Successfully payment");
    //           $("#form").find("input, textarea").val("");
    //           $("#start").val('<?php echo date('Y-m'); ?>');
    //           $("#add_amount").val('997');
    //         } else {
    //           $('#payment-error').addClass('error').html(res.msg);
    //         }
    //         ele.prop('disabled', false);
    //         ele.find('.spinner-border').hide();

    //       },
    //       error: function(xhr) {
    //         ele.prop('disabled', false);
    //         $('#payment-error').addClass('error').html("Something Wrong");
    //         //ele.removeClass('spinner-border');
    //         ele.find('.spinner-border').hide();
    //       },

    //     });




    //   });

    // });
  </script>
</body>

</html>