<?php include 'array.php';
// session_start();
if($_SERVER['REMOTE_ADDR']=='150.129.165.222'){
	//print_r($_SESSION);exit;
}

 //print_r($_SESSION);?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - <?php echo $GLOBALS['SITENAME']?></title>
    <?php include 'head.php'; ?>

</head>
<style>
  .error {
    color: red;
  }
  .option {
    position: relative;
    padding-left: 20px;
    cursor: pointer;
}
</style>
<body>
<?php include 'header.php';//print_r($_SESSION);exit; ?>

         <section id="get_stared">

        <div class="container">
            <div class="row">
                <div class="d-flex justify-content-center">


                    <div class="get_box col-md-7">
                    <div class="d-flex justify-content-center"><h2>Sign Up For a Free Account</h2></div>
                    <div class="d-flex justify-content-center"><h3>Get 7 Meditation Frequencies FREE ($197 Value)</h3></div><br>

                <center><img src="images/the-free-freq.png" style="max-width:247px; max-height:502px"/></center>

                    <form  id="sign_up" method="post" action="post.php">

                    <input type="hidden"  name="register" value="1">

                    <?php if(!empty($_SESSION['err'])){
                           echo '<p style="color:red";>'.$_SESSION['err'].'</p>';
                            unset($_SESSION['err']);
                    }
                      ?>
                    <input type="text" class="form-control input" placeholder="Name" name="name">



                        <input type="text" class="form-control input" placeholder="Email" name="email">

                       <input type="password" class="form-control input" placeholder="Password" name="password">


                       <!-- <div class="row">
                      <div class="col-md-6 mt-md-0 mt-3">
                    <label>Birthday</label>
                    <input type="date" class="form-control" name="dateofbirth" required >
                </div>
                <div class="col-md-6 mt-md-0 mt-3">
                    <label class="align-items-center">Gender</label>
                    <div class="align-items-center mt-2">
                        <label class="option">
                            <input type="radio" name="gender" value="1">&nbsp;Male
                            <span class="checkmark"></span>
                        </label>
                        <label class="option ms-4">
                            <input type="radio" name="gender" value="2">&nbsp;Female
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div> -->


                       <button type="submit" class="btn col-lg-12  col-sm-12 c_acco ">
                                Sign Up</button>

                    </form>

                        <!-- <div class="flex_box">

                            <span class="line_left"> </span>
                            <h5>or sign in with </h5> <span class="line_right"> </span>

                        </div>


                        <div class="flex_box social_box">


                            <button type="submit" class="btn btn-primary google "> <i class="fa fa-google"></i>
                                Continue with Google</button>



                            <button type="submit" class="btn btn-primary facebook "><i class="fa fa-facebook"></i>
                                Continue with facebook</button>

                        </div> -->


                        <div class="flex_box justify-content-center">
                            <span class="bootom_link">Already have an account?</span> <a href="index.php"> Login </a> </span>

                        </div>
                        <div class="flex_box justify-content-center"> <a href="forgot.php">Forgotten Password?</a> </div>

                    </div>

                </div>
            </div>
        </div>

         </section>


         <script>
  $(document).ready(function () {

    $.validator.addMethod("letters_numbers_special", function(value, element) {
        return this.optional(element) || /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*])[a-zA-Z0-9!@#$%&*]+$/i.test(value);
    }, "A minimum 8 characters password contains a combination of special character and number");

  $('#sign_up').validate({
      rules: {
        name: {
          required: true
        },
        email: {
          required: true,
          email: true
        },

        password: {
          required: true,
          minlength: 8,
        //  letters_numbers_special: true
        }

      },
      messages: {
        name: 'Please enter name',
        email: {
          required: 'Please enter Email Address',
          email: 'Please enter a valid Email Address',
        },

        password: {
          required: 'Please enter Password',
          minlength: 'Password must be at least 8 characters long',
        }

      },
      submitHandler: function (form) {
        form.submit();
      }
    });
  });
</script>
</body>

</html>
