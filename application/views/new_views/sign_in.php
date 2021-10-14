<?php include('header.php'); ?>

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
                            <h3>Welcome</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Sign in to continue
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('sign_in_sub')?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email_phone" name="email_phone" placeholder="name@example.com" value="<?php if(isset($email)){ echo $email;} else {echo set_value('email_phone');}?>" required>
                            <label for="email_phone">Email or Phone</label>
                            <div class="err-msg">
                                <?php echo form_error('email_phone'); ?>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" value="<?php echo set_value('password');?>" required>
                            <label for="password">Password</label>
                            <div class="err-msg">
                                <?php echo form_error('password'); ?>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="show_pass" onclick="show_password()" style="border-radius:0 !important;">
                            <label class="form-check-label" for="show_pass">
                                Show Password
                            </label>
                        </div>


                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Sign in</button>
                        </div>

                    </form>

                    <div class="links">
                        <div class="forgot">
                            <a href="<?php echo base_url('forgot_pass_1');?>">Forgot Password</a>
                        </div>
                        <div class="dont_have">
                            <a href="<?php echo base_url('dont_have_pass');?>">Don't have a Password</a>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>

             <script>

                function show_password(){
                    var x = document.getElementById("password");
                    if (x.type === "password") {
                        x.type = "text";
                    } else {
                        x.type = "password";
                    }
                }

            </script>