<?php header("Access-Control-Allow-Origin: *"); ?>
<?php include('header-new.php'); ?>   
 <div class="main-body-holder renew">

<?php include('sidebar.php'); ?>  


<div class="header-form-section">
  <section class="container renew section scrollspy" id="RenewForm">
    <div class="row">
      <div class="col s12 l12">
        <!-- <h2 class="black-text left parking-membership"> Parking Membership Renewal</h2> -->
        <h5 class="black-text left renewal-header"> Parking Membership Renewal</h5>
      </div>
    </div>
  </section>

    <?php $location_id = 19; ?>
     <?php if($this->session->flashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('errors'); ?>
        </div>
    <?php endif; ?>

 
  <section class="container  renew section" >
    <div class="row">

        <div class="col s12 l4 parking-box right">
            <br><br>           
            <br>
        </div>
        

        <div class="col s12 l8 left">
            <ul class="tabs form-headers renew-form-headers">

                <li class="tab col l3">
                    <a href="#parkingInfo" class="black-text form-section">
                        <i class="material-icons black-text">looks_one</i>
                        Parking
                    </a>
                </li>

                <li class="tab col l3">
                    <a href="#payment_summ" class="black-text form-section">
                        <i class="material-icons black-text">looks_two</i>
                        Payment & Summary
                    </a>
                </li>
            </ul>

            <hr>

            <?php $attributes = array('id' => 'parkingForm'); echo form_open('Activation/Landing/buyNewPackge', $attributes); ?>

                <div class="col s12 l8 form-tab" id="parkingInfo">

                    <div class="row renewal-sel">

                        <div class="col s12 l12">
                            <div class="input-field" >
                                <select name="parking_area" id="parking_area" required class="browser-default">
                                    
                                    <option value="<?php if(isset($current_mem)){ echo $current_mem['parking_id']; } ?>"  style="color:green !important;">
                                       <?php if(isset($current_mem)){ echo $current_mem['parking_name']; } ?>
                                    </option>
                                    
                                    <?php foreach ($upgraded_mem as $up_parking) {?>
                                       <?php  if($up_parking['capacity'] > 0) {?>
                                        <option value="<?php echo $up_parking['parking_id']; ?>"><?php echo $up_parking['parking_name']; ?></option>
                                    <?php }} ?>

                                </select>
                                <span class="helper-text right-alert" data-error="Inavlid" data-success="valid"></span>
                            </div>                     
                        </div>     

                        <div class="col s12 l12">
                            <div class="input-field">
                                <select name="parking_space" id="parking_space" required class="browser-default" onchange="setSummaryDetails()">
                                    <option value="<?php if(isset($current_mem)){ echo $current_mem['parkspace_id']; } ?>" >
                                        <?php if(isset($current_mem)){ echo $current_mem['parkspace_name']; } ?>
                                    </option>                                        
                                </select>
                                <span class="helper-text right-alert" data-error="Inavlid" data-success="valid"></span>
                            </div>                     
                        </div>     
    
                        <div class="col s12 l12">
                            <div class="input-field">
                                <select name="parking_type" id="parking_type" required class="browser-default">
                                    <option value="<?php if(isset($current_mem)){ echo $current_mem['membership_type_id']; } ?>" >
                                        <?php if(isset($current_mem)){ echo $current_mem['membership_type']; } ?>
                                    </option> 
                                    
                                    <?php foreach ($renewal_types as $renewal_type) {?>
                                        <option value="<?php echo $renewal_type['id']; ?>"><?php echo $renewal_type['membership_type']; ?></option>
                                    <?php } ?>

                                </select>
                                <span class="helper-text right-alert" data-error="Inavlid" data-success="valid"></span>
                            </div>                     
                        </div> 

                        <input type="hidden" id="current_park_area" data-id="<?php if(isset($current_mem)){ echo $current_mem['parking_id']; }?>" data-value="<?php if(isset($current_mem)){ echo $current_mem['parking_name'];} ?>">
                        <input type="hidden" id="current_park_space" data-id="<?php if(isset($current_mem)){ echo $current_mem['parkspace_id']; }?>" data-value="<?php if(isset($current_mem)){ echo $current_mem['parkspace_name'];} ?>">
                        <input type="hidden" id="current_park_type" data-id="<?php if(isset($current_mem)){ echo $current_mem['membership_type_id']; }?>" data-value="<?php if(isset($current_mem)){ echo $current_mem['membership_type'];} ?>">
                        <input type="hidden" id="current_park_dis" data-id="<?php if(isset($disc)){ echo $disc; }?>" data-value="<?php if(isset($disc)){ echo $disc;} ?>">
                        <input type="hidden" id="current_park_arent" data-id="<?php if(isset($current_mem)){ echo $current_mem['annual_rent']; }?>" data-value="<?php if(isset($current_mem)){ echo $current_mem['annual_rent'];} ?>">

                    </div>

                    <div class="validity renewal-info">
                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>From</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6  class="ren_val" id="valid_from"><?php echo date('jS M Y', strtotime('first day of january next year')); ?></h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Until</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="valid_until"><?php echo date('jS M Y', strtotime('last day of december next year')); ?></h6>
                            </div>
                        </div>                        
                    </div>


                    <div class="parking renewal-info">

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Parking Charges</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="parking_charges">BD <?php if(isset($current_mem)){ echo $current_mem['annual_rent'];} ?> </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Discount Applied</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="dsc_perc"><?php if(isset($disc_perc)){echo $disc_perc;} ?>%</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Discount Amount</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="dsc_amt">-BD <?php if(isset($disc_amt)){echo $disc_amt;} ?> </h6>
                            </div>
                        </div>

                        <div class="row ttl_row">
                            <div class="col s6 l6">                 
                                <h6>Total</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="ttl">BD <?php if(isset($current_mem) && isset($disc_amt)){ echo ($current_mem['annual_rent'] - $disc_amt);}  ?></h6>
                            </div>
                        </div>    
                    
                    </div>

                    <div class="row">
                        <div class="col s12 l12">
                            <div class="card-btns peach flexed_spc_btn">
                                <div class="btn renew-btn ghost-btn ghost-btn-peach">
                                    <a href="#" onclick="history.go(-1);">Back</a>
                                </div>
                                <button id="parking_next_btn" type="button"onclick="go_next()" class=" btn renew-btn prm-btn prm-btn-peach"> Next</button>
                                
                            </div>                                        
                        </div>   
                    </div>
                </div>
            <?php echo form_close(); ?>

            <?php $attributes = array('id' => 'paymentForm'); echo form_open('BDF/renew_bdf', $attributes); ?>

                <div class="col s12 l8 form-tab" id="payment_summ">

                    <div class="parking renewal-info">
                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Parking Area</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6  class="ren_val" id="sum_park_area"><?php if(isset($current_mem)){ echo $current_mem['parking_name']; } ?></h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Parking Space</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="sum_park_space"><?php if(isset($current_mem)){ echo $current_mem['parkspace_name']; } ?></h6>
                            </div>
                        </div>                        
                    </div>

                    <div class="validity renewal-info">
                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Membership</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6  class="ren_val" id="sum_park_type"> <?php if(isset($current_mem)){ echo $current_mem['membership_type']; } ?></h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Valid From</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="valid_from"><?php echo date('jS M Y', strtotime('first day of january next year')); ?></h6>
                            </div>
                        </div>   

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Valid Until</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="valid_until"><?php echo date('jS M Y', strtotime('last day of december next year')); ?></h6>
                            </div>
                        </div>                        
                    </div>

                    <div class="parking renewal-info">

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Parking Charges</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="sum_parking_charges">BD <?php if(isset($current_mem)){ echo $current_mem['annual_rent'];} ?> </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Discount Applied</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="sum_dsc_perc"><?php if(isset($disc_perc)){echo $disc_perc;} ?>%</h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s6 l6">                 
                                <h6>Discount Amount</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="sum_dsc_amt">-BD <?php if(isset($disc_amt)){echo $disc_amt;} ?> </h6>
                            </div>
                        </div>

                        <div class="row ttl_row">
                            <div class="col s6 l6">                 
                                <h6>Total</h6>
                            </div>
                            <div class="col s6 l6">                 
                                <h6 class="ren_val" id="sum_ttl">BD <?php if(isset($current_mem) && isset($disc_amt)){ echo ($current_mem['annual_rent'] - $disc_amt);}  ?></h6>
                            </div>
                        </div>    
                    
                    </div>

                    <div class="row">
                        <div class="col s12 l12 flexed_spc_btn">                                    

                            <div class="card-holder">
                                <input type="radio" id="debit" name="card" value="debit"  onchange="setPaymentMethod(this)" required>
                                <label for="debit">
                                    <img src="<?php echo base_url() . 'assets/images/credit-card-blk.svg'; ?> " alt="card-icon" class="card-icon">
                                    Debit
                                </label>        
                            </div> 

                            <div class="card-holder">
                                <input type="radio" id="credit" name="card" value="credit"  onchange="setPaymentMethod(this)" required>
                                <label for="credit">
                                    <img src="<?php echo base_url() . 'assets/images/credit-card-blk.svg'; ?> " alt="card-icon" class="card-icon">
                                    Credit
                                </label>        
                            </div> 
                            
                        </div>   
                    </div>
                    
                    <div class="row">
                        <div class="col s12 l12">
                            <div class="card-btns peach flexed_spc_btn">
                                <button type="button"onclick="go_back()" class="btn renew-btn ghost-btn ghost-btn-peach">Back</button>

                                <button type="submit" class=" btn renew-btn prm-btn prm-btn-peach" id="pay_next_btn">Pay</button>

                            </div>                                        
                        </div>   
                    </div>

                </div>

                <input type="hidden" id="parking_charges_ttl" name="parking_charges_ttl" value="<?php if(isset($current_mem)){ echo $current_mem['annual_rent']; }?>" class="form-control" required>
                <input type="hidden" id="parking_dis_amt" name="parking_dis_amt" value="<?php if(isset($disc_amt)){echo $disc_amt;} ?>" class="form-control">
                <input type="hidden" id="amt_payable" name="amt_payable" value="<?php if(isset($current_mem) && isset($disc_amt)){ echo ($current_mem['annual_rent'] - $disc_amt);}  ?>" class="form-control">
                <input type="hidden" id="payment_method" name="payment_method" value="credit" class="form-control">
                <input type="hidden" id="valid_from" name="valid_from" value="<?php echo date('Y-m-d h:i:s', strtotime('first day of january next year')); ?>" class="form-control">
                <input type="hidden" id="valid_until" name="valid_until" value="<?php echo date('Y-m-d h:i:s', strtotime('last day of december next year')); ?>" class="form-control">

            <?php echo form_close(); ?>
        </div>

    </div>
</section>
</div>
</div>

<?php include('footer.php'); ?>
<?php include('footer-renew.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    $.validator.setDefaults({ 
        ignore: [],
    });

    $("#parking_next_btn").click(function() {
        if($('#parkingForm').valid()){
            $('ul.tabs').tabs('select','payment_summ');
        }else{
            console.log('form invalid');
        }
    });

    $("#pay_next_btn").click(function() {
        if($('#paymentForm').validate({
            rules: {
                parking_charges_ttl:{
                    required:true,
                    minlength:1
                },
                parking_dis_amt:{
                    required:true,
                    minlength:1
                },
                amt_payable:{
                    required:true,
                    minlength:1
                },        
                payment_method:{
                    required:true,
                    minlength:1
                },        
                amount_val:{
                    required:true,
                    minlength:1
                },        
            },

            errorElement : 'div',
            errorPlacement: function(error, element) {
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error)
                } else {
                    error.insertAfter(element);
                }
            },

            submitHandler: function (form) {
                var form_serialized = $('form').serialize();
                console.log(form_serialized);

                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url('BDF/renew_bdf');?>",
                    data: form_serialized,
                    dataType: "json",
                    success: function(data) {
                        if(data){
                            window.location.href = '<?php echo base_url('reviewMembershipDetails');?>'
                        }
                    },
                    error: function(error) {
                        console.log('submit handler error');
                        console.log(error);
                    }
                });
                // serialize and join data for all forms
                // ajax submit
                return false;
            }
        }));
    });

    $("#parkingForm").validate({
        rules: {
            parking_area:{
                required:true,
                minlength:1
            },
            parking_space:{
                required:true,
                minlength:1
            },
            parking_type:{
                required:true,
                minlength:1
            },        
        },
        //For custom messages
        messages: {
            parking_area:{
                required: "Please select a parking area",
            },
            parking_space:{
                required: "Please select a parking space",
            },
            parking_type:{
                required: "Please select a parking type",
            },            
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });

    var i = 1;
    $('.btn .dropdown-toggle .btn-light').css({
        "padding": "2px"
    });

    document.getElementById('parking_area').addEventListener('change', function() {
        //value of the park lot (id)
        var parkingLot = document.getElementById('parking_area').value;

        if(parkingLot) {
            $.ajax({
                url: "<?php echo base_url('get_vacant_parks'); ?>",
                type: 'post',
                data: {
                    parkingLot: parkingLot
                },
                dataType: 'json',
                headers: {
                    'Access-Control-Allow-Origin': '*',
                },
                success: function(response) {
                    $("#parking_space").empty();

                    var ex_parkspace_id = $(current_park_space).attr("data-id");
                    var ex_parkspace_name = $(current_park_space).attr("data-value");                    
                    var ex_parking_id = $(current_park_area).attr("data-id");  
                    var disc = $(current_park_dis).attr("data-id");  
                    
                    var ex_park_set = false;
                    var new_park_price_set = false;
                    var new_park_disc_set = false;
                    var new_park_ttl = false;

                    if(response.length > 0){                       

                        stringJSON = JSON.stringify(response);
                        parsedObject = JSON.parse(stringJSON);                        


                        for(var i = 0; i < response.length; i++) {

                            if(ex_parking_id == response[i].Parking_id && (!ex_park_set)){
                                $('#parking_space').append("<option style='color:green !important;' value='"+ex_parkspace_id+"' selected>"+ex_parkspace_name+" </option>");
                                ex_park_set = true;
                            }

                            var id = parsedObject[i].id;
                            var name = parsedObject[i].name;
                            $('#parking_space').append("<option value='"+id+"'>"+name+"</option>");                            

                            if( !new_park_disc_set || !new_park_price_set || !new_park_ttl){

                                document.getElementById('parking_charges').innerHTML = 'BD ' + response[i].annual_rent;                             
                                new_park_price_set = true;

                                var disc_amt = Number(response[i].annual_rent) -  ( Number(response[i].annual_rent) * Number(disc));
                                document.getElementById('dsc_amt').innerHTML = 'BD ' + disc_amt;
                                document.getElementById('sum_dsc_amt').innerHTML = 'BD ' + disc_amt;
                                new_park_disc_set = true;

                                document.getElementById('ttl').innerHTML = 'BD ' +  (Number(response[i].annual_rent) - disc_amt);
                                document.getElementById('sum_ttl').innerHTML = 'BD ' +  (Number(response[i].annual_rent) - disc_amt);
                                new_park_ttl = true;

                                document.getElementById('sum_park_area').innerHTML = response[i].parking_name;
                                
                                var e = document.getElementById("parking_type");
                                document.getElementById('sum_park_type').innerHTML = e.options[e.selectedIndex].text;

                                setSummaryDetails();
                                setHiddenInputs(response[i].annual_rent,disc_amt,(Number(response[i].annual_rent) - disc_amt));
                            }                            
                        }
                        // getParkingPackage();
                        // getPrice();
                    }        
                },    
                error: function () {
                    alert('error - 1');
                }
            });
        } else {
            console.log('parking area not selected');
        }
    });

    function setSummaryDetails() {
        var park_space = document.getElementById('parking_space');
        document.getElementById('sum_park_space').innerHTML = park_space.options[park_space.selectedIndex].text
    };

    function go_back(){
        $('ul.tabs').tabs('select','parkingInfo');
    }

    function go_next(){
        $('ul.tabs').tabs('select','payment_summ');
    }

    function setPaymentMethod(elm){
        document.getElementById('payment_method').value = elm.value;
        document.getElementById('payment_method').value = elm.value;
    }


    function setHiddenInputs(rent,disc_amt,amt_payable){
        document.getElementById('parking_charges_ttl').value = rent;
        document.getElementById('parking_dis_amt').value = disc_amt;
        document.getElementById('amt_payable').value = amt_payable;
    }
</script>
</script>
