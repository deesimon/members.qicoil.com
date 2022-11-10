<?php

if (empty($_GET) || empty($_GET['q'])) {
  header('location:forgot.php');
  die;
}
$id = base64_decode($_GET['q']);
//echo $id;
//die;
?>


<!DOCTYPE html>
<html lang="en">

<head>
<title>Newpassword - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>

</head>
<style>
  .error {
    color: red;

  }

  .err {
    color: red;
  }
</style>
</head>

<body>
  <?php include 'header.php'; ?>

  <section id="get_stared">

    <div class="container">
      <div class="row">
        <h3 class="text-center"> Change Your Password </h3>

        <div class="d-flex justify-content-center">


          <div class="get_box col-md-5">
            <form id="change_pw" method="post" action="post.php">
              <input type="hidden" name="change_pw" value="1">
              <input type="hidden" name="id" value="<?php echo $id; ?>">
              <input type="password" class="form-control input" placeholder="Enter New Password" name="password" id="password">
              <input type="password" class="form-control input" placeholder="Enter Confirm Password" name="confirm_password">
              <button type="submit" class="btn col-lg-12  col-sm-12 c_acco " value="submit"> Change Password</button>
            </form>

          </div>

        </div>
      </div>
    </div>

  </section>

  <script>
    $(document).ready(function() {
      $('#change_pw').validate({
        rules: {

          password: {
            required: true,
            minlength: 8
          },

          confirm_password: {
            required: true,
            equalTo: "#password"
          }

        },
        messages: {

          password: {
            required: 'Please enter new password.',
            minlength: 'Password must be at least 8 characters long.',
          },

          confirm_password: {
            required: 'Please enter confirm password.',
          }

        },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
  </script>



</body>

</html>