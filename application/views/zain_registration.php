<?php include('header_zain.php'); ?>

                <?php if($this->session->flashdata('errors')){ ?>
                    <div class="error-div" id="error-div">
                        <div class="error-in-div">
                            <h4 class="msg-big">Oops!</h4>
                            <span onclick="closeError()" class="close_err">X</span>
                        </div>
                        <p class="msg-in"><?php echo $this->session->flashdata('errors'); ?></p>
                    </div>
                <?php } ?>

                <div style="display:none !important;">
                    <?php echo $client_id; ?>
                </div>

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading black-font">
                            <h3>Welcome</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Lets create your account
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('zain_sign_in_otp')?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="zain mobile number" value="<?php if(isset($mobile)){ echo $mobile;} else {echo set_value('mobile');}?>" required>
                            <label for="mobile">Mobile</label>
                            <div class="err-msg">
                                <?php echo form_error('mobile'); ?>
                            </div>
                        </div>

                        <input type="hidden" name="client_id" id="client_id" value="<?php if(isset($client_id)){echo $client_id;} ?>">
                        
                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-peach-2 sign_btn">Send OTP</button>
                        </div>

                    </form>

                </section>

            </div>

            <?php include('footer.php'); ?>
