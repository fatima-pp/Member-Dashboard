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

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading black-font">
                            <h3>Register</h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Lets create your account
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('zain_activate')?>" method="POST">

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Ali" value="<?php if(isset($name)){ echo $name;} else {echo set_value('name');}?>" required>
                            <label for="name">Name</label>
                            <div class="err-msg">
                                <?php echo form_error('name'); ?>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@email.com" value="<?php if(isset($email)){ echo $email;} else {echo set_value('email');}?>" required>
                            <label for="email">Email</label>
                            <div class="err-msg">
                                <?php echo form_error('email'); ?>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                                <label class="form-check-label" for="male">Male</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="female" >
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>

                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="dob" name="dob" placeholder="Ali" value="<?php if(isset($dob)){ echo $dob;} else {echo set_value('dob');}?>" required>
                            <label for="dob">Date of birth</label>
                            <div class="err-msg">
                                <?php echo form_error('dob'); ?>
                            </div>
                        </div>

                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" value="<?php echo set_value('password');?>" required>
                            <label for="password">Pick a strong password</label>
                            <small id="emailHelp" class="form-text text-muted">Must be atleast 6 characters with atleast 1 digit.</small>

                            <div class="err-msg">
                                <?php echo form_error('password'); ?>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="confirm_password" value="<?php echo set_value('confirm_password');?>" required>
                            <label for="confirm_password">Confirm Password</label>
                            <div class="err-msg">
                                <?php echo form_error('confirm_password'); ?>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="show_pass" onclick="show_password()" style="border-radius:0 !important;">
                            <label class="form-check-label" for="show_pass">
                                Show Password
                            </label>
                        </div>

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-peach-2 sign_btn">Activate</button>
                        </div>

                    </form>

                    <?php $clnt_id = 0; if(isset($client_id)){ $clnt_id = $client_id; } ?>

                    <input type="hidden" name="mobile" id="mobile" value="<?php if(isset($mobile)){echo $mobile;} ?>">

                    <div class="links">
                        <div class="forgot">
                            <a href="<?php echo base_url($clnt_id.'/zain_sign_in');?>">Use another number </a>?
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>

            <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

            <script>
                
                $(document).ready(function() 
                {

                    $('#dob').daterangepicker(
                    {  
                        autoUpdateInput: true,
                        singleDatePicker: true,
                        showDropdowns: true,
                        minYear: 1901,
                        maxYear: parseInt(moment().format('YYYY'),10),
                        maxDate:new Date(new Date().getTime() ),
                        locale:
                        {
                            format: 'DD-MM-YYYY',cancelLabel: 'Clear',
                        },
                        autoclose:true
                    });
                });

                function show_password(){
                    var x = document.getElementById("password");
                    var y = document.getElementById("confirm_password");
                    if (x.type === "password") {
                        x.type = "text";
                        y.type = "text";
                    } else {
                        x.type = "password";
                        y.type = "password";
                    }
                }

            </script>
