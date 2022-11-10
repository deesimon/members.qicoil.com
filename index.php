<?php
   include('array.php');
  // session_start();
  if (!empty($_SESSION['id'])) {
    header('location:starter-frequencies.php');
    die;
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login - <?php echo $GLOBALS['SITENAME']?></title>
  <?php include 'head.php'; ?>
</head>
<style>
  .error {
    color: red;

  }

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
          <h2 class="text-center"> QUANTUM & RIFE FREQUENCY WEBAPP (BETA) </h2>
        </div>
        <div class="d-flex justify-content-center" id="get_stared">

          <div class="get_box col-md-5">

            <?php //echo '111---'; print_r($_SESSION);
            if (!empty($_SESSION['err'])) {
              echo '<p class=" col-lg-12 err">' . $_SESSION['err'] . '</p>';
              unset($_SESSION['err']);
            }
            ?>
            <?php if (!empty($_SESSION['success'])) {
              echo '<p class=" col-lg-12">' . $_SESSION['success'] . '</p>';
              unset($_SESSION['success']);
            }
            ?>

            <form id="login" method="post" action="post.php">
              <input type="hidden" name="login" value="1">
              <input type="text" class="form-control input" placeholder="Email" name="email">

              <input type="password" class="form-control input" placeholder="Password" name="password">

              <button type="submit" class="btn col-lg-12  col-sm-12 c_acco" value="submit"> Sign in </button>

              <!-- <div class="flex_box">
                <span class="line_left"> </span>
                <h5>or sign in with </h5> <span class="line_right"> </span>
              </div>
            </form>

            <div class="flex_box social_box">
              <button type="submit" class="btn btn-primary google"> <i class="fa fa-google"></i>
                Continue with Google</button>
              <button type="submit" class="btn btn-primary facebook"> <i class="fa fa-facebook"></i>
                Continue with facebook</button>

            </div> -->

            <div class="flex_box justify-content-center">
              <span class="bootom_link">Don't have an account?</span> <a href="register.php"> Sign Up
              </a> </span>

            </div>
            <div class="flex_box justify-content-center"> <a href="forgot.php"> Forgotten Password?</a> </div>

          </div>

        </div>
      </div>
    </div>

  </section>

  <script>
    $(document).ready(function() {
      $('#login').validate({
        rules: {

          email: {
            required: true,
            email: true
          },

          password: {
            required: true,
            minlength: 6
          }

        },
        messages: {

          email: {
            required: 'Please enter Email Address.',
            email: 'Please enter a valid Email Address.',
          },

          password: {
            required: 'Please enter Password.',
            minlength: 'Password must be at least 8 characters long.',
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
