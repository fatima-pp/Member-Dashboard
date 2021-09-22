<?php include('header_zain.php'); ?>



                <section class="sign_in_form">
                    <div class="page-heading">
                        <div class="heading black-font">
                            <h3>That should not have happened !</h3>
                        </div>
                        <br><br>
                        <div class="sec-heading">
                            <h4>
                                We are very sorry
                            </h4>
                            <h4>
                               Please try again or contact our team for assistance.
                            </h4>
                        </div>
                    </div>
                    <form action="<?php echo base_url($function_name) ;?>" method="POST">                  
                        <div class="btn-holder err_page_zain">
                            <button type="button" class="ghost-btn ghost-btn-peach-2 sign_btn" onclick="contactUs()">Contact us</button>
                            <button type="submit" class="prm-btn prm-btn-peach-2 sign_btn">Try again</button>
                        </div>

                    </form>
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

                function contactUs(){
                    window.location.href = 'https://park-pass.com/contact';
                }

            </script>
