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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    
    <link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">

    
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/BDF/style-header.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/BDF/style-new.css'); ?>">


    <title>ParkPass</title>
   
</head>
<body>    

  <!-- navbar -->
        <nav class="nav-wrapper-1 white z-depth-0"> 
            <div class="nav-container">

                <ul id="dropdown1" class="dropdown-content memberships-dropdown">
                    <li><a href="<?php echo base_url('buyPackage'); ?>">Valet Membership</a></li>
                    <li><a href="<?php echo base_url('membership_activation'); ?>">Parking Membership</a></li>
                </ul>
                
                <ul>
                    <a href="<?php echo base_url('home'); ?>" class="logo-1">
                        <img src="http://park-pass.com/src/new-images/parkpass-black.svg" alt="parkpass logo" class="parkpass-black-logo">
                    </a>
                </ul>

                <ul class="hide-on-med-and-down mid-nav-list">
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Booking</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdown1">Memberships<i class="material-icons right">arrow_drop_down</i></a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">Locations</a></li>
                    <li><a href="<?php echo base_url('page_under_const'); ?>">About</a></li>                   
                </ul>

                <ul class="hide-on-med-and-down right-nav-list">
                    <li><a href="<?php echo base_url('page_under_const'); ?>" class="member">I'm a <br /> Member</a></li>
                    <li>
                        <a href="<?php echo base_url('membership_activation'); ?>" class="mdc-button mdc-button--outlined black-text z-depth-0" id="join_btn_black">
                            Join
                        </a>
                    </li>                             
                </ul>
                

                <!-- mobile menu -->
                <a href="" class="sidenav-trigger menu-icon black-text"  data-target="mobile-menu">
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
                         <a href="<?php echo base_url('membership_activation'); ?>" class="btn mdc-button mdc-button--outlined black-text z-depth-0" id="join_btn">
                            Join
                        </a> 
                    </li>
                </ul>             

            </div>
        </nav>