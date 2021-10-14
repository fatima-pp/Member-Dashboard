<?php include('header.php'); ?>

                <?php isset($data) ? print_r($data) : ''; ?>
                
                <?php if((isset($not_registered) && !($not_registered))) { ?>
                    <div class="error-div" id="error-div">
                        <div class="error-in-div">
                            <h4 class="msg-big">Oops!</h4>
                            <span onclick="closeError()" class="close_err">X</span>
                        </div>
                        <p class="msg-in">We couldn’t find an account matching the email. Please ensure the email is <span>registered</span>.</p>
                    </div>
                <?php  }?> 

                <?php if((isset($not_verified) && !($not_verified))) { ?>
                    <div class="error-div" id="error-div">
                        <div class="error-in-div">
                            <h4 class="msg-big">Oops!</h4>
                            <span onclick="closeError()" class="close_err">X</span>
                        </div>
                        <p class="msg-in">Please ensure the email is <span>verified</span>.Check your inbox for a verification link.</p>
                    </div>
                <?php  }?> 

                <?php if((isset($is_mail_sent) && ($is_mail_sent))) { ?>
                    <div class="success-div" id="success-div">
                        <div class="success-in-div">
                            <h4 class="msg-big">Success!</h4>
                            <span onclick="closeSuccess()" class="close_succ">X</span>
                        </div>
                        <p class="msg-in">An email has been sent with password reset instructions.</p>
                    </div>
                <?php  }?> 


                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3>Forgot Password</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Easily reset with email or phone
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('forgot_pass'); ?>" method="POST">

                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="<?php echo set_value('email'); ?>" required>
                            <label for="floatingInput">Email</label>
                            <div class="err-msg">
                                <?php echo $this->session->flashdata('errors'); ?>
                            </div>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Reset Password</button>
                        </div>

                    </form>

                    <div class="links">
                        <div class="forgot">
                            <a href="<?php echo base_url('sign_in');?>">Back to Login</a>
                        </div>
                        <div class="dont_have">
                            <a href="<?php echo base_url('forgot_pass_2');?>">Reset using Mobile</a>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>

        