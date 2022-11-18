<?php
include('constants.php');

function subscription($name, $email = null, $cardDetails, $prduct_id = null, $addAmount = null, $planType = null, $productType = null)
{
    // print_r($name);
    // print_r($email);
    // print_r($cardDetails);
    // print_r($prduct_id);
    // die;

    $transactionId = $balanceTransaction = $description = $payStatus = '';
    $stripe_secret = STRIPE_SECRET_KEY;
    $header = array("Authorization: Bearer $stripe_secret");

    $cardNumber = $cardDetails['cardNumber'];
    $cvv = $cardDetails['cvv'];
    $exYear = $cardDetails['exYear'];
    $exMonth = $cardDetails['exMonth'];

    $customer_url = CUSTOMERS_URL;
    $cc_data = array("email" => $email);
    $cc_post_data = http_build_query($cc_data);
    $cc_res = curl_post($customer_url . '?' . $cc_post_data, '', $header);
    $CC_Json = json_decode($cc_res['res']);
    $customer_retrive_data = $CC_Json->data;
    if (!empty($customer_retrive_data)) {
        $customer_id = $customer_retrive_data[0]->id;
        $default_source = $customer_retrive_data[0]->default_source;
    } else {
        $cc_data = array("email" => $email, "name" => $name);
        $cc_post_data = http_build_query($cc_data);
        $cc_res = curl_post($customer_url, $cc_post_data, $header);
        $CC_Json = json_decode($cc_res['res']);
        $customer_id = $CC_Json->id;
    }

    if ($planType == 'lifetime') {
        if (!empty($customer_id)) {
            /********************** Create Token From Card ***************************************/
            $url = CREATE_TOKEN;
            $data = array(
                "card" =>
                array(
                    'number' => $cardNumber,
                    'exp_month' => $exMonth,
                    'exp_year' => $exYear,
                    'cvc' => $cvv
                ),
            );
            //print_r($cc_data);die;
            $post_data = http_build_query($data);
            $res = curl_post($url, $post_data, $header);
            $Json_res = json_decode($res['res']);

            /********************** Create Charges ***************************************/
            if (!empty($Json_res->id)) {
                if (empty($default_source)) {
                    $url = 'https://api.stripe.com/v1/customers/' . $customer_id . '/sources';
                    $data = array("source" => $Json_res->id);
                    $post_data = http_build_query($data);
                    curl_post($url, $post_data, $header);
                }

                $url = CREATE_CHARGES;
                $data = array(
                    "amount" => 100 * $addAmount,
                    "currency" => 'usd',
                    // "source" => $Json_res->id,
                    "description" => strtoupper($productType) . ' ' . ucfirst($planType),
                    "customer" => $customer_id,
                );
                $post_data = http_build_query($data);
                $res = curl_post($url, $post_data, $header);
                $chargeJson = json_decode($res['res']);
                if (!empty($chargeJson->id)) {
                    $payStatus = '1';
                    $transactionId = $chargeJson->id;
                    $balanceTransaction = $chargeJson->balance_transaction;
                    $description = 'Payment Done';
                    $return = array('success' => true, 'msg' => 'Charges Successfully');
                } else {
                    $payStatus = '2';
                    $description = $chargeJson->error->message;
                    $return = array('success' => false, 'msg' => $chargeJson->error->message);
                }
            } else {
                $payStatus = '2';
                $description = $Json_res->error->message;
                $return = array('success' => false, 'msg' => $Json_res->error->message);
            }
        } else {
            $payStatus = '2';
            $description = $CC_Json->error->message;
            $return = array('success' => false, 'msg' => $CC_Json->error->message);
        }
    } else {
        if (!empty($customer_id)) {

            $sub_url = SUBSCRIPTIONS_URL;
            $sub_data = array(
                "customer" => $customer_id,
                'currency' => 'usd',
                "items[0][price]" => $prduct_id,
                "payment_behavior" => 'default_incomplete',
                "expand" => array('latest_invoice.payment_intent'),
            );
            //print_r($sub_data);
            $sub_post_data = http_build_query($sub_data);
            $sub_res = curl_post($sub_url, $sub_post_data, $header);
            $sub_Json = json_decode($sub_res['res']);
            //print_r($sub_Json);
            //die;
            $subscription_id = $sub_Json->id;
            $clientSecret = $sub_Json->latest_invoice->payment_intent->client_secret;
            $payment_intent_id = $sub_Json->latest_invoice->payment_intent->id;

            if (!empty($subscription_id)) {
                $url = PAYMENT_METHODS_URL;
                $data = array(
                    "type" => 'card',
                    'card' => array(
                        'number' => $cardNumber,
                        'exp_month' => $exMonth,
                        'exp_year' => $exYear,
                        'cvc' => $cvv
                    ),
                );
                $post_data = http_build_query($data);
                $res = curl_post($url, $post_data, $header);
                $PM_Json = json_decode($res['res']);
                //print_r($PM_Json);
                //die;
                $payment_method_id = $PM_Json->id;

                if (!empty($payment_method_id)) {

                    $pi_url = PAYMENT_INTENTS_URL . $payment_intent_id . '/confirm';
                    $pi_data = array(
                        "payment_method" => $payment_method_id,
                        "shipping" => array("name" => "Demo Demo", "address" => array("line1" => "510 Townsend St", "postal_code" => "02124", "city" => "Boston", "state" => "MA", "country" => "US"))
                    );
                    $pi_post_data = http_build_query($pi_data);
                    $res = curl_post($pi_url, $pi_post_data, $header);
                    $PI_Json = json_decode($res['res']);
                    //print_r($PI_Json);
                    //die;
                    $id = $PI_Json->id;
                    $status = $PI_Json->status;

                    if (!empty($id) && $status == 'succeeded') {
                        $payStatus = '1';
                        $transactionId = $PI_Json->id;
                        $balanceTransaction = $PI_Json->charges->data[0]->balance_transaction;
                        $description = 'Payment Done';
                        $return = array('success' => true, 'msg' => 'Charges Successfully');
                    } else {
                        $payStatus = '2';
                        $description = $PI_Json->error->message;
                        $return = array('success' => false, 'msg' => $PI_Json->error->message);
                    }
                } else {
                    $payStatus = '2';
                    $description = $PM_Json->error->message;
                    $return = array('success' => false, 'msg' => $PM_Json->error->message);
                }
            } else {
                $payStatus = '2';
                $description = $sub_Json->error->message;
                $return = array('success' => false, 'msg' => $sub_Json->error->message);
            }
        } else {
            $payStatus = '2';
            $description = $CC_Json->error->message;
            $return = array('success' => false, 'msg' => $CC_Json->error->message);
        }
    }

    $data = array('payStatus' => $payStatus, 'transactionId' => $transactionId, 'balanceTransaction' => $balanceTransaction, 'description' => $description, 'return' => $return,);
    return $data;
}
