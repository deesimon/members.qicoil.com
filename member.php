<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
include('array.php');
include('constants.php');
// session_start();
if (!isset($_SESSION['email'])) {
  header('Location:index.php');
  exit;
}

$members = array();
$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
$data = array('userid' => $_SESSION['id']);
$url = MEMBER_SUBSCRIPTION;
$post_data = http_build_query($data);
$res = curl_post($url, $post_data, $header);
$response = json_decode($res['res']);
$response = $response->members;
if ($response[0]->fetch_flag != -1) {
  $members = $response;
}
// print_r($members);
// die;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Member Account - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <style>
    .table {
      background: white;
    }

    .sub-div {
      padding: 7px;
      background: white;
    }
  </style>
</head>
<?php include 'header.php'; ?>

<body id="con_listing">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <?php include 'sidebar.php'; ?>
        <div class="col-sm-12 col-md-10">
          <div class="row">
            <div class="col-md-12">
              <h2 class="main-title"><b>My Subscriptions</b></h2>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col">Membership Name</th>
                    <th scope="col" align="right">Price</th>
                    <th scope="col">Subscriptions Date</th>
                    <th scope="col">Renewal Date</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($members)) {
                    foreach ($members as $v) { ?>
                      <tr>
                        <td scope="row"><?php echo $GLOBALS['CATEGORIES'][$v->categoryId] . ' - ' . ucfirst($v->planType); ?></td>
                        <td scope="row" align="right">$<?php echo $v->amount; ?></td>
                        <td scope="row"><?php echo date('Y-m-d', strtotime($v->subscriptionDate)); ?></td>
                        <td scope="row"><?php echo date('Y-m-d', strtotime($v->expirationDate)); ?></td>
                        <td scope="row"><a href="question.php?id=<?php echo ($v->id);?>">Downgrade</a></td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td scope="row" colspan="5">You do not have an active membership.</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
                  </div>
              <div class="col sub-div">
                <h4><a href="https://www.qicoil.com/pricing/">View all Membership Options</a></h4>
              </div>
            </div>
          </div>
        </div>
      </div>
</body>

</html>