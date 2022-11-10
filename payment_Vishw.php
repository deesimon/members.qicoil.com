<?php

error_reporting(0);
include ('array.php');
session_start();
if(!isset($_SESSION['email']))
{ 
header('Location:login.php'); exit;
}


if (!empty($_POST))
{

//print_r($_SESSION);die;
$userid = $_SESSION['id'];
$return = [];

$stripe_secret = 'sk_test_51HjL9VBpAKNCvML59wWzisVva9JGbWupiFUuu5IU22QhQ4wKru3uoBz1bQJyodQJKvOs3BMc2nU5CZxdHtxL9JZp00L20hNYBv';
$header = array("Authorization: Bearer $stripe_secret");

$cardNumber =$_POST ['cardnumber'];
$cvv = $_POST['cvc'];
$start = explode("-", $_POST['start']);
$exYear = $start[0];
$exMonth = $start[1];
$addAmount = $_POST ['add_amount'];
$addAmount = '4.99';
$planPriceCents = round($addAmount*100); 

$customer_url='https://api.stripe.com/v1/customers';
$cc_data=array("email" => $_SESSION['email'], "name" =>$_POST ['bname']);
$cc_post_data = http_build_query($cc_data);
$cc_res = curl_post( $customer_url,$cc_post_data,$header);
$CC_Json = json_decode($cc_res['res']);
//print_r($CC_Json);die;
$customer_id=$CC_Json->id;
//$customer_id='cus_M9UKAsFrIgvuPl';
if(!empty($customer_id)){
	
	$url = 'https://api.stripe.com/v1/prices';
	$data=array('unit_amount' => $planPriceCents, 
                'currency' => 'usd', 
                'recurring' => ['interval' => 1], 
                'product_data' => ['name' => $planName]
			);
	$post_data = http_build_query($data);
	$res = curl_post($url,$post_data,$header);
	$PM_Json = json_decode($res['res']);
//	print_r($PM_Json);//die;

	$url = 'https://api.stripe.com/v1/payment_methods';
	$data=array("type" => 'card',
				'card' => array('number'=>$cardNumber,
						'exp_month'=>$exMonth,
						'exp_year'=>$exYear,
						'cvc'=>$cvv),
				);
	$post_data = http_build_query($data);
	$res = curl_post($url,$post_data,$header);
	$PM_Json = json_decode($res['res']);
//	print_r($PM_Json);//die;
	$payment_method = $PM_Json->id;
//	$payment_method = 'pm_1LRCFGBpAKNCvML5jYBohsVC';

	$curl = 'https://api.stripe.com/v1/subscriptions';
	$cc_data=array("customer" => $customer_id,
					"items[0][price]" =>'price_1LPNCzBpAKNCvML5WGg2JCLF',
					"default_payment_method"=>$payment_method
				);
	echo $cc_post_data = http_build_query($cc_data);
	$cc_res = curl_post( $curl,$cc_post_data,$header);
	$CC_Json = json_decode($cc_res['res']);
	print_r($CC_Json);die;

	$cc_data=array('invoice_settings'=>array('default_payment_method'=>$payment_method));
	print_r($cc_data);
	$cc_post_data = http_build_query($cc_data);
	$cc_res = curl_post( $customer_url.'/'.$customer_id,$cc_post_data,$header);
	$cust_Json = json_decode($cc_res['res']);
	print_r($cust_Json);die;
 
// print_r ($_POST);
// die;
$addAmount = $_POST ['add_amount'];
$cardNumber =$_POST ['cardnumber'];
$cvv = $_POST['cvc'];
$bname =$_POST ['bname'];
$start = explode("-", $_POST['start']);
$exYear = $start[0];
$exMonth = $start[1];
$cc_url = 'https://api.stripe.com/v1/tokens';
$cc_header = array("Authorization: Bearer $stripe_secret");
$cc_data = array("card" => 
			     array("number" => $cardNumber,
					"exp_month" =>$exMonth,
					"exp_year" =>$exYear,
					"cvc" => $cvv)

		);
  
//print_r($cc_data);die; 
$cc_post_data = http_build_query($cc_data);
$cc_res = curl_post($cc_url,$cc_post_data,$cc_header);
$CC_Json = json_decode($cc_res['res']);
 //print_r($CC_Json);die;

if(!empty($CC_Json->id)){
	
	$url = 'https://api.stripe.com/v1/charges';
	$header = array("Authorization: Bearer $stripe_secret");
	$data = array("amount" => 100 * $addAmount,
				"currency" => 'USD',
				"source" => $CC_Json->id,
				"description" => $bname,
        "shipping" => array("name" => "Demo Demo","address" => array("line1"=>"510 Townsend St","postal_code"=>"02124","city"=>"Boston","state"=>"MA","country"=>"US")),
      );

	$post_data = http_build_query($data);
	$res = curl_post($url,$post_data,$header);
	$chargeJson = json_decode($res['res']);

  //print_r($chargeJson);
  if(!empty($chargeJson->id)){
    $return = array('success'=>true, 'msg'=>'Charges Successfully');
    $add_data = array('userid'=>$userid, 'name'=>$bname, 'amount'=>$addAmount, 'payStatus'=>1, 'payType'=>1, 'cardNumber'=>$cardNumber, 'exYear'=>$exYear, 'exMonth'=>$exMonth, 'cvv'=>$cvv, 'transactionId'=>$chargeJson->id, 'balanceTransaction'=>$chargeJson->balance_transaction, 'description'=>'Payment Done');
  }else {
    $add_data = array('userid'=>$userid, 'name'=>$bname, 'amount'=>$addAmount, 'payStatus'=>2, 'payType'=>1, 'cardNumber'=>$cardNumber, 'exYear'=>$exYear, 'exMonth'=>$exMonth, 'cvv'=>$cvv, 'description'=>$chargeJson->error->message);
    $return = array('success'=>false, 'msg'=>$chargeJson->error->message);
  }
  $url = 'https://apiadmin.qienergy.ai/payment/add';
  $header = array();
  $post_data = http_build_query($add_data);
  $res = curl_post($url,$post_data,$header);
  //print_r($res);die;
}else {
  $return = array('success'=>false, 'msg'=>$CC_Json->error->message);
}
echo json_encode($return);die;
//die;
//header('Location:payment.php');die;
}
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
    color:red;
   }
   .success { color: green; }
    .spinner-border{display:none;}
    h3{ text-align: center;}
</style>
</head>
  
<body>
  <?php  include 'header.php';?>
           
 <section id="payment">
    <div class="container">
        <div class="row">

        <!-- <div class="col-md-10"> <img src="images/ic_highlight_of.png" style="float:right;"> </div> -->
            <div class="col-md-8 col-md-offset-3">
       
              <div class="col-md-9" id='d1'> 
                     <h3> Rife Frequencies </h3>     
                 <label class="save_m">SAVE &nbsp;50%</label> 
                <div class="btn_box d-flex">
                <button type="button" class="btn toggle_btn1 col-md-6 b1 year_mo">Yearly</button> 
                <button type="button" class="btn  col-md-6 b2 year_mo">Monthly</button>
                </div>
           

                <div class="price_detail p-4 col-md-12"> 
                      <h3>$<span class="price1">99.99</span> </h3> 
                      <p><b>$<span class="price2">99.99 billed annually</span>*</b></p> 
                     <p><h4>cancel anytime *</h4></p>
                     <p><smal>prices in USD. Subject to currency exchange rates.</small></p>
                </div>
              <div class="top_border"> </div> 

            
              </div>   

            
              <div class="col-md-9 mt-3 mb-4">
             
              <form action="" method="POST" id="form">
              <input type="hidden" name="add_amount" id="add_amount" value="99.99">
              <input type="text" class="form-control input" placeholder="Bill Name"   name="bname">
              <input type="text" class="form-control input" placeholder="Card NUmber" name="cardnumber">
        

              <div class="flex-container">

          <div class="flex-child">
<label for="start">Expiry date</label>
              <input type="month" id="start"  class="form-control input" name="start"
                min="<?php echo date('Y-m');?>" value="<?php echo date('Y-m');?>" placeholder=">MM/YY/">
</div>

<div class="flex-child">
<label>CV CODE</label>
    <input type="tel" class="form-control input " placeholder="CVC" name="cvc">
</div>

</div>
<div class="flex-container">
   <p id="payment-error"></p>
</div>
                    <span class="payment_btn col-md-12 mb-3 p1 pri" name="amount"> Total = $99.99  </span>
                    
                <button type="button" class="btn payment_btn col-md-12 " value="submit" id="submitbtn">
              Purchase Year <div class="spinner-border" role="status"></div></button>

             
          
              </form>  
              </div>
              
  
              </div>


        
        </div>
    </div>
 </section>
 <script>
$(document).ready(function(){
 

$('.year_mo').click(function(){
  
  $(this).addClass("toggle_btn1");
  $(this).siblings('button').removeClass("toggle_btn1");
 
  if($(this).hasClass('b2')){
    var p1 = '4.99';
    var p2 = '59.99 billed monthly';
    var btn_text = 'Purchase Month';
    var btn_price = 'Total = $59.99';
  }else{
    var p1 = '99.99';
    var p2 = '99.99 billed annually';
    var btn_text = 'Purchase Year';
    var btn_price = 'Total = $99.99';
  }
  $('.price1').html(p1);
  $('.price2').html(p2);
  $('.payment_btn').html(btn_text);
  $('#add_amount').val(p1);
  $('.pri').html( btn_price);

  
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
      submitHandler: function (form) {
        form.submit();
      }
    });

    $("#submitbtn").click(function(event){
     
      var ele = $(this);
      $('#payment-error').removeClass('error').removeClass('success').html("");
      ele.prop('disabled', true);
      var data=$("#form").serializeArray();
      ele.find('.spinner-border').css('display','inline-block');

      $.ajax({
      url:"payment.php",
        type:"POST",
        data:data,
        dataType: 'json',
        success:function(res){
          
          if(res.success == true){
            $('#payment-error').addClass('success').html("Successfully payment");
            $("#form").find("input, textarea").val("");
            $("#start").val('<?php echo date('Y-m');?>');
            $("#add_amount").val('99.99');            
          }else {
            $('#payment-error').addClass('error').html(res.msg);
          }
          ele.prop('disabled', false);
          ele.find('.spinner-border').hide();
          
        },
        error:function(xhr) {
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