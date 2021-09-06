            <footer>
                <div class="logo-row">
                    <section class="social">
                        <div class="logo-holder-footer">
                            <img src="<?php echo base_url('assets/images/ParkPass_White_Stacked.svg'); ?>" alt="ParkPass Logo">
                        </div>
                        <ul class="company-logos">
                            <li>
                                <a class="btn-floating btn-small black" href="#">
                                    <img src="<?php echo base_url('assets/images/facebook.svg'); ?>" alt="facebook logo">                        
                                </a>
                            </li>
                            <li>
                                <a class="btn-floating btn-small black" href="#">
                                    <img src="<?php echo base_url('assets/images/instagram.svg'); ?>" alt="instagram logo"> 
                                </a>
                            </li>
                            <li>
                                <a class="btn-floating btn-small black " href="https://www.instagram.com/park_pass/">
                                    <img src="<?php echo base_url('assets/images/twitter.svg'); ?>" alt="twitter logo"> 
                                </a>
                            </li>
                        </ul>
                    </section>
                </div>

                <!-- <div class="social-contact-row"> -->
                    <section class="contact">
                        <h5 class="contact-title">Contact Us</h5>
                        <div class="contact-info">
                            <p>+973 3888 8231</p>
                            <p>+973 1700 2299</p>
                            <p>info@park-pass.com</p>
                        </div>
                    </section>
                    <section class="office">
                        <h5 class="location-title">Our Office</h5>
                        <div class="location-info">
                            <p>Office 1602, Entrance 614,</p>
                            <p>Road 1011, Sanabis 0410,</p>
                            <p>Capital Governorate.</p>
                        </div>
                    </section>


                <!-- </div> -->
            </footer>
            </div>
        <script src="https://code.jquery.com/jquery-3.4.0.js" integrity="sha256-DYZMCC8HTC+QDr5QNaIcfR7VSPtcISykd+6eSmBW5qo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js" type="text/javascript"></script>
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
        <script src="https://anijs.github.io/lib/anijs/anijs.js"></script>
        <script src="https://anijs.github.io/lib/anijs/helpers/dom/anijs-helper-dom-min.js"></script>
        
        <script>
            $(document).ready(function(){
                $(".dropdown-trigger").dropdown();
                
                var sidebar_elem = document.querySelector('.sidenav-close');
                var sidebar_inst = M.Sidenav.getInstance(sidebar_elem);

                $('.sidenav').sidenav(
                    {
                        edge:'right',
                        inDuration:300
                    }
                );


                $('.materialboxed').materialbox();
                $('.parallax').parallax();
                $('.tabs').tabs();
                $('.datepicker').datepicker({
                    disableWeekends:true
                });
                $('.tooltipped').tooltip();
                $('.scrollspy').scrollSpy();
                $('select').formSelect();

                var elem = document.querySelector('.tabs');
                var options = {}
                var instance = M.Tabs.init(elem, options);


                // $(".sidenav-trigger").hover(
                //     function() {  
                //         sidebar_inst.open();
                //     },
                //     setTimeout(()=>{
                //         sidebar_inst.close();
                //     },1000)
                // );

            });

            function closeError() {
                $error_div = document.getElementById('error-div'); 
                $error_div.style.display = 'none';
            }

            function closeSuccess() {
                $success_div = document.getElementById('success-div'); 
                $success_div.style.display = 'none';
            }
        </script>
    </body>
    
</html>