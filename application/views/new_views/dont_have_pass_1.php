<?php include('header.php'); ?>
                
                <div class="error-div" id="error-div" style="display:<?php echo ($this->session->flashdata('errors')) ? 'block' : 'none'; ?>">
                    <div class="error-in-div">
                        <h4 class="msg-big">Oops!</h4>
                        <span onclick="closeError()" class="close_err">X</span>
                    </div>
                    <p class="msg-in" id="err_msg">
                        <?php echo 
                            ($this->session->flashdata('errors')) ? 
                            $this->session->flashdata('errors') : ''; 
                        ?>
                    </p>
                </div>

                <div class="success-div" id="success-div" style="display:none;">
                    <div class="success-in-div">
                        <h4 class="msg-big">Password found!</h4>
                        <span onclick="closeSuccess()" class="close_succ">X</span>
                    </div>
                    <p class="msg-in">You have a registered password.Would like to reset ?</p>
                    <p class="msg-in"><a href="<?php echo base_url('forgot_pass_2'); ?>">Reset Password</a></p>
                </div>

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3>Dont have a password</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Create one now
                            </h4>
                        </div>
                    </div>

                    <form action="<?php echo base_url('create_pass'); ?>" method="POST">

                        <div class="mobile_otp">

                            <div class="form-floating mb-3 mobile">
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="39000000" required  value="<?php echo set_value('phone');?>">
                                <label for="floatingInput">Mobile Number</label>
                            </div>

                            <a onclick="send_otp(this)" href="javascript:void(0);" class="send_otp_link" id="otp_link">Send OTP</a>
                            <a href="javascript:void(0);" class="send_otp_link otp_sent" id="sent_otp" style="display: none;">OTP Sent!</a>
                            
                        </div>

                        <div class="form-floating">
                            <input type="number" class="form-control" id="otp" name="otp" placeholder="OTP" required >
                            <label for="floatingOTP">OTP</label>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Confirm</button>
                        </div>

                    </form>

                    <div class="links">
                        <div class="forgot resend_link">
                            Didn't receive OTP ?<a onclick="send_otp(this)" href="javascript:void(0);" id="resend_otp_link">Resend OTP</a>
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
                                if(response == '-1'){
                                    set_err_msg("We couldn’t find an account matching the mobile number. Please ensure the mobile is <span>registered</span>.");
                                }
                                else if(response == 2){
                                    set_succ_msg();
                                }
                                else if(response == 0){
                                    set_err_msg("Couldn't send OTP. Please ensure the mobile number is <span>correct</span>.");
                                }
                                else{
                                    closeError();
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

                function set_succ_msg(){
                    var succ_div = document.getElementById('success-div');                    
                    succ_div.style.display = 'block';                    
                }


            </script>
        