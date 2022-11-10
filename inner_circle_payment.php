<?php

error_reporting(0);
include('array.php');
session_start();
if (!isset($_SESSION['email'])) {
  header('Location:index.php');
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
  <title>qilife.io</title>
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

        <!-- <div class="col-md-10"> <img src="images/ic_highlight_of.png" style="float:right;"> </div> -->
        <div class="col-md-8 col-md-offset-3">

          <div class="col-md-9" id='d1'>
            <h3> Quantum Frequencies </h3>
            <label class="save_m q_color">SAVE &nbsp;50%</label>
            <div class="btn_box d-flex q_border">
              <button type="button" class="btn  col-md-6 b1 year_mo q_color">Yearly</button>
              <button type="button" class="btn  col-md-6 b2 year_mo">Monthly</button>
            </div>


            <div class="price_detail p-4 col-md-12 q_color">
              <h3>$<span class="price1">8.25 / mo</span> </h3>
              <p><b>$<span class="price2">99.99 billed annually</span>*</b></p>
              <p>
              <h4>cancel anytime *</h4>
              </p>
              <p>
                <smal>prices in USD. Subject to currency exchange rates.</small>
              </p>
            </div>
            <div class="top_border" style="border-color:#409f83;"> </div>


          </div>


          <div class="col-md-9 mt-3 mb-4">

            <form action="" method="POST" id="form">
              <input type="hidden" name="add_amount" id="add_amount" value="99.99">
              <input type="text" class="form-control input q_boder1" placeholder="Bill Name" name="bname">
              <input type="text" class="form-control input q_boder1" placeholder="Card NUmber" name="cardnumber">


              <div class="flex-container">

                <div class="flex-child">
                  <label for="start">Expiry date</label>
                  <input type="month" id="start" class="form-control input q_boder1" name="start" min="<?php echo date('Y-m'); ?>" value="<?php echo date('Y-m'); ?>" placeholder=">MM/YY/">
                </div>

                <div class="flex-child">
                  <label>CV CODE</label>
                  <input type="tel" class="form-control input q_boder1" placeholder="CVC" name="cvc">
                </div>

              </div>
              <div class="flex-container">
                <p id="payment-error"></p>
              </div>
              <span class="payment_btn col-md-12 mb-3 p1 pri q_color" name="amount"> Total = $99.99 </span>
              <button type="button" class="btn payment_btn col-md-12 q_color" value="submit" id="submitbtn">
                Purchase Year <div class="spinner-border" role="status"></div></button>

            </form>
          </div>


        </div>



      </div>
    </div>
  </section>
  <script>
    $(document).ready(function() {


      $('.year_mo').click(function() {

        $(this).addClass("q_color");
        $(this).siblings('button').removeClass("q_color");

        if ($(this).hasClass('b2')) {
          var p1 = '14.99';
          var p2 = '14.99 billed monthly';
          var btn_text = 'Purchase Month';
          var btn_price = 'Total = $14.99';
        } else {
          var p1 = '8.25 / mo';
          var p2 = '99.99 billed annually';
          var btn_text = 'Purchase Year';
          var btn_price = 'Total = $99.99';
        }
        $('.price1').html(p1);
        $('.price2').html(p2);
        $('.payment_btn').html(btn_text);
        $('#add_amount').val(p1);
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
          cvc: {
            required: true,
            minlength: 3,
          },



        },
        messages: {
          name: 'Please enter Name.',
          start: {
            required: 'Please Select The Opton.',

          },
          cardnumber: {
            required: 'Please Enter Cardnumber.',
            rangelength: 'Card Number should be 16 digit number.'
          },

          cvc: {
            required: 'Please Enter cvc Number.',
            minlength: 'CVC should be 3 digit number.'
          },


        },
        submitHandler: function(form) {
          form.submit();
        }
      });

      $("#submitbtn").click(function(event) {

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
              $("#add_amount").val('99.99');
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




      });

    });
  </script>
</body>

</html>