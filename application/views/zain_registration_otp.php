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


                <?php var_dump($zain_dtls);?>
                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading peach">
                            <h3>Welcome</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Lets create you account
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('sign_in_sub')?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="zain mobile number" value="<?php if(isset($email)){ echo $email;} else {echo set_value('mobile');}?>" required>
                            <label for="mobile">Mobile</label>
                            <div class="err-msg">
                                <?php echo form_error('mobile'); ?>
                            </div>
                        </div>
                        
                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-peach-2 sign_btn">Continue</button>
                        </div>

                    </form>

                </section>

            </div>

            <?php include('footer.php'); ?>
