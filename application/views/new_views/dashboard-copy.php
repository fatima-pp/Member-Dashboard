<?php include('header-new.php'); ?>         
<?php include('sidebar.php'); ?>         

                <section class="sign_in_form mem_dashboard">
                    
                <div class="page-heading" style="margin-bottom:1.5rem !important">
                        <div class="main-heading black-font wlcm-hdg">
                            <h1>Welcome <?php if(isset($customer)){echo $customer['first_name'];} ?> ,</h1>
                        </div>
                    </div>

                    <div class="page-heading" style="margin-bottom:1.5rem !important">
                        <div class="sec-heading primary">
                            <h3>My Account</h3>
                        </div>
                    </div>

                    <div class="card profile-card">
                        <div class="card-header flexed_spc_btn" >
                            Member Profile
                            <img src=" <?php echo base_url('assets/images/arrow-down-blue.svg'); ?>" alt="arrow down" class="arrow">
                        </div>
                        <div class="card-body">
                            <ul>

                                <li class="dsktp-first-child">
                                    <div class="member_id primary">
                                        <?php if(isset($customer)){echo $customer['customers_id'];} ?>
                                    </div>
                                    <div class="edit_btn">
                                        <a href="#"> Edit
                                            <img src="<?php echo base_url('assets/images/edit_icon.svg'); ?>" alt="edit icon">
                                        </a>
                                    </div>
                                </li>

                                <li class="mob-first-child">
                                    <div class="label">
                                        Membership ID
                                    </div>
                                    <div class="value">
                                       <?php if(isset($customer)){echo $customer['customers_id'];} ?>
                                    </div>
                                </li>

                                <div class="acc_info name-div">
                                    <li>
                                        <div class="label">
                                            First Name
                                        </div>
                                        <div class="value">
                                            <?php if(isset($customer)){echo $customer['first_name']; } ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">
                                            Last Name
                                        </div>
                                        <div class="value">
                                             <?php if(isset($customer)){echo $customer['last_name']; } ?>
                                        </div>
                                    </li>
                                </div>

                                <div class="acc_info cpr-gen">
                                    <li>
                                        <div class="label">
                                            CPR
                                        </div>
                                        <div class="value">
                                             <?php if(isset($customer)){echo $customer['CPR']; } ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">
                                            Gender
                                        </div>
                                        <div class="value">
                                             <?php if(isset($customer)){echo $customer['gender']; } ?>
                                        </div>
                                    </li>
                                </div>

                                <div class="acc_info email-mob">
                                    <li>
                                        <div class="label">
                                            Email
                                        </div>
                                        <div class="value">
                                             <?php if(isset($customer)){echo $customer['email']; } ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">
                                            Mobile
                                        </div>
                                        <div class="value">
                                             <?php if(isset($customer)){echo $customer['mobile_number']; } ?>
                                        </div>
                                    </li>
                                </div>

                                <div class="acc_info dept-prof">                               
                                    <li>
                                        <div class="label">
                                            Department
                                        </div>
                                        <div class="value">
                                            <?php if(isset($dept) && $dept) {echo $dept['department'];} ?>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">
                                            Profession
                                        </div>
                                        <div class="value">
                                            <?php if(isset($dept) && $dept) {echo $dept['profession'];} ?>
                                        </div>
                                    </li>
                                </div>

                            </ul>
                        </div>
                    </div>

                    <div class="page-heading" style="margin-bottom:1.5rem !important">
                        <div class="sec-heading primary">
                            <h3>My Memberships</h3>
                        </div>
                    </div>
                    <!-- <?php var_dump($memberships); ?> -->

                    <div class="memberships_row">
                    <?php foreach($memberships as $membership){ ?>

                        <div class="card mem_card">

                            <div class="card-body membership-card">
                                <h3 class="membership_heading">
                                    <?php  echo $membership['membership_name']; ?>
                                </h3>
                                <h6 class="location">
                                    <?php  echo $membership['location']; ?>
                                </h6>

                                <div class="right-sec">
                                    <img src="<?php echo $membership['logo']; ?> " alt="tag" class="tag"> 
                                </div>

                                <p class="info">
                                    <?php echo $membership['description']; ?>
                                </p>

                                <p class="status">
                                     <?php echo $membership['validity']; ?>
                                </p>

                                <img src="<?php echo base_url('assets/images/image-holder.svg'); ?>" alt="image-holder" style="margin: 0 auto">

                                <div class="card-btns peach flexed_spc_btn">
                                    <div class="btn ghost-btn ghost-btn-peach">
                                        <a href="<?php echo base_url('sign_in');?>">View</a>
                                    </div>
                                    <div class="btn prm-btn prm-btn-peach">
                                        <a href="<?php echo base_url('renew/'.$membership['account_id']);?>">Renew</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <?php }  ?>

                        <!-- <div class="card mem_card">

                            <div class="card-body membership-card">
                                <h3 class="membership_heading">
                                    Annual Parking Membership
                                </h3>
                                <h6 class="location">
                                    Physio Car Park
                                </h6>


                                <div class="right-sec">
                                    <img src="<?php echo base_url('assets/images/tag.svg'); ?> " alt="tag" class="tag"> 
                                </div>

                                <p class="info">
                                    All year access to Ambulance Area Car Park at all times of the day.
                                </p>

                                <p class="status">
                                    Expiring on : 31st Dec 2021
                                </p>

                                <img src="<?php echo base_url('assets/images/image-holder.svg'); ?>" alt="image-holder" style="margin: 0 auto">


                                <div class="card-btns peach flexed_spc_btn">
                                    <div class="btn ghost-btn ghost-btn-peach">
                                        <a href="<?php echo base_url('sign_in');?>">View</a>
                                    </div>
                                    <div class="btn prm-btn prm-btn-peach">
                                        <a href="<?php echo base_url('renew/'.$membership['account_id']);?>">Renew</a>
                                    </div>
                                </div>
                            </div>

                        </div> -->
                        

                    </div>
                </section>

            </div>

            <?php include('footer-dashboard.php'); ?>
            <?php include('footer.php'); ?>
        