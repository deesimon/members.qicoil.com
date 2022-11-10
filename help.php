<?php
error_reporting(0);
include ('array.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>help -</title>
  <?php include 'head.php';
  // session_start();
  if (!empty($_SESSION)) {
    header('location:frequencies.php');
    die;
  }
  ?>
   
</head>

<body>
<?php include 'header.php'; ?>

<section id="get_stared">

  <div class="container">
    <div class="row">
      <h3 class="text-center"> contact to help us </h3>

      <div class="d-flex justify-content-center">


        <div class="get_box col-md-5">
          <?php if (!empty($_SESSION['success'])) {
            echo '<p class="btn col-lg-12">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
          }
          if (!empty($_SESSION['err'])) {
            echo '<p class="btn col-lg-12 err">' . $_SESSION['err'] . '</p>';
            unset($_SESSION['err']);
          }
          ?>


<!-- <a href="https://www.qienergy.ai/support/">hiii</a> -->

           <form id="forgot" method="post" action="post.php">
            <input type="hidden" name="forgot_pw" value="1">
            <input type="text" class="form-control input" placeholder="Name" name="Name">

            <input type="hidden" name="forgot_pw" value="1">
            <input type="text" class="form-control input" placeholder="Email Address" name="email">

            <input type="hidden" name="forgot_pw" value="1">
            <input type="text" class="form-control input" placeholder="Subject" name="Subject">

           
            <textarea  class="form-control input" placeholder="Description" name="Description">
          </textarea> 

            <button type="submit" class="btn col-lg-12  col-sm-12 c_acco " value="submit"> submit</button>
          </form> -->
           <div class="flex_box justify-content-center">
            <span class="bootom_link">Already have account?</span> <a href="login.php"> Signin
            </a> </span>

          </div> 


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

        //   password: {
        //     required: true,
        //     minlength: 6
        //   }

        },
        messages: {

          email: {
            required: 'Please enter Email Address.',
            email: 'Please enter a valid Email Address.',
          },

        //   password: {
        //     required: 'Please enter Password.',
        //     minlength: 'Password must be at least 8 characters long.',
        //   }

        },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
  </script>

</body>

</html>