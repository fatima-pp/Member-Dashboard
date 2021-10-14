<?php include('header.php'); ?>

<style>


</style>
                <div class="error-div" id="error-div" style="display:none">
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




                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3>Forgot password</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Easily reset with email or phone
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('get_token_set_new_pass'); ?>" method="POST" id="OTP_form">

                        <div class="mobile_otp">
                            <div class="form-floating mb-3 mobile">
                                <input type="number" class="form-control inputs" id="phone" name="phone" placeholder="39000000" required value="<?php echo isset($mobile) ? $mobile : set_value('phone'); ?>">
                                <label for="floatingInput">Mobile Number</label>
                            </div>
                            
                            <a onclick="send_otp(this)" href="javascript:void(0);" class="send_otp_link" id="otp_link">Send OTP</a>
                            <a href="javascript:void(0);" class="send_otp_link otp_sent" id="sent_otp" style="display: none;">OTP Sent !</a>
                            
                        </div>

                        <div class="form-floating">
                            <input type="number" class="form-control" id="otp" name="otp" placeholder="password" required>
                            <label for="floatingOTP">OTP</label>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Confirm</button>
                        </div>

                    </form>


                    <div class="links">
                        <div class="forgot">
                            <a onclick="send_otp(this)" href="javascript:void(0);" id="resend_otp_link">Resend OTP</a>
                        </div>
                        <div class="dont_have">
                            <a href="<?php echo base_url('forgot_pass_1');?>">Reset using Email</a>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>
        
            <script>
                const send_otp = (e) =>{

                    console.log(e.target);
                    var mobile = document.getElementById('phone').value;

                    var otp_link = document.getElementById('otp_link');
                    var sent_otp = document.getElementById('sent_otp');
                    var resend_otp_link = document.getElementById('resend_otp_link');

                    if(mobile.length < 8){
                        set_err_msg(" Please ensure the mobile number is <span>correct</span>.");
                    }
                    else{
                        
                        $.ajax({
                            url: "<?php echo base_url('verify_send_otp')?>",
                            type: 'post',
                            data: {
                                mobile: mobile
                            },
                            dataType: 'json',
                            success: function(response) {
                                //no account found under the provided nmber
                                if(response == '-1'){
                                    set_err_msg("We couldn’t find an account matching the mobile number. Please ensure the mobile is <span>registered</span>.");
                                }

                                else if(response == 0){
                                    set_err_msg("Couldn't send OTP. Please ensure the mobile number is <span>correct</span>.");
                                }
                                else{
                                    e.id == 'resend_otp_link' ? change_btn_link('','',resend_otp_link) : change_btn_link(otp_link,sent_otp,'');
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



                function change_btn_link(otp_link,sent_otp,resend_otp_link){
                    if(resend_otp_link == ''){
                        otp_link.style.display = 'none';
                        sent_otp.style.display = 'block';
                        setTimeout(function(){ 
                            otp_link.style.display = 'block';
                            sent_otp.style.display = 'none';
                        }, 60000);
                    }
                    else{
                        resend_otp_link.innerHTML = 'OTP Sent!';
                        setTimeout(function(){ 
                           resend_otp_link.innerHTML = 'Resend OTP';
                        }, 60000);
                    }
                }


            </script>