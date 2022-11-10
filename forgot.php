<?php 
// session_start();
 include 'array.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
<title>Forgot - <?php echo $GLOBALS['SITENAME']?></title>
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

  <section id="get_stared">

    <div class="container">
      <div class="row">
        <h3 class="text-center"> Reset Your Password </h3>

        <div class="d-flex justify-content-center">


          <div class="get_box col-md-5">
            <?php if (!empty($_SESSION['success'])) {
              echo '<p class="col-lg-12">' . $_SESSION['success'] . '</p>';
              unset($_SESSION['success']);
            }
            if (!empty($_SESSION['err'])) {
              echo '<p class="col-lg-12 err">' . $_SESSION['err'] . '</p>';
              unset($_SESSION['err']);
            }
            ?>
            <form id="forgot" method="post" action="post.php">
              <input type="hidden" name="forgot_pw" value="1">
              <input type="text" class="form-control input"  placeholder="Your Email Address" name="email">
             
           
              <button type="submit" class="btn col-lg-12  col-sm-12 c_acco " value="submit"> Reset My Password</button>
            </form>

            <div class="flex_box justify-content-center">
              <span class="bootom_link">Already have account?</span> <a href="index.php"> Login
              </a> 

            </div>


          </div>

        </div>
      </div>
    </div>

  </section>

  <script>
    $(document).ready(function() {
      $('#forgot').validate({
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


//   target: '#preview', 
//   success: function() { 
//   $('#formbox').slideUp('fast'); 
// }

//     $().ready(function() {
//     $("#form").validate({ 
//     });
// });


</script>
</body>
</html>