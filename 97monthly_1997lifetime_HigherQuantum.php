<?php

// use function PHPSTORM_META\type;
error_reporting(1);
include('array.php');
include('constants.php');
// session_start();
include('functions.php');


  $page_type = 3;
  $p_type = ' Unlock  197 + Higher Quantum';
  $imge_value = "subscription-images-higher-quantum.jpg";
  $save_value = '49.89';
  $pricepay = '97';
  $lifetime_value = '1997';
  $amount_value = '16';



if (!empty($_POST)) {
  $header = array('Content-Type: application/x-www-form-urlencoded');
  $update_plan = FREQUENCIES_URL . '?category=' . $_POST['page_type'];
  $response = curl_post($update_plan, '', $header);
  $data = json_decode($response['res']);
  foreach ($data->frequencies as $v) {
    $album_ids[] = $v->id;
    $subcategory_ids[] = $v->subCategoryId;
  }
  // print_r($album_ids);
  // print_r($subcategory_ids);die;

  if (isset($_SESSION['email'])) {
    $userid = $_SESSION['id'];
    $email = $_SESSION['email'];
  } else {
    $name = $_POST['bname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $data = array('name' => $name, 'email' => $email, 'password' => $password);
    $post_data = http_build_query($data);
    $url = REGISTER_URL;
    //  print_r($data);//die;
    $res = curl_post($url, $post_data, $header);
    //print_r($res['res']);die;

    $json = json_decode($res['res']);
    //	print_r($data2);die;
    if ($json->user[0]->fetch_flag == 1) {
      $userid = $json->user[0]->id;
      $_SESSION['id'] = $userid;
      $_SESSION['email'] = $email;
      $_SESSION['password'] = $password;
      $_SESSION['name'] = $name;
      $_SESSION['category_ids'] = array($_POST['page_type']);
      $_SESSION['subcategory_ids'] = $subcategory_ids;
      $_SESSION['album_ids'] = $album_ids;
      $_SESSION['verified'] = 0;
    } else {
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
  // print_r($_POST);die;
  if ($_POST['page_type'] == 3) {
    if ($addAmount == '97') {
      $prduct_id =  PRODUCT_HIGHER_QUANTUM_MONTHLY;
      $planType = 'monthly';
    } elseif ($addAmount == '1997') {
      $prduct_id = PRODUCT_HIGHER_QUANTUM_LIFETIME;
      $planType = 'lifetime';
    }
    $productType = 'Higher Quantum';
  } 



  // print_r($name);
  // print_r($email);
  // print_r($cardDetails);
  // print_r($prduct_id);
  //   die;
  if (empty($name) || empty($cardNumber) || empty($cvv)) {
    $return = array('success' => false, 'msg' => 'Please Enter Details');
  } else {
    $subscription_res = subscription($name, $email, $cardDetails, $prduct_id, $addAmount, $planType, $productType);
    $return = $subscription_res['return'];
  }
  // print_r($subscription_res);
  // die;
  if (!empty($subscription_res['description']) && !empty($subscription_res['payStatus'])) {

    $url = PAYMENT_ADD_URL;
    $header = array('Content-Type: application/x-www-form-urlencoded');
    $add_data = array('userid' => $userid, 'name' => $name, 'amount' => $addAmount, 'payStatus' => $subscription_res['payStatus'], 'payType' => 1, 'cardNumber' => $cardNumber, 'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv, 'transactionId' => $subscription_res['transactionId'], 'balanceTransaction' => $subscription_res['balanceTransaction'], 'description' => $subscription_res['description'], 'productType' => $productType, 'planType' => $planType, 'category_id' => $_POST['page_type']);

    $_SESSION['category_ids'] = array_merge($_SESSION['category_ids'], array($_POST['page_type']));
    $_SESSION['subcategory_ids'] = array_merge($_SESSION['subcategory_ids'], $subcategory_ids);
    $_SESSION['album_ids'] = array_merge($_SESSION['album_ids'], $album_ids);
    //print_r($add_data);
    // die;
    $post_data = http_build_query($add_data);
    $res = curl_post($url, $post_data, $header);
    // print_r($res);
    // die;
  }

  echo json_encode($return);
  die;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Payment - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
    .error,
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

    #accordion_faq {
      max-width: 1200px;
      width: 100%;
      margin: 2em auto 0;
    }

    .ui-state-active,
    .ui-widget-content .ui-state-active,
    .ui-widget-header .ui-state-active,
    a.ui-button:active,
    .ui-button:active,
    .ui-button.ui-state-active:hover {
      border: 1px solid #010101;
      background: #fafcff;
      font-weight: normal;
      color: #666;
    }

    .ui-widget-content {
      border: none;
      color: #333333;
      margin-bottom: 12px;
    }

    .ui-state-active {
      border: 1px solid #010101;
      color: #fff;
      background: #010101 !important;
    }

    .ui-state-active,
    .ui-widget-content .ui-state-active,
    .ui-widget-header .ui-state-active,
    a.ui-button:active,
    .ui-button:active,
    .ui-button.ui-state-active:hover {
      border: none;
      border-bottom: 1px solid #010101;
    }

    .ui-corner-top,
    .ui-corner-all {
      border-radius: 0 !important;
    }

    .ui-accordion .ui-accordion-header {
      font-family: "Work Sans", sans-serif;
      font-weight: 700;
      border-radius: 0px !important;
      border: 1px solid #010101;
      padding: 12px 6px;
      text-align: left;
    }

    .ui-accordion .ui-accordion-content {
      font-weight: 400;
      text-align: left;
      font-family: "Poppins", sans-serif;
    }

    .ui-accordion-content li {
      list-style: disc;
    }

    .ui-widget-content p a {
      color: blue !important;
      text-decoration: underline;
    }
  </style>
  <script>
    $(function() {
      $("#accordion_faq").accordion({
        heightStyle: "content"
      });
    });
  </script>
</head>

<body>
  <?php include 'header.php'; ?>

  <section id="payment">
    <div class="container">
      <div class="row">

        <?php if (isset($_SESSION['category_ids']) && in_array($page_type, $_SESSION['category_ids'])) { ?>
          <div class="col-md-8 col-md-offset-3">
            <div class="col-md-9" id='d1'>
              <h3> You already purchased this subscription.</h3>
            </div>
          </div>
        <?php } else { ?>
          <?php if (STRIPE_MODE == 1) { ?>
            <div class="alert alert-warning text-center" role="alert">
              Payment mode is in Test Mode, Please use 4242424242424242 credit card, for expiry date use 12/<?php echo date('y'); ?> or future date and use 123 for CVC to purchase.
            </div>
          <?php } ?>
          <div class="col-md-3">
          </div>
          <div class="col-md-6 fan">
            <div class="col-md-12" id='d1'>
              <h3> <?php echo $p_type; ?> Frequencies </h3>
              <img class="ss1" src="images/<?php echo  $imge_value; ?>" alt="">

              <div class="btn_box d-flex row-fluid">
                <button type="button" class="btn toggle_btn1 col-md-6 b1 year_mo col-xs-6">Monthly</button>
                <button type="button" class="btn  col-md-6 b2 year_mo col-xs-6">Lifetime

                  <!-- <span class="save">Save UpTo $<?php echo $save_value; ?></span> -->

                </button>
              </div>


              <div class="price_detail p-4 col-md-12">
                <h3>$<span class="price1"><?php echo $pricepay; ?> / mo

                  </span> </h3>
                <p><b><span class="price2"></span></b></p>
                <p>
                <h4>60 Day Money Back Guarantee Cancel Anytime</h4>
                </p>
                <p>
                  <smal>Prices in USD.</small>
                </p>
              </div>

              <!-- <div class="top_border"> </div> -->


            </div>

            <div>
            <?php include 'Subscriptions-payment.php'; ?>
            </div>
          </div>
          <div class="col-md-3">
          </div>
        <?php } ?>
      </div>
    </div>
  </section>





<div>
<?php include 'add_text.php'; ?>
</div>





  <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
  <script>
    window.onload = function() {

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

        $(this).addClass("toggle_btn1");
        $(this).siblings('button').removeClass("toggle_btn1");

        if ($(this).hasClass('b2')) {
          //  $(amount_value).hide();

          ///  code fire when click on year
          var amount = '<?php echo $lifetime_value ?>';
          var p1 = '<?php echo $lifetime_value ?> ';

        } else {
          ///  code fire when click on month
          var amount = '<?php echo $pricepay ?>';
          var p1 = '<?php echo $pricepay ?> / mo';
          var p2 = '';
        }

        $('.price1').html(p1);
        $('.price2').html(p2);
        $('#add_amount').val(amount);
        // $('.payment_btn').html(btn_text);
        // $('.pri').html(btn_price);


      });

      $.validator.addMethod("letters_numbers_special", function(value, element) {
        return this.optional(element) || /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*])[a-zA-Z0-9!@#$%&*]+$/i.test(value);
      }, "A minimum 8 characters password contains a combination of special character and number");

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
            required: true,
            email: true
          },

          password: {
            required: true,
            minlength: 8,
            // letters_numbers_special: true
          },

          expirydate: {
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
          },

          password: {
            required: 'Please Enter password.',
            minlength: 'Password should be 8 character.'
          },

          expirydate: {
            required: 'Please Enter  month and year.',
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

        if ($('#form').valid() == true) {

          var ele = $(this);
          $('#payment-error').removeClass('error').removeClass('success').html("");
          ele.prop('disabled', true);
          var data = $("#form").serializeArray();
          ele.find('.spinner-border').css('display', 'inline-block');

          $.ajax({
            url: "97monthly_1997lifetime_HigherQuantum.php",
            type: "POST",
            data: data,
            dataType: 'json',
            success: function(res) {

              if (res.success == true) {
                $('#payment-error').addClass('success').html("Successfully payment");
                $("#form").find("input, textarea").val("");
                $("#expirydate").val('');
                $("#add_amount").val('<?php echo $permonth_value; ?>');
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