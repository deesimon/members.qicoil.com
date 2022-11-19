<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(1);
include('array.php');
include('constants.php');
// session_start();
if (!isset($_SESSION['email'])) {
  header('Location:index.php');
  exit;
}
$name=$_SESSION['name'];
$email=$_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Profile  - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <style>
    .form123{
        background-color: white;
    }
    </style>
</head>
<?php include 'header.php'; ?>

<body id="con_listing">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10">
        <div style="background: white; padding: 10px 0 10px 18px;">
          <div class="row">

          <div class="row">
            <div class="col-md-12">
                <!-- <h5>TDY Marketing</h5> -->
              <h3 class="main-title"><b>Profile</b></h3>
              </div>
            </div>

            <div class="row">
          <div class="col-md-12">
          <?php if (!empty($_SESSION['success'])) {
              echo '<p class="col-lg-6 btn-success">' . $_SESSION['success'] . '</p>';
              unset($_SESSION['success']);
            }
            if (!empty($_SESSION['err'])) {
              echo '<p class="col-lg-6 btn-danger">' . $_SESSION['err'] . '</p>';
              unset($_SESSION['err']);
            }
            ?>
  </div>
  </div>
         
   <div class="row">
    <div class="col-xs-10 col-md-6">
    <form action="post.php" method="post">
    <input type="hidden" name="update_name" value="1">
    <div class="form-group">
    <label for="exampleInputPassword1">Name</label>
    <input type="text" name="name" class="form-control" id="exampleInputPassword1" placeholder="First Name"  value="<?php echo $name?>">
  </div>

  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email"  readonly class="form-control" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" value="<?php echo $email?>">
    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
  </div>
  
 
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
<div class="row">
<div class="col-xs-12 col-md-12">
<form id="change_pw" method="post" action="post.php">
<!-- 
<label for="exampleInputPassword1">Change Password</label>
    <input type="hidden" name="update_pw" value="1">
    <div class="form-group">
    <input type="password" class="form-control " placeholder="Old Password" name="password">
  </div>
  <div class="form-group">
  <input type="password" class="form-control " placeholder="New Password" name="newpassword" id="newpassword">
  </div>
  <div class="form-group">
  <input type="password" class="form-control " placeholder="Reapet Password" name="reapetpassword">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
         </div>

 </form> -->
 <div>
 <h5>Do You Want to Downgrade Instead?  <a href="member.php">Manage 	Subscriptions</a></h5>
 
</div>
  </div>
  </div>
</div>
 </div>
</div>
</div>
 </div>

  

<?php
 include('footer.php');
 ?>

</body>

</html>