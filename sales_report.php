<?php include 'header.php' ?>
<style>
    tbody,tbody  a{
		color: #f47c68;
		font-weight: 600;
	}

	.table-container{
		max-width: fit-content;
	}
</style>

    <div class="container">
        <div class="jumbotron" style="margin-top: 90px">

            <!-- start of section filters -->
            <section id="filters">
            
                <h3>Search Usage</h3><br>
        
            <!-- start if form filter -->
                <form action="<?php echo base_url('Admin/Reports/exportSalesReport')?>" method="POST" id="form-filter">
                    <div class="row mb-4">
                        <div class="col-lg-1">
                            <label for="dateRangePicker" class="">Date</label>
                        </div>
                        <div class="col-lg-3">
                            <input class="form-control" type="text" name="dateRangePicker" id="dateRangePicker" autocomplete="off">
                        </div>
                        <div class="col-lg-1">
                            <label for="carNumber" class="mr-4">Car Number</label>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" class="form-control" name="carNumber" id="carNumber">
                        </div>
                        <div class="col-lg-1">
                            <label for="ticket" class="mr-4">Ticket</label>
                        </div>
                        <div class="col-lg-3">
                            <input class="form-control" type="text" name="ticket" id="ticket">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-1">
                            <label for="membership" class="">Membership ID</label>
                        </div>
                        <div class="col-lg-3">
                            <input class="form-control" type="text" name="membership" id="membership" autocomplete="off">
                        </div>
                        <div class="col-lg-1">
                            <label for="location" class="">Location</label>
                        </div>
                        <div class="col-lg-3">
                            <select name="location" id="location" class="selectpicker" data-live-search="true">
                                <option value="">All Locations</option>
                                <?php foreach($locations->result() as $location): ?>
                                    <?php echo "<option value='$location->name'> $location->name</option>" ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <label for="service" class="mr-4">Service Type</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="selectpicker" name="serviceType" id="serviceType">
                                <option value="">All Services</option>
                                <option value="2">Standard</option>
                                <option value="1">VIP</option>
                            </select>
                        </div>
                    </div>

                    <div class="row d-flex flex-row-reverse">
                        <button type="button" id="btn-reset" class="btn btn-success m-3">Clear</button>
                        <button type="sumbit" id="btn-export" class="btn btn-success m-3">Export</button>
                        <button type="button" id="btn-filter" class="btn btn-success m-3">Filter</button>
                    </div>
                </form> <!-- end of form filters -->

            </section><!-- end of section filters -->

        </div><!-- end of jumbotron div -->
    </div><!-- end of container div -->

    <!-- start of table -->
    <div class="container-fluid mt-5 table-container">
    <div class="table-responsive">
        <table id="user_data" class="table table-hover table-striped ">
            <thead class="thead-light">
                <tr>
                        <th>Ticket</th>
                        <th>Membership ID</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Car Number</th>
                        <th>Location</th>
                        <th>Payment</th>
                </tr>
            </thead>
            <tfoot class="thead-light">
                <tr>
                    <th>Ticket</th>
                    <th>Membership ID</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Car Number</th>
                    <th>Location</th>
                    <th>Payment</th>
                </tr>
            </tfoot>
        </table>
    </div>
    </div>    
     <!-- end of table -->
        

</div><!-- end of wrapper div -->
<?php include 'footer.php' ?>

<script>
$(function() {

$('input[name="dateRangePicker"]').daterangepicker({
  timePicker: true,
  startDate: moment().startOf('hour'),
  endDate: moment().startOf('hour').add(32, 'hour'),
  locale: {
      format: 'YYYY/MM/DD hh:mm',
      cancelLabel: 'Clear',
  },
});

$('#dateRangePicker').on('show.daterangepicker', function(ev, picker) 
  {
    //do something, like clearing an input
    $('.hourselect').val('6')
    $('.minuteselect').val('0')
    $('.ampmselect').val('AM')
  });


  $.when(dataTables).then(function() {
      $('#form-filter')[0].reset();
      dataTables.ajax.reload();
  });
});
var dataTables;

dataTables = $('#user_data').DataTable({
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": "<?php echo site_url('Admin/Reports/ajax_list')?>",
        "type": "POST",
        "data": function(data) {
            data.dateRangePicker = $('#dateRangePicker').val();
            data.carNumber       = $('#carNumber').val();
            data.membership      = $('#membership').val();
			data.ticket          = $('#ticket').val();
			data.location        = $('#location').val();
			data.serviceType     = $('#serviceType').val();
			data.client          = '';
        },
    },
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "drawCallback": function() {
            $('[data-toggle="popover"').popover({
                html: true
            });
        },
    
});



$('#btn-filter').click(function() {
	dataTables.ajax.reload();
});

$('#btn-reset').click(function() {
	$('#form-filter')[0].reset();
	$('#form-filter div:nth-child(2) div:nth-child(4) div button div div div').text('All Locations');
    $('#form-filter div:nth-child(3) div.col-lg-3 div button div div div').text('All Clients');
    $('#form-filter div:nth-child(2) div:nth-child(6) div button div div div').text('All Services')
    
	dataTables.ajax.reload();
});

</script>