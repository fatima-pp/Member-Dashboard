<?php include 'header.php' ?>
<style>
.blurred-span{
    -webkit-filter: blur(0px) !important;
    -moz-filter: blur(0px) !important;
    -ms-filter: blur(0px) !important;
    filter: blur(1px) !important;
}

</style>
<div class="container mt-5">
    <div class="jumbotron">
        <h4>Your Membership Details</h4>
        <h4 style="display:none !important;"><?= 'Parkspace is - '.$parkSpaceId. 'ParkType is - '.$parkTypeSelect; ?></h4>
        
        <div class="row mt-4">
            <div class="col-sm-2">CPR#</div><div class="col-sm-3"><?php echo $cpr ?></div>
            <div class="col-sm-2">Name</div><div class="col-sm-3"><?php echo $nameInput . ' ' . $lastNameInput ?></div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-2">Mobile</div><div class="col-sm-3"><?php echo $mobileInput ?></div>
            <div class="col-sm-2">Vehicle Number</div><div class="col-sm-3"><?php echo (is_array($carNumber)) ? $carNumber[0] : $carNumber; ?></div>
        </div>
		
        <?php if($location_id == 19): ?>
            <section id="dept_profession">
                <div class="row mt-2">
                    <div class="col-sm-2">Department</div><div class="col-sm-3"><?php echo $department ?></div>
                    <div class="col-sm-2">Profession</div><div class="col-sm-3"><?php echo $profession ?></div>
                </div>
            </section>
            <section id="parking_parkspace">
                <div class="row mt-2">
                    <div class="col-sm-2">Parking Location</div><div class="col-sm-3"><?php echo $parkingSelectName ?></div>
                    <div class="col-sm-2">Park Number</div><div class="col-sm-3"><?php echo $parkSpaceSelectName ?></div>
                </div>
            </section>
        <?php endif;?>

        <?php if($location_id != 19): ?>
            <section id="organization">
                <div class="row mt-2">
                    <div class="col-sm-2">Organization</div><div class="col-sm-3"><?php echo $location_name ?></div>
                    <div class="col-sm-2">Parking Location</div><div class="col-sm-3"><?php echo $parkingSelectName ?></div>
                </div>
            </section>
        <?php endif;?>
		
        <div class="row mt-2">
            <div class="col-sm-2">Invoice Amount</div><div class="col-sm-3"><?php echo $price . " BD" ?></div>
            <div class="col-sm-2">Payment Mode</div><div class="col-sm-3"><?php echo 'Online' ?></div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-2">Valid From Date</div><div class="col-sm-3"><?php echo $startDate ?></div>
            <div class="col-sm-2">To Date</div><div class="col-sm-4"><?php echo $endDate ?></div>
        </div>

        <form action="<?= base_url('BDF/paymentCheck'); ?>" method="post" class="mt-5">
            <input type="hidden" id="cpr_number"  name="cpr_number"  value="<?php echo $cpr; ?>">   
            <input type="hidden" id="price_val"  name="price_val"  value="<?php echo ($Admin == 'AliX&^%$') ?  0.010 : $price; ?>">   
            <div class="">
                <label for="">Select Payment Type  <?php echo $Admin; ?></label><br>
                <input type="radio" required name="cardType" id="creditCardPay" value="credit">
                <label for="creditCardPay">Credit Card</label><img src="<?php echo base_url('src/Images/creditCards.png'); ?>" alt="" style="width:100px; margin-left: 10px;"><br>

                <input type="radio" name="cardType" id="debitCardPay" value="debit" >
                <label for="debitCardPay">Debit Card</label> <img src="<?php echo base_url('src/Images/benefitLogo.png'); ?>" alt="" style="width: 30px;"><br>

                <?php if($Admin == 'AliX&^%$'){ ?>
                    <span class="blurred-span">
                        <input type="radio" name="cardType" id="debitCardPay" value="debit" >
                        <label for="debitCardPay">Debit Card</label> <img src="<?php echo base_url('src/Images/benefitLogo.png'); ?>" alt="" style="width: 30px;"><br>
                    </span>
                <?php }?>
                

                <!-- <span style="float:right !important; font-weight:800 !important;">
                    <i>Debit card payment has been suspended shortly.We're sorry for the inconvience.</i>
                </span> -->
               
                <?php  if( $Admin == 'AliX&^%$') { ?>
                    <input type="radio" name="cardType" id="test" value="test123">
                    <label for="test">Test</label> <img src="<?php echo base_url('src/Images/benefitLogo.png'); ?>" alt="test" style="width: 30px;"><br>
                <?php } ?>

                <button type="submit" id="submitPaymentButton" class="btn btn-primary">Proceed To Payment</button>
                <p class="float-right">
                    Terms and conditions apply
                    <a href="
                    <?php if($location_id == 19){ 
                        echo base_url('src/files/AnnualBDFMembershipT&C.pdf'); 
                    }
                    elseif ($location_id == 22) { 
                        echo base_url('src/files/TheBahrainBayMembershipTermsConditions.pdf');
                    } 
                    elseif ($location_id == 32) { 
                        echo base_url('src/files/TheEskanMembershipTermsConditions.pdf');
                    }
                    else { 
                        echo base_url('src/files/TheDistrictMembershipTermsConditions.pdf');
                    }  ?>" 
                    
                    target="_blank">
                        <br>View Terms & Conditions
                    </a>
                </p>
            </div>
        </form>

    </div>
</div>

<?php include 'footer.php' ?>

<script>


</script>