<?php include('header_zain.php'); ?>

                <div class="error-div" id="error-div" style="display:none;">
                    <div class="error-in-div">
                        <h4 class="msg-big">Oops!</h4>
                        <span onclick="closeError()" class="close_err">X</span>
                    </div>
                    <p class="msg-in" id="err_msg"></p>
                </div>

                <?php if($this->session->flashdata('errors')){ ?>
                    <div class="error-div" id="error-div">
                        <div class="error-in-div">
                            <h4 class="msg-big">Oops!</h4>
                            <span onclick="closeError()" class="close_err">X</span>
                        </div>
                        <p class="msg-in"><?php echo $this->session->flashdata('errors'); ?></p>
                    </div>
                <?php } ?>

                <div class="success-div" id="success-div" style="display:none;">
                    <div class="success-in-div">
                        <h4 class="msg-big">OTP sent!</h4>
                        <span onclick="closeSuccess()" class="close_succ">X</span>
                    </div>  
                    <p class="msg-in">Enter the OTP below to continue.</p>
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
                    <form action="<?php echo base_url('zain_verify_otp')?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP" value="" required>
                            <label for="otp">OTP</label>
                            <div class="err-msg">
                                <?php echo form_error('otp'); ?>
                            </div>
                        </div>
                        
                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-peach-2 sign_btn">Verify</button>
                        </div>

                        <input type="hidden" id="mobile" name="mobile" value="<?php if(isset($mobile)){ echo  $mobile; } ?>">

                    </form>

                    <?php $clnt_id = 0; if(isset($client_id)){ $clnt_id = $client_id; } ?>
                    
                    <div class="links">
                        <div class="forgot">
                            <a onclick="send_otp(this)" href="javascript:void(0);" id="resend_otp_link">Resend OTP</a>
                        </div>
                        <div class="dont_have peach">
                            <a href="<?php echo base_url($clnt_id.'/zain_sign_in');?>">Use another Number</a>
                        </div>
                    </div>

                </section>

            </div>

            <?php include('footer.php'); ?>
            <script>

                const send_otp = (e) =>{

                    console.log(e.target);
                    var mobile = document.getElementById('mobile').value;

                    var sent_otp = document.getElementById('success-div');
                    var resend_otp_link = document.getElementById('resend_otp_link');

                    if(mobile === '' || mobile === null || mobile.length < 8){
                        set_err_msg("Please ensure the mobile number is <span>correct</span>.");
                    }
                    else{
                        
                        $.ajax({
                            url: "<?php echo base_url('resend_otp')?>",
                            type: 'post',
                            data: {
                                mobile: mobile
                            },
                            dataType: 'json',
                            success: function(response) {
                                //no account found under the provided nmber
                                if(response === 1){
                                    sent_otp.style.display = "block";
                                }
                                else if(response === 0){
                                    set_err_msg("Couldn't send OTP. Please ensure the mobile number is <span>correct</span>.");
                                }
                                else{
                                    set_err_msg('Please contact IT for support');
                                }
                            },    
                            error: function (error) {
                                console.log(error.responseText);
                            }
                        });
                    }
                }

                function set_err_msg(msg){
                    var err_div = document.getElementById('error-div');
                    var err_msg = document.getElementById('err_msg');

                    err_div.style.display = 'block';
                    err_msg.innerHTML = msg;
                }

            </script>
