<?php
error_reporting(0);
// include ('array.php');
// session_start();
if($_SERVER['REMOTE_ADDR']=='150.129.165.222'){
	//print_r($_SESSION);//exit;
}
// $str=$_SESSION['name'];

// //$first_character = substr($str, 0, 1);
// echo $str; die;
$page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
//print_r($_SERVER);
//setcookie($email, $password, time() + 2 * 24 * 60 * 60); 
?>

<script>
    jQuery(function($) {
        $('#navbarNav ul li a').filter(function() {
            var locationUrl = window.location.href;
            var currentItemUrl = $(this).prop('href');

            return currentItemUrl === locationUrl;
        }).parent('li').addClass('active');
    });
</script>

<header id="header" class="sticky-top">
    <div class="container-fluid">
        <div class="row">

            <div class="col-3">
                <div class="logo">
                    <a href="https://www.qicoil.com/frequencies/"><img src="images/qi-life-io-logo.png";> </a>
                </div>
            </div>

            <nav class="navbar navbar-expand-lg navbar-light  col-9 main-menu" id="#topheader">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
<!--                    <li class="nav-item <?= ($activePage == 'index') ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>-->
                        <li class="nav-item<?= ($page == 'inner_frequencies.php') ? ' active' : ''; ?>">
                            <a class="nav-link" href="starter-frequencies.php">Frequencies</a>
                        </li>
                        <li class="nav-item <?= ($activePage == 'tutorial') ? 'active' : ''; ?>">
                            <a class="nav-link" href="tutorial.php"> Tutorials</a>
                        </li>
                        <!-- <li class="nav-item  <?= ($activePage == 'membership') ? 'active' : ''; ?>">
                            <a class="nav-link" href="membership.php">Membership</a>
                        </li> -->
						<li class="nav-item <?= ($activePage == 'shop') ? 'active' : ''; ?>">
                            <a class="nav-link" href="https://qilifestore.com/shop" target="_blank">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($activePage == 'reviews') ? 'active' : ''; ?>" href="https://qilifestore.com/pages/reviews" target="_blank">Reviews</a>
                        </li>
                        <li class="nav-item  <?= ($activePage == 'help') ? 'active' : ''; ?>">
                            <a class="nav-link" href="https://help.qilifestore.com/en-US"  target="_blank">Help</a>
                        </li>
                        <!-- <li class="nav-item  <?= ($activePage == 'payment') ? 'active' : ''; ?>">
                                <a class="nav-link" href="payment.php">Payment</a> -->

                        <li class="nav-item dropdown">
                            <a class="nav-link" href="https://www.qicoil.com/pricing/">Subscribe</a>
                            <!--<a class="btn dropdown-toggle nav-link" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Subscribe
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="payment.php">Rife Subscription</a>
                                <a class="dropdown-item" href="quantum_payment.php">Quantum Subscription</a>
                                <a class="dropdown-item" href="higher_quantum_payment.php">Higher Quantum Subscription</a>
                                <a class="dropdown-item" href="inner_circle_link_payment.php">Inner Circle Subscription</a>

                            </div>-->
                        <!-- </li>
                        <li class="nav-item <?= ($activePage == 'tutorial') ? 'active' : ''; ?>">
                            <a class="nav-link" href="member.php">Member</a>
                        </li> -->
                        <!-- </li>  -->
                        <?php  if (isset($_SESSION['email'])) {
                        ?>

                            <li class="nav-item dropdown">
                                <button type="button" class="nav-link pro_shape dropdown-toggle dropdown" data-toggle="dropdown">
                                    <?php
                                    $str = $_SESSION['name'];
                                    $str = explode(" ", $str);
                                    //print_r($str);exit;
                                    $firstchar = substr($str[0], 0, 1);
                                    $lastchar = substr($str[1], 0, 1);

                                    echo  $firstchar . $lastchar ?></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">
                                        <?php echo $_SESSION['email']  ?>
                                    </a>
                                    <!-- <a class="dropdown-item" href="#">
                                        Photo
                                    </a> -->
                                    <a class="dropdown-item" href="profile.php">
                                     My Profile
                                    </a>
                                    <a class="dropdown-item" href="changepassword.php">
                                    Change Password
                                    </a>
                                    <a class="dropdown-item" href="logout.php">
                                        Sign Out
                                    </a>
                                </div>
                            </li>

                        <?php
                        } else {

                        ?>

                            <li class="nav-item  <?= ($activePage == 'login') ? 'active' : ''; ?>">
                                <a class="nav-link" href="index.php">Login</a>
                            </li>
<!--                            <li class="nav-item  <?= ($activePage == 'register') ? 'active' : ''; ?>">
                                <a class="nav-link" href="register.php">Register</a>
                            </li>-->
                        <?php
                        } ?>
                    </ul>
                </div>
            </nav>




        </div>
    </div>

</header>