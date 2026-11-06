@extends('admin.layouts.layout')
@section('css')
<style>
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 999999; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
	    cursor: default !important;
	    color: #fff !important;
	    border: 1px solid transparent;
	    background: #134576 !important;
	    box-shadow: none !important;
	    border-radius: 6px !important;
	}
	table#producttable {
	    border-style: none !important;
	    background: #fff !important;
	    border-radius: 6px !important;
	    margin-bottom: 22px;
	}
    .dataTables_wrapper .dataTables_filter input {
	    margin-left: 0.5em;
	    margin-bottom: 15px;
	}
	thead.top-text {
	    padding: 10px 55px;
	    color: #134576;
	    text-transform: uppercase;
	}
	tr.bottom-text {
	    padding: 10px 55px;
	    color: #134576;
	    text-transform: uppercase;
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
                       <a href="/../index.php">
                          <i class="feather icon-home"></i>
                       </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">My Account - Addons Products</a></li>
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
		                			<div class="card-header">
		                 			</div>
					                <div class="tab-content responsive">
					                    <div id="cinfo" class="tab-pane fade active show">
					                        <div class="border-box border-top-none-table">
					                            <div class="row Information-tab">
					                                <div class="col-lg-12 col-md-12 col-sm-12">
					                                    <div class="border-box">
					                                        <div class="row">
					                                            <div class="col-md-12">
					                                                <div class="top-title border-bootm">
					                                                    <h3 class="table-heading">View Addons Products</h3>
					                                                </div>
                                            </div>
                                        </div>
                                        @if(session()->has("message"))
                                            <div class="alert alert-info">
                                                {{ session()->get('message') }}
                                            </div>
                                        @endif
                                        <div class="table-responsive log-table">
                                            <table class="table table-hover log-table" id="producttable">
                                                    <thead class="top-text">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Product Name</th>
                                                            <th>Addon Title</th>
                                                            <th>Description</th>
                                                            <th>Old Price</th>
                                                            <th>Discount</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        	@php
                                                        		$serial = 1;
                                                        	@endphp
                                                    		@foreach($productData as $product)

                                                        		<tr>
                                                                            <td>{{ $serial }}</td>
                                                                            <td>{{ $product->ProductName }}</td>
                                                                            <td>{{ $product->addonTitle }}</td>
                                                                            <td>{{ $product->addonDescription }}</td>
                                                                            <td>{{ $product->ProductPrice }}</td>
                                                                            <td>
                                                                                @if($product->addonDiscount !=null)
                                                                             		{{$product->addonDiscount."%"}}
                                                                             	@else
                                                                             		{{ $product->addonFixedPrice }}
                                                                                @endif
                                                                             </td>
                                                                            <td>{{ $product->start_date }}</td>
                                                                            <td>{{ $product->end_date }}</td>
                                                                            <td>
                                                                                @php 
                                                                                	$valll = $product->id 
                                                                                @endphp
                                                                                <button type="button" onclick="updateAddonProduct('<?php echo $valll;?>')" class="btn btn-primary">Update</button>
                                                                                <form action="addonremove"  method="post" id="remove_form" onsubmit="confirmRemove(<?php echo $valll; ?>);return false;">                                   
                                                                                    <button class="btn btn-danger" type="submit" >Remove</button>
                                                                            </form>

                                                                            </td>
                                                                        </tr>
                                                                        @php
                                                                        	$serial++;
                                                                        @endphp
                                                            @endforeach
                                                            
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bottom-text">
                                                            <th>#</th>
                                                            <th>Product Name</th>
                                                            <th>Addon Title</th>
                                                            <th>Description</th>
                                                            <th>Old Price</th>
                                                            <th>Discount</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!--update Modal -->
        <div class="modal fade " id="add_addons" tabindex="-1" role="dialog" aria-labelledby="add_addons" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"><b>Add Add-Ons On Products</b></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                  </div>
              <div class="modal-body">
                  
                  <form class="form-horizontal" method="POST" action="update_addon" enctype="multipart/form-data">
                  		<input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="Hidden" name="productid" id="addonProductId" value="">
                        <input type="hidden" name="proid" id="proid" value="">
                        <div class="form-group">
                            <label for="Title" class="col-sm-3 control-label">Add-ons Title</label>

                            <div class="col-sm-9">
                              <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Description" class="col-sm-3 control-label">Description</label>

                            <div class="col-sm-9">
                              <input type="text" class="form-control" id="desc" name="description" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="selectMethod" class="col-sm-3 control-label">Select Discount</label>

                            <div class="col-sm-9">
                                <select name="DiscountAddonsProduct" id="discountAddon">
                                    <option value="0">-Select Discount --</option>
                                    <option value="1">Fixed Price</option>
                                    <option value="2">Percentage</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="Value" class="col-sm-3 control-label">Value</label>

                            <div class="col-sm-9">
                              <input type="text" class="form-control" id="descountPercentage" name="descountAddons"required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Start-Date" class="col-sm-3 control-label">Start Date</label>

                            <div class="col-sm-9">
                              <input type="date" class="form-control" id="startDate" name="startDate" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Start-Date" class="col-sm-3 control-label">End Date</label>

                            <div class="col-sm-9">
                              <input type="date" class="form-control" id="endDate" name="endDate" required>
                            </div>
                        </div>
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" name="addProductAddons" value="Add Addons" class="btn btn-primary mr-auto">
                    </form>
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




@endsection