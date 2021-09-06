<?php include('header.php'); ?>

                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading primary">
                            <h3> <?php echo (isset($title)) ? $title : 'Any help?'; ?></h3>
                        </div>
                        <div class="sec-heading">
                            <h4>
                                Where were you headed?
                            </h4>
                        </div>
                    </div>

                    <div class="links">
                        <div class="forgot">
                            <button class="ghost-btn ghost-btn-blue sign_btn"><a href="<?php echo base_url('forgot_pass_1');?>">Forgot Password</a></button>
                        </div>
                        <div class="dont_have">
                            <button class="prm-btn prm-btn-blue sign_btn"><a href="<?php echo base_url('dont_have_pass');?>">Don't have a Password</a></button>
                        </div>
                    </div>
                </section>

            </div>

            <?php include('footer.php'); ?>
        