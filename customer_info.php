<?php include 'header.php' ?>
<style>
	tbody{
		color: #f47c68;
		font-weight: 600;
	}

	.table-container{
		max-width: fit-content;
	}
</style>
<div id="currentPage" class="d-none">Customer Information</div>
	<div class="container">
		<div class="jumbotron" style="margin-top: 90px">	
			<section>
				<h3>Search Customers</h3><br>
		
				<form action="" id="form-filter">

					<div class="row mb-4">
						<div class="col-lg-1">
							<label for="membership" class="">Membership ID</label>
						</div>
						<div class="col-lg-3">
							<input class="form-control" type="text" name="membership" id="membership" autocomplete="off">
						</div>
						<div class="col-lg-1">
							<label for="activationDate">Activation Date</label>
						</div>
						<div class="col-lg-3">
							<input type="text" id="activationDate" name="activationDate" class="form-control">
						</div>
						<div class="col-lg-1">
							<label for="mobile">Mobile</label>
						</div>
						<div class="col-lg-3">
							<input type="text" id="mobile" name="mobile" class="form-control">
						</div>
					</div>

					<div class="row mb-4">
						<div class="col-lg-1">
							<label for="fname" class="">First Name</label>
						</div>
						<div class="col-lg-3">
							<input class="form-control" type="text" name="fname" id="fname" autocomplete="off">
						</div>

						<div class="col-lg-1">
							<label for="lname" class="">Last Name</label>
						</div>
						<div class="col-lg-3">
							<input class="form-control" type="text" name="lname" id="lname" autocomplete="off">
						</div>

						<div class="col-lg-1">
							<label for="car_number">Car Plate Number</label>
						</div>
						<div class="col-lg-3">
							<input type="text" id="car_number" name="car_number" class="form-control">
						</div>
					</div>


					<div class="row mb-1">
						<div class="col-lg-1">
							<label for="email" class="mr-4">Email</label>
						</div>
						<div class="col-lg-3">
							<select class="selectpicker" name="email" id="email" data-live-search="true">
								<option value="">All Emails</option>
								<?php foreach($customers->result() as $customer) {
									echo "<option value='{$customer->email}'>{$customer->email}</option>";
								} ?>
							</select>
						</div>
					</div>		
					<div class="row d-flex flex-row-reverse">
						<button type="button" id="btn-reset" class="btn btn-success m-3">Clear</button>
						<button type="button" id="btn-filter" class="btn btn-success m-3">Filter</button>
					</div>
				</form>
			</section>
	</div>
		<div class="container-fuild mt-5 table-container" style="">
			<div class="table-responsive">
				<table id="user_data" class="table table-hover text-center table-striped">
					<thead class="thead-light">
						<tr>
							<th>Membership ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Activation Date</th>
							<th>Client</th>
							<th>Mobile</th>
							<th>Card Last Digits</th>
							<th>Gender</th>
							<th>Action</th>
						</tr>
					</thead>
					<tfoot class="thead-light">
						<tr>
							<th>Membership ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>Activation Date</th>
							<th>Client</th>
							<th>Mobile</th>
							<th>Card Last Digits</th>
							<th>Gender</th>
							<th>Action</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	</div>

<?php include 'footer.php' ?>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>

$(function() {

	$('#activationDate').daterangepicker({
		singleDatePicker: true,
		showDropdowns: false,
		minYear: 2015,
	});

	// problem with the filter, they are not working when the 
	// table loads but they work when the table is reloaded
	// this issue is only in this table
	// Error is fixed by using the below promise .when .then
	// to reload the table

	$.when(dataTable).then(function() {
		$('#form-filter')[0].reset();
		dataTable.ajax.reload();
	});

});

var dataTable;

dataTable = $('#user_data').DataTable({
	"processing": true,
	"serverSide": true,
	"order": [],
	"ajax": {
		"url": "<?php echo site_url('Admin/crud/fetch_user'); ?>",
		"type": "POST",
		"data": function(data) {
			data.membership     = $('#membership').val();
			data.activationDate = $('#activationDate').val();
			data.client         = '';
			data.email          = $('#email').val();
			data.mobile			= $('#mobile').val();
			
			data.fname			= $('#fname').val();
			data.lname			= $('#lname').val();
			data.car_number		= $('#car_number').val();
		}
	},
	"searching": false,
	"ordering": false,
	"bLengthChange": false,
	"drawCallback": function() {
		//executes after tables reloads
	},
	"columnDefs": [
		{

		},
	],
});

$('#btn-filter').click(function() {
	dataTable.ajax.reload();
});

$('#btn-reset').click(function() {
	$('#form-filter')[0].reset();
	$('#form-filter div.row.mb-1 div:nth-child(2) div button div div div').text('All Clients');
	$('#form-filter div.row.mb-1 div:nth-child(4) div button div div div').text('All Emails');
	dataTable.ajax.reload();
});

</script>