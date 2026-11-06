@extends('admin.layouts.layout')
@section('css')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">


<style type="text/css">
	.toggle.btn.btn-default.off {
	    border: 1px solid #cec8c8;
	}
	.btn.btn-default.active.toggle-off {
    background-color: #b3b0b0;
    outline: 0;
    -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
    box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
  }
  span.toggle-handle.btn.btn-default {
	    background-color: #fff;
	}
	table.dataTable thead th, table.dataTable thead td {
	    border-color: #e0dddd !important;
	}
	table.dataTable.no-footer {
	    border-bottom: 1px solid #9c9c9c !important;
	    margin-bottom: 43px;
	}

	.table {
	  border-spacing: 0 0.85rem !important;
	}


	#profile_card {
	    background-color: #f3f7f96b;
	}
	#type {
	    border: 1px solid #cccccc;
	}
</style>
@endsection
@section('content')
<div class="page-header">
    <div class="page-block">
       <div class="row align-items-center">
          <div class="col-md-12">
             <ul class="breadcrumb">
                <li class="breadcrumb-item">
                   <a href="/">
                      <i class="feather icon-home"></i>
                   </a>
                </li>
                <li class="breadcrumb-item">Coupons Management</li>
             </ul>
          </div>
       </div>
    </div>
 </div>
<div class="admin-content">
	<div class="pcoded-inner-content">
		<div class="main-body">
			<div class="page-wrapper">
        <div class="page-body">
          <div class="row">
            <div class="col-sm-12">
             	<div class="card">
             			@if ($message = Session::get('success'))
									<div class="alert alert-success alert-block">
									    <button type="button" class="close" data-dismiss="alert">×</button>    
									    <strong>{{ $message }}</strong>
									</div>
									@endif
									  
									@foreach($errors->all() as $error)
									<div class="alert alert-danger alert-block">
									    <button type="button" class="close" data-dismiss="alert">×</button>    
									    <strong>{{ $error }}</strong>
									</div>
									@endforeach
								<div class="messages"></div>
              	<div class="card-header">
              		<h3>Coupons</h3>
              		<a class="btn btn-primary add-coupon float-right" data-toggle="modal" data-target="#edit_coupon"><i class="feather icon-plus-circle" ></i>Add</a>
              	</div>
								<div class="card-block" id="profile_card">
			            <div class="row">
			              <div class="col-md-12">
			                <div id="wizard">
		                    <section>
		                    	<div class="coupons">
		                    		<table class="table table-default" id="coupon_table">
		                    			<thead>
		                    				<th>No.</th>
		                    				<th>Code</th>
		                    				<th>Type</th>
		                    				<th>Value</th>
		                    				<th>Percent Off</th>
		                    				<th>Start Date</th>
		                    				<th>End Date</th>
		                    				<th>Active</th>
		                    				<th>Actions</th>
		                    			</thead>
		                    			<tbody>
		                    				@php 
		                    					$count = 0;
		                    				@endphp
		                    				@foreach($coupons as $coupon)
		                    				<tr>
		                    					<td>{{ ++$count }}</td>
		                    					<td>{{ $coupon->code }}</td>
		                    					<td>{{ $coupon->type }}</td>
		                    					<td>{{ $coupon->value }}</td>
		                    					<td>{{ $coupon->percent_off }}</td>
		                    					<td>{{ $coupon->start_date }}</td>
		                    					<td>{{ $coupon->end_date }}</td>
		                    					<td>
		                    						<div>
  																		<input type="checkbox" id="switch_{{$count}}" data-onstyle="success" data-toggle="toggle" data-on="Activate" data-off="Deactivate" @if($coupon->is_active == 1) checked @endif onchange="updateCouponStatus(this,{{$coupon->id}})">
																		</div>
																	</td>
		                    					<td>
		                    						<a class="btn btn-primary edit-coupon" data-toggle="modal" data-target="#edit_coupon" data-id="{{$coupon->id}}"><i class="feather icon-edit" ></i></a>
		                    						<a class="btn btn-danger" href="{{route('admin.coupon.delete',$coupon->id)}}"><i class="feather icon-delete"></i></a>
		                    					</td>
		                    				</tr>
		                    				@endforeach
		                    			</tbody>
		                    		</table>
		                    	</div>
		                    </section>
			                </div>
		                </div>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
</div>
	<!-- Change password Modal -->
<div id="edit_coupon" class="modal fade" role="dialog">
	<div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
		        <h4 class="modal-title">Update Coupon</h4>
		        <button type="button" class="close adjust_x" data-dismiss="modal">&times;</button>
		    </div>
        <div class="modal-body">
	      	<form method="post" action="" id="edit_coupon_form">
	      		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        <div class="row">
		         	<label>Code</label>
		         	<input class="form-control" type="text" name="code" id="code" required>
		        </div>
		        <div class="row">
		         	<label>Type</label>
		         	<select class="form-control" id="type" name="type">
		         		<option value="fixed">Fixed</option>
		         		<option value="percent">Percent OFF</option>
		         	</select>
		        </div>
		        <div class="row value-container">
		         	<label>Value</label>
		         	<input class="form-control" type="text" name="value" id="value">
		        </div>
		        <div class="row percent-container">
		         	<label>Percent OFF</label>
		         	<input class="form-control" type="text" name="percent_off" id="percent_off">
		        </div>
		        <div class="row">
		         	<label>Start Date</label>
		         	<input class="form-control" type="date" name="start_date" id="start_date" required>
		        </div>
		        <div class="row">
		         	<label>End Date</label>
		         	<input class="form-control" type="date" name="end_date" id="end_date" required>
		        </div>
		        <div class="row">
		         	<div class="col-md-9 small_screen_submit">
	         			<input type="submit" class="btn btn-info" name="submit" id="Save">
	            </div>
	          	<div class="col-md-3 small_screen_close">
		         		<button  type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		         	</div>
		        </div>
			    </form>
		    </div>
			</div>
    </div>
  </div>
	<!-- Change password Modal -->
@endsection

@section('script')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
	$('#coupon_table').DataTable({
    aaSorting: [],
    responsive: true,

    columnDefs: [
      {
        responsivePriority: 1,
        targets: 0
      },
      {
        responsivePriority: 2,
        targets: -1
      }
    ]
  });
   $(".dataTables_filter input")
    .attr("placeholder", "Search here...")
    .css({
      width: "300px",
      display: "inline-block"
    });

  $(function() {
    $('#toggle-two').bootstrapToggle({
      on: 'Enabled',
      off: 'Disabled'
    });
  });
  $('#type').on('change', function(){
  	var type = $(this).val();
  	if (type === 'fixed') {
			$('.value-container').show();
			$('.percent-container').hide();
		}else{
			$('.value-container').hide();
			$('.percent-container').show();
		}
  });
  $(document).on("click", ".add-coupon", function (event) {
  	$('#edit_coupon .modal-title').text('Add Coupon');
  	$('#Save').val('Add');
  	$('#edit_coupon_form').attr("action", "/admin/coupon/new");
  	var type = $('#type').val();
  	if (type === 'fixed') {
			$('.value-container').show();
			$('.percent-container').hide();
		}else{
			$('.value-container').hide();
			$('.percent-container').show();
		}
  });
  function updateCouponStatus(e,id){
  	var status = $(e).is(':checked');
  	 $.ajax({
    	url: '/admin/coupon/deactivate/'+id,
    	type: 'post',
    	headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    	data: {status:status},
    	success: function(coupon){
    		$('.messages').html(`<div class="alert alert-info alert-block">
									    <button type="button" class="close" data-dismiss="alert">×</button>    
									    <strong>`+coupon['message']+`</strong>
									</div>`);
    	},
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
          alert(XMLHttpRequest['responseText']);
          console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
      }
    });
  };
  $(document).on("click", ".edit-coupon", function (event) {

  	$('#edit_coupon .modal-title').text('Update Coupon');
    var id = $(this).data('id');
    var code = $(this).parent().parent().children('td:nth-child(2)').text();
    var type = $(this).parent().parent().children('td:nth-child(3)').text();
    var value = $(this).parent().parent().children('td:nth-child(4)').text();
    var percent_off = $(this).parent().parent().children('td:nth-child(5)').text();
    var start_date = $(this).parent().parent().children('td:nth-child(6)').text();
    var end_date = $(this).parent().parent().children('td:nth-child(7)').text();

		$('#code').val(code);
		$('#value').val(value);
		$('#type option[value="'+type+'"]').prop('selected',true);
		if (type === 'fixed') {
			$('.value-container').show();
			$('.percent-container').hide();
		}else{
			$('.value-container').hide();
			$('.percent-container').show();
		}
		$('#percent_off').val(percent_off);
		$('#start_date').val(start_date);
		$('#end_date').val(end_date);
    //  $.ajax({
    // 	url: '/admin/coupon/get',
    // 	type: 'get',
    // 	data: {id:id},
    // 	success: function(coupon){
    // 		if (coupon) {
    // 			$('#code').val(coupon['code']);
    // 			$('#value').val(coupon['value']);
    // 			$('#type option[value="'+coupon['code']+'"]').prop('select',true);
    // 			$('#start_date').val(coupon['start_date']);
    // 			$('#end_date').val(coupon['end_date']);
    // 			$('#code').val(coupon['code']);
    // 			$('#code').val(coupon['code']);
    // 		}
    // 		$('#edit_coupon').attr("action", "admin/coupon/update/"+id);
    // 	},
    //   error: function(XMLHttpRequest, textStatus, errorThrown) { 
    //       alert(XMLHttpRequest['responseText']);
    //       console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
    //   }
    // });
		$('#edit_coupon_form').attr("action", "/admin/coupon/update/"+id);
  });
</script>
@endsection