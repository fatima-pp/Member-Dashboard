<?php include('header.php'); ?>

                <?php if((isset($is_changed) && ($is_changed))) { ?>
                    <div class="success-div" id="success-div">
                        <div class="success-in-div">
                            <h4 class="msg-big">Success!</h4>
                            <span onclick="closeSuccess()" class="close_succ">X</span>
                        </div>
                        <p class="msg-in">Password changed successfully!</p>
                        <p class="msg-in">Back to <span> <a href="<?php echo base_url('sign_in'); ?>">Login</a> </span>!</p>
                    </div>
                <?php  }?> 

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3>Forgot password ?</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Just a step away
                            </h4>
                        </div>
                    </div>

                    <div class="err-msg">
                        <?php echo $this->session->flashdata('errors'); ?>
                    </div>

                    <form action="<?php echo base_url('set_new_pass'); ?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" value="<?php echo set_value('password'); ?>" >
                            <label for="floatingPassword">Password</label>

                        </div>

                        <div class="form-floating ">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="confirm password" value="<?php echo set_value('confirm_password'); ?>" >
                            <label for="floatingConfirmPassword">Confirm Password</label>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Confirm</button>
                        </div>

                        <input type="hidden" value="<?php if(isset($account_id)){ echo $account_id;} ?>" id="account_id" name="account_id">
                    </form>

                    <div class="links">
                        <div class="dont_have">
                            <a href="<?php echo base_url('sign_in');?>">Back to Login</a>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>
        