@extends('layouts.user.user_main')
@section('css')
 
<style type="text/css">
	input.form-control {
	    margin-bottom: 15px;
	}
	.card .card-header {
	    background-color: #134576;
	    border-bottom: none;
	    padding: 20px 30px;
	    position: relative;
	    text-align: center;
	    border-radius: 6px 6px 0 0;
	    text-transform: uppercase;
    }
	.card .card-header h5 {
	    margin-bottom: 0;
	    color: #fff;
	    font-size: 18px;
	    display: inline-block;
	    margin-right: 10px;
	    line-height: 1.1;
	    position: relative;
	    font-weight: 700;
	    padding: 5px;
    }
    #payments_card {
	    background-color: #dfe4e7;
    }
	.col-form-label {
	    font-size: 16px;
	    color: #134576 !important;
	    font-weight: 700;
    }
    .form-control {
	    border-radius: 6px;
	    border-style: none;
	    padding-top: 10px;
	    padding-bottom: 10px;
	}
	input#submitButton {
	    padding: 10px 24px;
	    margin-bottom: 15px;
	    margin-left: 20px;
	    background-color: #134576;
	    border-radius: 6px;
	    border-style: none;
	    text-transform: uppercase;
	}
	input#submitButton:hover {
	    padding: 10px 24px;
	    margin-bottom: 15px;
	    margin-left: 20px;
	    background-color: #b0cb02;
	    border-radius: 6px;
	    border-style: none;
	    text-transform: uppercase;
	}
	table.dataTable.no-footer {
	    border-style: none !important;
	    background: #fff !important;
	    border-radius: 6px !important;
	    margin-top: 22px !important;
    }
    #payments-table>thead>tr>th {
	    padding: 23px 89px;
	    text-align: center;
	    color: #134576;
	    text-transform: uppercase;
    }
    #payments-table>tbody>tr>td {
	    padding: 23px 89px;
	    text-align: center;
	    color: #134576;
	    text-transform: uppercase;
    }
    div.dataTables_wrapper div.dataTables_info {
	    display: inline-block;
	    font-weight: 600;
    }
	.dataTables_wrapper .dataTables_length select{
	    border-style: none !important;
	    padding: 5px 30px 5px 5px !important;
	    border-radius: 6px !important;
	}
	.dataTables_wrapper .dataTables_filter input {
	    margin-left: 0.5em;
	    border-style: none !important;
	    border-radius: 6px !important;
	    padding: 5px 15px 5px 15px !important;
	}
	.dataTables_wrapper .dataTables_paginate .paginate_button {
	    box-sizing: border-box;
	    display: inline-block;
	    min-width: 1.5em;
	    padding: 0.5em 1em;
	    margin-left: 10px;
	    text-align: center;
	    text-decoration: none !important;
	    cursor: pointer;
    }
	.dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
	    color: #333 !important;
	    border: 1px solid #979797;
	    background-color: white;
	    border-radius: 6px;
	    background: linear-gradient(to bottom, #fff 0%, #dcdcdc 100%);
	    box-shadow: none !important;
	}
	.dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
	    cursor: default;
	    color: #fff !important;
	    border: 1px solid transparent;
	    background: #134576;
	    box-shadow: none;
	    border-radius: 6px;
	    margin-top:10px;
	}
</style>
@endsection
@section('content')
<div class="col-sm-12">

	<div class="card">

		<div class="card-header">

			<h5>View Active Calls</h5>

		</div>

		<div class="card-block" id="payments_card">

			<div class="row">

				<div class="col-md-12">

					<div id="wizard">

						<section>
							<form method="POST" action="">
								{{csrf_field()}}
								<div class="active-calls-stats">
									<div class="form-group">
										<label class="col-form-label text-info">Enter IVR:</label>
										<input type="text" name="ivr" id="ivr" class="form-control" placeholder="Enter IVR" required />
									</div>
									
									<div class="row">
								        <div class='col-sm-6'>
								            <div class="form-group">
								                <label class="col-form-label text-info">From:</label>
								                <input type='date' class="form-control" name="from" required />
								                     
								            </div>
								        </div>
								        <div class='col-sm-6'>
								            <div class="form-group">
								                <label class="col-form-label text-info">To:</label>
								                <input type='date' class="form-control" name="to" required />
								                     
								            </div>
								        </div>
								    </div>
								    <div class="row">
										<input id="submitButton" class="btn btn-info float-right" value="View Calls History" type="submit" />
									</div>
								</div>
								
								
							</form>

							<table class="table table-responsive" id="payments-table">

								<thead>

									<tr>

										<th>IVR</th>
										<th>Number</th>
										<th>Connect Time</th>
										<th>Disconnect Time</th>
										<th>Disconnect Reason</th>

									</tr>

								</thead>

								<tbody id="activecallsdata">

									<?php  if($activeCallsResults != ""){
									 	
									 	foreach ($activeCallsResults as $value) {
									 		echo "<tr>";
									 		echo "<td>".$value->CLI."</td>";
									 		echo "<td>".$value->CLD."</td>";
									 		echo "<td>".$value->connect_time."</td>";
									 		echo "<td>".$value->disconnect_time."</td>";
									 		echo "<td>".$value->disconnect_reason."</td>";
									 		echo "</tr>";
									 	}
									 	
									 } ?>

 								</tbody>

							</table>

						</section>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

@endsection
@push('scripts')

<script type="text/javascript">

	/*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');

	breadcrumb.text('Call History');

	breadcrumb.prop("href", "calls_history.php")*/
	var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b><a href="/active_calls"><span>Call History</span></a></b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/calls_history"]').parents().addClass('active');

	$(document).ready( function () {

	    $('#payments-table').DataTable();

	} );

</script>

<script type="text/javascript">
	// function getActivecalls(){
	// 	var ivr = $('#ivr').val();
	// 	if(ivr != ""){
	// 		$.ajax({
	// 			url: 'actions/getActivecalls.php',
	// 			method: 'POST',
	// 			data: {ivrnumber: ivr},
	// 			success: function(data){
	// 				console.log(data);
	// 				var callResult = JSON.parse(data);
	// 				//var callResult = data;
	// 				var htmlstr = "";
	// 				if( callResult.length > 0){
	// 					//alert(callResult.length);
	// 					for(var i = 0; i < callResult.length; i++){
	// 						htmlstr += "<tr>";
	// 						htmlstr += "<td>" +callResult[i].CLD+ "</td>";
	// 						htmlstr += "<td>" +callResult[i].CLI+ "</td>";
	// 						htmlstr += "<td>" +callResult[i].connect_time+ "</td>";
	// 						htmlstr += "</tr>";
	// 					}
	// 					$('#activecallsdata').html(htmlstr);
	// 				}else{
	// 					alert("There is no Active Call.");
	// 				}
	// 			}
	// 		});

	// 	}else{
	// 		alert("Please Enter Your IVR.");
	// 	}
	// }
</script>

@endpush
