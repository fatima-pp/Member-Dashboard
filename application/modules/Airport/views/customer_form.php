<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title Page-->
    <title>Customer Form</title>

    <!-- Icons font CSS-->
    <link href="<?php echo base_url('assets/vendor/mdi-font/css/material-design-iconic-font.min.css'); ?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/font-awesome-4.7/css/font-awesome.min.css'); ?>" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- assets/Vendor CSS-->
    <link href="<?php echo base_url('assets/vendor/select2/select2.min.css'); ?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/datepicker/daterangepicker.css'); ?>" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="<?php echo base_url('assets/css/main.css'); ?>" rel="stylesheet" media="all">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->

</head>

<style>

.ticket-qr{
    height:100% !important;
    width:35% !important;
}
.trns-card{
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
    background-color:#f8f9fa;
    padding: 2%;
    margin-bottom: 5%;
}
.car_images{
    width:50% !important;
    height:auto !important;
}
.image-row{
    display:flex;
    justify-content:space-between;
    flex-wrap:inherit !important;
}

.car_images {
  transition: transform .2s;
}

.car_images:hover {
  -ms-transform: scale(1.5); /* IE 9 */
  -webkit-transform: scale(1.5); /* Safari 3-8 */
  transform: scale(1.5); 
}

</style>

<body>
    <div class="page-wrapper bg-gra-02 p-t-130 p-b-100 font-poppins">
        <div class="wrapper wrapper--w680">
            <div class="card card-4">
                <div class="card-body">

                <section id="transaction_details">
                    <div class="trns-card bg-light text-dark">
                        <div class="trns-card-body">
                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Location</label>
                                        <p>Bahrain Airport</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Gate</label>
                                        <p>Airport Gate 1</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Date</label>
                                        <p>2020-12-31</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Time</label>
                                        <p>12:00:00 am</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Ticket</label>
                                        <p>TEST453439</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="ticket-qr">
                                    </div>
                                </div>
                            </div>

                            <div class="row image-row">
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                            </div>
                            <div class="row image-row">
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="transaction_details">
                    <div class="trns-card bg-light text-dark">
                        <div class="trns-card-body">
                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Location</label>
                                        <p>Bahrain Airport</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Gate</label>
                                        <p>Airport Gate 1</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Date</label>
                                        <p>2020-12-31</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Time</label>
                                        <p>12:00:00 am</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-space">
                                <div class="col-2">
                                    <div class="input-group">
                                        <label class="label">Ticket</label>
                                        <p>TEST453439</p>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="ticket-qr">
                                    </div>
                                </div>
                            </div>

                            <div class="row image-row">
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                            </div>
                            <div class="row image-row">
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <img src="data:image/png;base64,<?php echo $ticket_qr; ?>" alt="ticket_qr" class="car_images">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                    
                    <h2 class="title">Schedule Pick-Up Time</h2>

                    <form method="POST" action="Airports/submit_customer_form">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">first name</label>
                                    <input class="input--style-4" type="text" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">last name</label>
                                    <input class="input--style-4" type="text" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">CPR</label>
                                    <input class="input--style-4" type="text" name="cpr" required>
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Email</label>
                                    <input class="input--style-4" type="email" name="email" required>
                                </div>
                            </div>                          
                            
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Mobile</label>
                                    <input class="input--style-4" type="text" name="mobile" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Whatsapp</label>
                                    <input class="input--style-4" type="text" name="whatsapp">
                                </div>
                            </div>
                        </div>

                        <h2 class="title">Flight Details</h2>

                        <div class="input-group">
                            <label class="label">Airline</label>
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select name="airline" required>
                                    <option disabled="disabled" selected="selected">Choose option</option>
                                    <option>Gulf Air</option>
                                    <option>Emirates</option>
                                    <option>Etihad</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>


                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Arrival date</label>
                                    <div class="input-group-icon">
                                        <input class="input--style-4 js-datepicker" type="text" name="arrival_date" required>
                                        <i class="zmdi zmdi-calendar-note input-icon js-btn-calendar"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="input-group">
                                    <label class="label">Flight No</label>
                                    <input class="input--style-4" type="text" name="flight_no" required>
                                </div>
                            </div>

                        </div>

                        <div class="p-t-15">
                            <button class="btn btn--radius-2 btn--blue" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
	<script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>

    <!-- /Vendor JS-->
    <script src="<?php echo base_url('assets/vendor/select2/select2.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/datepicker/moment.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/datepicker/daterangepicker.js'); ?>"></script>

    <!-- Main JS-->
    <script src="<?php echo base_url('assets/js/global.js'); ?>"></script>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
<!-- end document-->