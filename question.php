<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(2);
include('array.php');
include('constants.php');
// session_start();
if (!isset($_SESSION['email'])) {
  header('Location:index.php');
  exit;
}
$id=$_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Profile  - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
  <style>
.closebtn{
  color: red;
    font-size: 17px;
    font-weight: normal;
    padding-right: 10px;
}
  </style>
</head>
<?php include 'header.php'; ?>

<body id="con_listing">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-10" style="background-color: white;">
          <div class="row">
            <div class="col-md-12">
                <!-- <h5>TDY Marketing</h5> -->
              
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6 col-md-6">
            <h5 class="main-title "><b>You'all also no longer be  able to use three features</b></h5>
            <div>
                <ul>
               
                <li><i class="fa fa-close closebtn"></i>Essential Frequencies for Wellness and Meditation </li>
                <li> <i class="fa fa-close closebtn"></i>2-3 Dimensional</li>
                <li> <i class="fa fa-close closebtn"></i>822+ Quantum Frequencies</li>
               </ul>
            </div>
            <div>
            <a href="starter-frequencies.php" class="btn btn-primary btn-md  " role="button" aria-pressed="true">Keep My current Plan</a><br>
            <small><a href="current.php?id=<?php echo ($id); ?>" class="" role="button" aria-pressed="true">Contiue WIth Downgrade</a></small>
          </div>

            <!-- <a href="starter-frequencies.php" class="btn btn-primary btn-md active " role="button" aria-pressed="true">Keep My current Plan</a>
        <a href="currant.php" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Contiue WIth Downgrade</a> -->

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