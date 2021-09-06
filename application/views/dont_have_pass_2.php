<?php include('header.php'); ?>


                <?php if((isset($pass_created) && ($pass_created))) { ?>
                    <div class="success-div" id="success-div">
                        <div class="success-in-div">
                            <h4 class="msg-big">Success!</h4>
                            <span onclick="closeSuccess()" class="close_succ">X</span>
                        </div>
                        <p class="msg-in">Your account has been registered. Please proceed to login</p>
                    </div>
                <?php  }?> 

                <?php if($this->session->flashdata('errors')){ ?>
                    <div class="error-div" id="error-div">
                        <div class="error-in-div">
                            <h4 class="msg-big">Oops!</h4>
                            <span onclick="closeError()" class="close_err">X</span>
                        </div>
                        <p class="msg-in"><?php echo $this->session->flashdata('errors'); ?></p>
                    </div>
                <?php } ?>

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3>Don't have a password ?</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Just a step away
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('create_account'); ?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required value="<?php echo set_value('email');?>">
                            <label for="floatingInput">Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password"  value="<?php echo set_value('password');?>" required>
                            <label for="floatingPassword">Password</label>
                        </div>

                        <div class="form-floating ">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="confirm password" required value="<?php echo set_value('confirm_password');?>">
                            <label for="floatingConfirmPassword">Confirm Password</label>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Confirm</button>
                        </div>

                    </form>

                    <div class="links">
                        <div class="forgot">
                            <a href="<?php echo base_url('forgot_pass_1');?>">Forgot Password</a>
                        </div>
                        <div class="dont_have">
                            <a href="<?php echo base_url('sign_in');?>">Back to Login</a>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>

        