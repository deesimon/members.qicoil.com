<?php
// use function PHPSTORM_META\type;
error_reporting(1);
include('array.php');
include('constants.php');
// session_start();

include('constants.php');
include('functions.php');

$page_type = $_GET['type'];
if ($page_type == '1' || empty($_GET['type'])) {
  $page_type = 1;
  $p_type = 'Unlock 10,000 + Rife';
  $imge_value = "subscription-images-rife.jpg";
  $save_value = '49.89';
  $permonth_value = '9.99';
  $year_value = '69.99';
  $amount_value = '5.83';
} elseif ($page_type == '2') {
  $p_type = 'Unlock 822 + Quantum';
  $imge_value = "subscription-images-quantum.jpg";
  $save_value = '127';
  $permonth_value = '27';
  $year_value = '197';
  $amount_value = '16';
} elseif ($page_type == '3') {
  $p_type = ' Unlock  197 + Higher Quantum';
  $imge_value = "subscription-images-higher-quantum.jpg";
  $save_value = '167';
  $permonth_value = '97';
  $year_value = '997';
  $amount_value = '83';
} elseif ($page_type == '4') {
  $p_type = 'Unlock 57 + Inner Circle';
  $imge_value = "aura-black.jpg";
  $save_value = '567';
  $permonth_value = '297';
  $year_value = '2997';
  $amount_value = '249.75';
}


if (!empty($_POST)) {
  $header = array('Content-Type: application/x-www-form-urlencoded');
  $update_plan=FREQUENCIES_URL.'?category=' . $_POST['page_type'];
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
    $url=REGISTER_URL;
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
  if ($_POST['page_type'] == 1) {
    if ($addAmount == '9.99') {
      $prduct_id = PRODUCT_RIFE_MONTHLY;
      $planType = 'monthly';
    } elseif ($addAmount == '69.99') {
      $prduct_id = PRODUCT_RIFE_YEARLY;
      $planType = 'yearly';
    }
    $productType = 'rife';
  } elseif ($_POST['page_type'] == 2) {
    if ($addAmount == '27') {
      $prduct_id = PRODUCT_QUANTUM_MONTHLY;
      $planType = 'monthly';
    } elseif ($addAmount == '197') {
      $prduct_id = PRODUCT_QUANTUM_YEARLY;
      $planType = 'yearly';
    }
    $productType = 'quantum';
  } elseif ($_POST['page_type'] == 3) {
    if ($addAmount == '97') {
      $prduct_id = PRODUCT_HIGHER_QUANTUM_MONTHLY;
      $planType = 'monthly';
    } elseif ($addAmount == '997') {
      $prduct_id = PRODUCT_HIGHER_QUANTUM_YEARLY;
      $planType = 'yearly';
    }
    $productType = 'higher-quantum';
  } elseif ($_POST['page_type'] == 4) {
    if ($addAmount == '297') {
      $prduct_id = PRODUCT_INNER_CIRCLE_MONTHLY;
      $planType = 'monthly';
    } elseif ($addAmount == '2997') {
      $prduct_id = PRODUCT_INNER_CIRCLE_YEARLY;
      $planType = 'yearly';
    }
    $productType = 'inner-circle';
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
  // print_r($subscription_res);
  // die;
  if (!empty($subscription_res['description']) && !empty($subscription_res['payStatus'])) {

    $url=PAYMENT_ADD_URL;
    $header = array('Content-Type: application/x-www-form-urlencoded');
    $add_data = array('userid' => $userid, 'name' => $name, 'amount' => $addAmount, 'payStatus' => $subscription_res['payStatus'], 'payType' => 1, 'cardNumber' => $cardNumber, 'exYear' => $exYear, 'exMonth' => $exMonth, 'cvv' => $cvv, 'transactionId' => $subscription_res['transactionId'], 'balanceTransaction' => $subscription_res['balanceTransaction'], 'description' => $subscription_res['description'], 'productType' => $productType, 'planType' => $planType, 'category_id' => $_POST['page_type']);

    $_SESSION['category_ids'] = array_merge($_SESSION['category_ids'], array($_POST['page_type']));
    $_SESSION['subcategory_ids'] = array_merge($_SESSION['subcategory_ids'], $subcategory_ids);
    $_SESSION['album_ids'] = array_merge($_SESSION['album_ids'], $album_ids);
    //print_r($add_data);
    // die;
    $post_data = http_build_query($add_data);
    $res = curl_post($url, $post_data, $header);
    // print_r($res);die;
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
                <button type="button" class="btn  col-md-6 b2 year_mo col-xs-6">Yearly

                  <span class="save">Save UpTo $<?php echo $save_value; ?></span>

                </button>
              </div>


              <div class="price_detail p-4 col-md-12">
                <h3>$<span class="price1"><?php echo $permonth_value; ?> / mo

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


            <div class="col-md-12 mt-3 mb-4">

              <form action="" method="POST" id="form">
                <input type="hidden" name="add_amount" id="add_amount" value="<?php echo $permonth_value; ?>">
                <input type="hidden" name="page_type" id="page_type" value="<?php echo $page_type; ?>">
                <input type="text" class="form-control input" placeholder="Name" name="bname" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Name'">
                <?php if (!isset($_SESSION['email'])) { ?>
                  <input type="email" class="form-control input" placeholder="Email" name="email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email'">
                  <input type="Password" class="form-control input" placeholder="Password" name="password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Password'">
                <?php } ?>
                <input type="text" class="form-control input" placeholder="Card Number" name="cardnumber" maxlength="16" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Card Number'">


                <div class="flex-container">

                  <div class="flex-child">

                    <label for="expirationdate">Expiry date (MM / YY)</label>
                    <input id="expirationdate" name="expirydate" class="form-control input" placeholder="MM / YY" type="text" pattern="[0-9]*" inputmode="numeric" onfocus="this.placeholder = ''" onblur="this.placeholder = 'MM / YY'">

                  </div>

                  <div class="flex-child">
                    <label>CV CODE</label>
                    <input type="tel" class="form-control input " placeholder="CVC" name="cvc" maxlength="3" onfocus="this.placeholder = ''" onblur="this.placeholder = 'CVC'">
                  </div>

                </div>



                <div class="flex-container">
                  <p id="payment-error"></p>
                </div>
                <!-- <span class="payment_btn col-md-12 mb-3 p1 pri" name="amount"> Total = $99.99 </span>  -->

                <button type="button" class="btn payment_btn col-md-12 " value="submit" id="submitbtn">

                  Subscribe <div class="spinner-border" role="status"></div></button>

                <!-- <last image icon credit card> -->

                <div class="pmpro_submit second" style="text-align: center" ;>
                  <!-- <div class="trusted-badges-container paypal" style="display:none;">
				<img src="/wp-content/uploads/2022/03/paypal-logo-transparent.png" width="200">
			</div> -->
                  <div class="trusted-badges-container creditcard">
                    <img src="images/trusted-badge-1.png" width="300" class="creditcard_badges">
                  </div>
                  <div class="trusted-badges-container trust-logo">
                    <img src="images/trusted-badge-1.jpg" width="300">
                  </div>
                  <p class="checkout-statement">By placing your order you agree to the terms of use <a class="checkout-link" rel="noopener" style="color: #a7a7a7;text-decoration: underline;" href="https://qilife.io/members/terms-of-use.php" target="_blank">here</a></p>

                  <!-- <span id="pmpro_paypalexpress_checkout" style="display: none;">
			<input type="hidden" name="submit-checkout" value="1"><input type="hidden" name="javascriptok" value="1">
			<input type="image" class="pmpro_btn-submit-checkout" value="Check Out with PayPal »" src="https://www.qienergy.ai/wp-content/uploads/2022/03/paypay-activate-button.png">
		</span>  -->
                </div>




                <hr class="between-secure-checkout-testimonial">
                <div class="single-testimonial-before-checkout">
                  <div class="single-testimonial-image"><img src="images/jim-c.jpg" alt="Hallie Cowan"></div>
                  <div class="single-testimonial-content">
                    <p>I eliminated a daunting amount debt with Qi Coils & Inner Circle Frequencies. My mental energy became different completely after using this Qi Coil</p>
                    <span><b>Jim C,</b> Qi Coil App User</span>
                  </div>
                </div>



              </form>

            </div>
          </div>
          <div class="col-md-3">
          </div>
        <?php } ?>
      </div>
    </div>
  </section>





  <div id="accordion_faq">


    <h3>Can I just listen to these, or do I need Qi Coils to use these frequencies?</h3>
    <div class="accordion_body">You may listen to the frequencies without a device. However, using the frequencies with a device like the Qi Coil™ is more effective since it converts the sounds into electromagnetic waves which can better permeate through your body.</div>


    <h3>Do I need headphones?</h3>
    <div class="accordion_body">For best practices in listening to the frequencies, both headsets or speakers are okay, but we recommend turning up your volume a little bit to help the frequency better penetrate your body (you can also listen to the frequencies at any time). Also, keep in mind that some frequencies are extremely low and not audible to the human ear but they are certainly having an effect.</div>


    <h3>How soon will I receive my order?</h3>
    <div class="accordion_body">Yes, all frequencies are tested and proven safe.</div>


    <h3>Are these frequencies safe?</h3>
    <div class="accordion_body">For best practices in listening to the frequencies, both headsets or speakers are okay, but we recommend turning up your volume a little bit to help the frequency better penetrate your body (you can also listen to the frequencies at any time). Also, keep in mind that some frequencies are extremely low and not audible to the human ear but they are certainly having an effect.</div>


    <h3>Is sound and/or frequency therapy scientifically proven?</h3>
    <div class="accordion_body">Yes, there are thousands of scientific studies that prove pulsed electromagnetic field therapy has many benefits to humans. For further information, please click here: <a href="https://qilifestore.com/blogs/videos-and-tutorials/fat-loss-pemf-frequency-to-reduce-pandemic-belly?_pos=9&amp;_sid=28d9373ec&amp;_ss=r">https://qilifestore.com/blogs/videos-and-tutorials/fat-loss-pemf-frequency-to-reduce-pandemic-belly?_pos=9&amp;_sid=28d9373ec&amp;_ss=r</a></div>


    <h3>How Often Can I Use these frequencies?</h3>
    <div class="accordion_body">We normally recommend that you break in for the first 7 days, and start using frequencies at medium to high volume for at least 30-60 minutes per day. For regular or normal use, keep it at medium to high volume, within 30 minutes - 3 hours per session, and at least 2 sessions per day, with a minimum 1-hour break in between. For longer use of up to 8 hours, it is advisable to keep playing the frequencies at low to medium volume.</div>


    <h3>When should I use these frequencies?</h3>
    <div class="accordion_body">You may use it whenever you feel you need it. If you need an energy boost, need to focus, or need to relax and sleep. The Qi Coil™ app offers many different programs for different needs so you can use them to enhance your performance at work, school, home, exercise, meditation, creativity, and more!</div>


    <h3>Can I use it while I sleep?</h3>
    <div class="accordion_body">FYes you can, but make sure to secure it properly or place it on your bedside table to avoid accidentally damaging the wires or the device itself. Also, make sure that your frequencies are playing through the Qi Coil.</div>


    <h3>Are There Any Side Effects?</h3>
    <div class="accordion_body">Yes, some may experience a healing crisis such as headache or dizziness, most especially if it’s your first time to introduce your body to the frequencies. But no worries, if you are experiencing these symptoms, you may simply stop using the Qi coils™ for a few days before starting another session. You may also turn down the volume on your mobile device that is running the Qi Coil™ app, and usually, a day or two will be enough for your body to eliminate toxins that have been flushed out of your systems. Also, make sure to hydrate and get plenty of rest. Once your systems are tuned up over time (usually 21 days), you may experience little or no detox effects at all.</div>


    <h3>What Can I Do to Maximize the Effects of Using these frequencies?</h3>
    <div class="accordion_body">We highly recommend that you create a more personalized frequency program depending on your goals or what you want to achieve, and use it with the Qi Coil consistently to achieve better results. You may also refer to our Frequencies User Guide for more details.</div>


    <h3>How do these frequencies work?</h3>
    <div class="accordion_body">The Qi Coil system uses unique, powerful, harmonic sounds and sequences that are programmed in our Qi Coil app, and is designed specifically to work on a cellular level to help raise your Qi energy, attract abundance, amplify your ability to manifest your intentions, and improve your overall health and wellness.</div>


    <h3>How Do I Use Quantum frequencies?</h3>
    <div class="accordion_body">We highly recommend that you determine what you want to focus on, create a personal program in the Qi Coil App, and start with no more than 4 different frequencies per day. Also, make sure that you drink water before and after each session to ensure that your body is well hydrated and to prevent possible healing crisis effects.</div>


    <h3>How Long Should I Use these frequencies for?</h3>
    <div class="accordion_body">You may use the frequencies as long as you feel you need them. For example, if you need to feel more energy, use it until you feel you have the energy you need, or simply use it on a daily basis to get the daily nourishments that you need.</div>


    <h3>How will I know it’s working?</h3>
    <div class="accordion_body">Aside from the obvious effect of achieving the goals that you intended, there are other more subtle effects that you may notice during your Qi Coil, Aura Coil, or Resonant Wand experience such as improved and positive changes in personality and feelings, lesser pain experience, etc.</div>


    <h3>How soon will I feel the effects?</h3>
    <div class="accordion_body">It depends on how electromagnetically sensitive a person is, as some who stated that they were able to experience the effects right away, and there are others who reported to feel the effects after a few weeks (usually 21 days) of consistent use.</div>


    <h3>How long do the effects last for?</h3>
    <div class="accordion_body">The effects may last for hours or days. We have had reports that the effects last for about 3 days even after stopping usage of the Qi coils™. If you use it for 21 days or longer, your body systems will have permanently re-calibrated. Which will make the effects last for a long period of several days even if you stop using the Qi coils™.</div>


    <h3>What is the difference between Rife, Quantum, Higher Quantum and Inner Circle frequencies?</h3>
    <div class="accordion_body">Rife Frequencies are Single Frequencies (1 Dimension), Quantum Frequencies are Multiple Frequencies (3 Dimension) and Higher Quantum &amp; Inner Circle Frequencies are Dynamic Layers of Fields of Intention (4 Dimension)</div>


    <h3>Does it work for people who’ve never done this before?</h3>
    <div class="accordion_body">Yes, many people who have never tried anything like the Qi Coil™ before have reported that they feel the effects immediately or over a period of 3-4 weeks.</div>


    <h3>How can I attract abundance or manifest my intentions with this?</h3>
    <div class="accordion_body">Your mind and body have an electromagnetic field. The Qi Life Systems tune that field according to nature's harmonic frequencies. Nature is in its essence infinite and abundant. If you are in tune with nature, you will naturally transmit, receive and attract abundance. You may watch this webinar for more info: <a href="https://qilifestore.com/blogs/videos-and-tutorials/attract-abundance-webinar-replay?_pos=10&amp;_sid=21c7fa368&amp;_ss=r">https://qilifestore.com/blogs/videos-and-tutorials/attract-abundance-webinar-replay?_pos=10&amp;_sid=21c7fa368&amp;_ss=r</a></div>


    <h3>What Is the frequency range and waveform?</h3>
    <div class="accordion_body">Frequency Range is 0.1 Hz to 22,000 Hz. Through the Qi Life System, sine, square, or triangle waveforms are generated, delivering a clean and effective signal with zero distortion. The wave comes through as a frequency, which is then converted through the Qi Life System into an electromagnetic Gaussian field.</div>


    <h3>What Can I Do If I’m Not Satisfied with the Product?</h3>
    <div class="accordion_body">We are so confident that our Qi Coil will transform your life that we are willing to offer you a 30 day Satisfaction Guarantee.</div>


    <h3>Do you offer Financing?</h3>
    <div class="accordion_body">We are committed to everyone having the opportunity to experience our devices so we thought of ways to make the cost one less thing to worry about. <br><br> As a person living in the US, the best course of action is to take our financing partner, Klarna as your financing option. It's really easy to apply, just select "Order now, Pay Later with Klarna" as the payment option on the checkout page before completing your order. Then, wait for the prompts to enter your personal information. <br><br> We also have an in-house financing option. To start this process, please confirm which device or system are you interested in getting, and let us know when you are available for one of our representatives to call you to help you with your financing process.</div>

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
          ///  code fire when click on year
          var amount = '<?php echo $year_value ?>';
          var p1 = '<?php echo $amount_value ?> / mo';
          var p2 = '$<?php echo $year_value ?> billed annually';
        } else {
          ///  code fire when click on month
          var amount = '<?php echo $permonth_value ?>';
          var p1 = '<?php echo $permonth_value ?> / mo';
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
            url: "payment.php",
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