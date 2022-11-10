<?php
// session_start();
include('array.php');

if (empty($_GET) || empty($_GET['q'])) {
  header('location:frequencies.php');
  die;
}
$userid = base64_decode($_GET['q']);
// echo $userid;
// die;

$header = array('Content-Type: application/x-www-form-urlencoded');
$data = http_build_query(array('userid' => $userid));
$url = 'https://apiadmin.qienergy.ai/api/verify_user';
$res = curl_post($url, $data, $header);
// echo $data;
// print_r($res['res']);
// die;
$response = json_decode($res['res']);
// print_r($response);
$fetch_flag = $response->user[0]->fetch_flag;
if ($fetch_flag == 1) {
  if (isset($_SESSION['email'])) {
    $_SESSION['verified'] = 1;
  }
}
header('location:frequencies.php');
die;
