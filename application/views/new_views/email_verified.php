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
                                <?php  echo ($is_verified) ? 'Your email has been verified.' : 'Your email could not be verified.';?>
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url('sign_in/'.$user_email)?>" method="POST">                        

                        <div class="btn-holder">
                            <button type="submit" class="prm-btn prm-btn-blue sign_btn">Sign in</button>
                        </div>

                    </form>

                </section>

            </div>

            <?php include('footer.php'); ?>

>