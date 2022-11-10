<?php
  include('array.php');
  // session_start();
  if (empty($_SESSION['id'])) {
    header('location:index.php');
    die;
  }
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style2.css">

<head>
  <title>Changepassword  - <?php echo $GLOBALS['SITENAME'] ?></title>
  <?php include 'head.php'; ?>
</head>
<style>
  .error {
    color: red;

  }
/*
.p1{
width: 500px;
}
    */
  .err {
    color: red;
  }

  #get_stared .btn.err {
    background-color: red;
  }
</style>
</head>

<body>
  <?php include 'header.php'; ?>

  <section>

    <div class="container">
      <div class="row">

        <div class="col-md-12">
          <h2 class="text-center"> Change Password</h2>
        </div>
        <div class="d-flex justify-content-center" id="get_stared">

          <div class="get_box col-xs-12 col-md-5">
          <?php if (!empty($_SESSION['success'])) {
              echo '<p class=" col-lg-12">' . $_SESSION['success'] . '</p>';
              unset($_SESSION['success']);
            }
            if (!empty($_SESSION['err'])) {
              echo '<p class=" col-lg-12 err">' . $_SESSION['err'] . '</p>';
              unset($_SESSION['err']);
            }
            ?>
            <form id="change_pw" method="post" action="post.php">
              <input type="hidden" name="update_pw" value="1">
              <input type="password" class="form-control input" placeholder="Old Password" name="password">
              <input type="password" class="form-control input" placeholder="New Password" name="newpassword" id="newpassword">
              <input type="password" class="form-control input" placeholder="Reapet Password" name="reapetpassword">

              <button type="submit" class="btn col-lg-12 col-xs-12 c_acco" value="submit">Submit</button>
             </form>

          </div>

        </div>
      </div>
    </div>

  </section>


  


</body>

</html>

<script>
    $(document).ready(function() {
      $('#change_pw').validate({

        rules: {
          password: {
            required: true,
            minlength: 6
          },
          newpassword: {
            required: true,
            minlength: 8
          },
          reapetpassword: {
            required: true,
            equalTo : "#newpassword"
          }
        },
        messages: {
          password: {
            required: 'Please enter old password.',
            minlength: 'Password must be at least 8 characters long.',
          },
          newpassword: {
            required: 'Please enter new password.',
            minlength: 'Password must be at least 8 characters long.',
          },
          reapetpassword: {
            required: 'Please enter reapet password.',
          }

        },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });


</script>
