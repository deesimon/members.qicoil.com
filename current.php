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
        <div class="col-md-10" style="background-color: white;">
          <div class="row">
            <div class="col-md-12">
             
             </div>
            </div>
            <div class="row">
            <div class="col-xs-6 col-md-6">
            <form>
            <h5 class="main-title "><b>why are you downgradding your loom print?</b></h5>
            <h5> check any and all reasons</h5>
            <div>
            <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 Didn't have features or integrations i needed
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck">
                 <label class="form-check-label" for="defaultCheck2">
                 This product no longer fits my needs
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 Reliability or performance concerns
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 The pricing wasn't worth the value
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 I couldn't figure out how to use it
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 Customer service was not satisfactory
                </label>
           </div>
           <div class="form-check">
                 <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                 <label class="form-check-label" for="defaultCheck1">
                 Another reason
           </div>
               
            </div>
         </div> 
       </div>
    
</form>
<button type="submit" class="btn btn-primary btn-sm">Complete downgrade</button>
    <a href="question.php" class="btn btn-primary btn-sm " role="button" aria-pressed="true">Back to prev</a>
</div>
</div>
</div>
</div>
</div>
</div> </div>

  

<?php
 include('footer.php');
 ?>

</body>

</html>