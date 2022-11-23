<div class="col-md-12 mt-3 mb-4">

              <form action="" method="POST" id="form">
                <input type="hidden" name="add_amount" id="add_amount" value="<?php echo $pricepay; ?>">
                <input type="hidden" name="page_type" id="page_type" value="<?php echo $page_type; ?>">
                <input type="text" class="form-control input" placeholder="Name" name="bname" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Name'">
                <?php if (!isset($_SESSION['email'])) { ?>
                  <input type="email" class="form-control input" placeholder="Email" name="email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email'">
                  <input type="Password" class="form-control input" placeholder="Password" name="password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Password'">
                <?php } ?>
                <input type="text" class="form-control input" placeholder="Card Number" name="cardnumber" maxlength="16" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Card Number'">


                <div class="flex-container">

                  <div class="flex-child">

                    <label for="expirationdate">Expiry date (MM / YY)</label>
                    <input id="expirationdate" name="expirydate" class="form-control input" placeholder="MM / YY" type="text" pattern="[0-9]*" inputmode="numeric" onfocus="this.placeholder = ''" onblur="this.placeholder = 'MM / YY'">

                  </div>

                  <div class="flex-child">
                    <label>CV CODE</label>
                    <input type="tel" class="form-control input " placeholder="CVC" name="cvc" maxlength="3" onfocus="this.placeholder = ''" onblur="this.placeholder = 'CVC'">
                  </div>

                </div>



                <div class="flex-container">
                  <p id="payment-error"></p>
                </div>
                <!-- <span class="payment_btn col-md-12 mb-3 p1 pri" name="amount"> Total = $99.99 </span>  -->

                <button type="button" class="btn payment_btn col-md-12 " value="submit" id="submitbtn">

                  Subscribe <div class="spinner-border" role="status"></div></button>

                <!-- <last image icon credit card> -->

                <div class="pmpro_submit second" style="text-align: center" ;>
                  <!-- <div class="trusted-badges-container paypal" style="display:none;">
				<img src="/wp-content/uploads/2022/03/paypal-logo-transparent.png" width="200">
			</div> -->
                  <div class="trusted-badges-container creditcard">
                    <img src="images/trusted-badge-1.png" width="300" class="creditcard_badges">
                  </div>
                  <div class="trusted-badges-container trust-logo">
                    <img src="images/trusted-badge-1.jpg" width="300">
                  </div>
                  <p class="checkout-statement">By placing your order you agree to the terms of use <a class="checkout-link" rel="noopener" style="color: #a7a7a7;text-decoration: underline;" href="https://qilife.io/members/terms-of-use.php" target="_blank">here</a></p>

                  <!-- <span id="pmpro_paypalexpress_checkout" style="display: none;">
			<input type="hidden" name="submit-checkout" value="1"><input type="hidden" name="javascriptok" value="1">
			<input type="image" class="pmpro_btn-submit-checkout" value="Check Out with PayPal »" src="https://www.qienergy.ai/wp-content/uploads/2022/03/paypay-activate-button.png">
		</span>  -->
                </div>




                <hr class="between-secure-checkout-testimonial">
                <div class="single-testimonial-before-checkout">
                  <div class="single-testimonial-image"><img src="images/jim-c.jpg" alt="Hallie Cowan"></div>
                  <div class="single-testimonial-content">
                    <p>I eliminated a daunting amount debt with Qi Coils & Inner Circle Frequencies. My mental energy became different completely after using this Qi Coil</p>
                    <span><b>Jim C,</b> Qi Coil App User</span>
                  </div>
                </div>



              </form>

            </div>
          </div>