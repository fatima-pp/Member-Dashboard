<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">   
    <!-- font awesome -->    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <!--Import Google Icon Font-->
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

   
    <title>ParkPass</title>
   
</head>
<body>    

  <!-- navbar -->
        <nav class="nav-wrapper-1 transparent z-depth-0"> 
            <div class="container-1">

                <ul id="dropdown1" class="dropdown-content memberships-dropdown">
                    <li><a href="<?php echo base_url('buyPackage'); ?>">Valet Membership</a></li>
                    <li><a href="<?php echo base_url('membership_activation'); ?>">Parking Membership</a></li>
                </ul>
                
                <ul class="left">
                    <a href="<?php echo base_url('home'); ?>" class="logo-1">
                        <img src="http://park-pass.com/src/new-images/ParkPassFinalWhiteLogo.png" alt="parkpass logo" class="parkpass-white-logo-1">
                    </a>
                </ul>

                <ul class="center hide-on-med-and-down">
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Booking</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdown1">Memberships<i class="material-icons right">arrow_drop_down</i></a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Locations</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">About</a></li>                   
                </ul>

                <ul class="right hide-on-med-and-down">
                    <li><a href="<?php echo base_url('page_under_const'); ?>" class="member">I'm a <br /> Member</a></li>
                    <li>
                        <a href="<?php echo base_url('membership_activation'); ?>" class="btn mdc-button mdc-button--outlined white-text z-depth-0" id="join_btn_blue">
                            Join
                        </a>
                    </li>                             
                </ul>
                

                <!-- mobile menu -->
                <a href="" class="sidenav-trigger  menu-icon"  data-target="mobile-menu">
                    <i class="material-icons">menu</i>
                </a>
                
                <ul class="sidenav" id="mobile-menu">
                    <li><a class="sidenav-close" href="#!">x</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Booking</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Memberships</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Locations</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">About</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>" class="normal-18">I'm a Member</a></li>                                   

                    <li>
                         <a href="<?php echo base_url('membership_activation'); ?>" class="btn mdc-button mdc-button--outlined white-text z-depth-0" id="join_btn">
                            Join
                        </a> 
                    </li>
                </ul>             

            </div>
        </nav>