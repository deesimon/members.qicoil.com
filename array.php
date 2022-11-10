<?php
session_start();

$CATEGORIES = array(1 => "Rife", 2 => "Quantum", 3 => "Higher Quantum", 4 => "Inner Circle");

$posturl = 'https://apiadmin.qienergy.ai/api/subcategories';
$res = curl_post($posturl, '', $header);
// $response = json_decode($res['res']);
// print_r($res);
// die;
$SUBCATEGORIES = json_decode($res['res']);
$SUBCATEGORIES = $SUBCATEGORIES->subcategories;
$SITENAME = 'Qi Coil WebApp (BETA)';


$url = $_SERVER['REQUEST_URI'];
$key = 'index';
if ($url == '/logout.php' || $url == '/post.php' || $url == '/register.php' || $url == '/forgot.php') {
    setcookie('backurl', '', time() + (7200), '/');
} elseif (strpos($url, $key) == false && $url != '/favicon.ico' && $url != '/') {
    $cookie_name = "backurl";
    $cookie_value = $url;
    setcookie($cookie_name, $cookie_value, time() + (7200), '/');
}

function curl_post($url, $fields_string, $header, $t1 = 60, $userpwd = '', $put = '')
{

    $response = array();
    //	print_r($userpwd);
    //	print_r($header);die;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($fields_string)) curl_setopt($ch, CURLOPT_POST, 1);
    else {
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    }
    if (is_array($fields_string)) curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields_string));
    else curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, $t1);
    if (!empty($header) && is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    elseif (!empty($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    if (!empty($userpwd)) curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
    if (!empty($put)) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $put);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $result = curl_exec($ch);
    $p_time = curl_getinfo($ch);

    if (curl_errno($ch)) {
        $result =  "CURL ERROR: " . curl_error($ch);
    } elseif (empty($result)) {
        $result =  "Time out - ($t1 secs)"; // Timeout in $t1 secs 
    }
    curl_close($ch);
    $response['res'] = $result;
    $response['post_time'] = $p_time['total_time'];

    return $response;
}
