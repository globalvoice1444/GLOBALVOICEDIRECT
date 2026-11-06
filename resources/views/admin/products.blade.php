@extends('admin.layouts.layout')
@section('css')
<style>
	.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 999999; /* Sit on top */
    padding-top: 60px; /* Location of the box */
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
    padding: 40px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    text-align: right;
    font-size: 33px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
tr.bottom-text {
    padding: 10px 55px;
    color: #134576;
    text-transform: uppercase;
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
input[type="file"] {
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 5px 5px 5px 5px;
    margin-bottom: 20px;
    width: 100%;
}
.border-box.border-top-none-table {
    margin: 60px 30px 30px 30px;
    border-radius: 6px;
    background-color: #dfe4e7;
    border: none;
}
table#product-table {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-bottom: 20px;
    margin-left: 20px;
    margin-top: 12px;
    margin-right: 20px;
    display: block;
    overflow-x: auto;
    width:auto;
}
#product-table_filter {
    margin-right: 20px;
}
#product-table_length {
	margin-left: 20px;
}
@media only screen and (max-width: 767px){
	#product-table_filter, #product-table_length {
	    margin: 0;
	}
}
@media only screen and (min-device-width : 320px) and (max-device-width : 480px){
			.nav.nav-pills {
			    height: 180px !important;
			}
			}
		@media only screen and (min-device-width : 411px)  and (-webkit-min-device-pixel-ratio : 2){
			.nav.nav-pills {
			    height: 130px !important;
			}
			}
			@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (-webkit-min-device-pixel-ratio : 2){
				.nav.nav-pills {
				    height: 0px !important;
				}
				}
				@media only screen and (min-device-width: 375px) and (-webkit-device-pixel-ratio: 3){
					.nav.nav-pills {
				    height: 130px !important;
				}
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
                <li class="breadcrumb-item">Products</li>
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
								<div class="card-block" id="profile_card">
				          <div class="row">
			              <div class="col-md-12">
			                  <div id="wizard">
			                     <section>
			                        <div class="container">
			                             <ul class="nav nav-pills">
			                            <li><a data-toggle="tab" href="#cinfo" class="active show">View Product</a></li>
			                            <li><a data-toggle="tab" href="#login-info">Add Product</a></li>
			                            <li><a data-toggle="tab" href="#cc-bill">Removed Product</a></li>
			                             </ul>
			                        </div> 
			                    </section> 
				                </div> 
				            </div> 
					        </div> 
					    	</div>
								@if(session()->has("message"))
									<div class="alert alert-info">
					  					{{ session()->get('message') }}
									</div>
								@endif
								@if($errors->any())
									<div class="alert alert-danger">
										<?php $err = $errors->all(); ?>
										{{ $err[0] }}
									</div>
								@endif
								<div class="tab-content responsive">
									<div id="cinfo" class="tab-pane fade active show">
										<form method="post" action="action/act_updateuser.php">
											<div class="border-box border-top-none-form">
												<div class="row Information-tab">
													<div class="col-lg-12 col-md-12 col-sm-12">
														<div class="border-box">
															<div class="row">
																<div class="col-md-12">
																	<div class="top-title border-bootm">
																		<h3 class="table-heading">View Products</h3>
																	</div>
																</div>
															</div>
															<div class="form-inline-form">										 
																<div class="table-responsive log-table">
																	<table class="table table-hover log-table" id="producttable">
																		<thead class="top-text">
																			<tr>
																				<th>#</th>
																				<th>Product Name</th>
																				<th>Description</th>
																				<th>Price</th>
																				<th>Weight</th>
																				<th>Quantity</th>
																				<th>Action</th>
																			</tr>
																		</thead>
																		<tbody>
																		@php
																			$serial = 1;
																		@endphp
																 		@foreach($productDetail as $pro)
																			<tr>

																				<td>{{ $serial }}</td>
																				<td>{{ $pro->ProductName }}</td>
																				<td>{{ $pro->ProductCartDesc }}</td>
																				<td>{{ $pro->ProductPrice }}</td>
																				<td>{{ $pro->ProductWeight }}</td>
																				<td>{{ $pro->ProductStock }}</td>
																				@php 
																					$valll =  $pro->ProductID; 
																				@endphp
																				<td><button type="button" onclick="updateProduct('{{ $valll }}')" class="btn btn-primary">Update</button><a type="button" href="act_removeProduct?pid={{ $valll }}" class="btn btn-danger">Remove</a>
																					<button type="button" onclick="setProductId('{{ $valll }}')" class="btn btn-primary addons" data-toggle="modal" data-target="#add_addons">Add Addons</button>
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
																				<th>Description</th>
																				<th>Price</th>
																				<th>Weight</th>
																				<th>Quantity</th>
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
										</form>
									</div>
									<!-- / Contact Information -->
									
									<div id="login-info" class="tab-pane fade">
										<form method="post" action="/action/act_addProduct" enctype="multipart/form-data">
											<input type="hidden" name="_token" value="{{ csrf_token() }}">
											<div class="border-box border-top-none">
												<div class="row Information-tab">
													<div class="col-lg-8 col-md-7 col-sm-12">
														<div class="border-box">
															<div class="row">
																<div class="col-md-12">
																	<div class="top-title border-bootm">
																		<h3 class="table-heading">Add Products</h3>
																	</div>
																</div>
															</div>
															<div class="form-inline-table">
																 
																<div class="row">
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Product Name</label>
																	</div>
																	<div class="col-lg-12">
																		<input class="form-control" name="productname" value="" type="text" required>
																	</div>
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Cart Description</label>
																	</div>
																	<div class="col-lg-12">
																		<input class="form-control" name="description" value="" type="text" required>
																	</div>
																</div>
																<div class="row">
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Short Description</label>
																	</div>
																	<div class="col-lg-12">
																		<textarea name="shortDesc" class="form-control"></textarea>
																	</div>
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Long Description</label>
																	</div>
																	<div class="col-lg-12">
																		<textarea name="longDesc" class="form-control"></textarea> 
																	</div>
																</div>
																<div class="row">
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Price</label>
																	</div>
																	<div class="col-lg-12">
																		<input class="form-control" name="price" value="" type="text">
																	</div>
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Weight</label>
																	</div>
																	<div class="col-lg-12">
																		<input class="form-control" name="weight" value="" type="text" required>
																	</div>
																</div>
																<div class="row">
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Category</label>
																	</div>
																	<div class="col-lg-12">
																		<select class="form-control" name="categorySel" id="categorySel">
																		  <option value="-1">--Select Category--</option>
																		  	@foreach($categories as $cat)

																				    <option value="{{ $cat->CategoryID }}">{{ $cat->CategoryName }}</option>
																			@endforeach

																		</select>
																	</div>
																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Quantity</label>
																	</div>
																	<div class="col-lg-12">
																		<input class="form-control" name="quantity" min="1" value="" type="number" required>
																	</div>
																</div>
																<div class="row">

																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Image</label>
																	</div>
																	<div class="col-lg-12">
																		<!-- <input class="form-control" name="image" value="" type="file" required> -->
																		<input type="file" name="image" required>
																	</div>
																	 
																</div>
																<div class="row">

																	<div class="col-lg-6 space-right">
																		<label class="category-heading">Alt Tag Text</label>
																	</div>
																	<div class="col-lg-8">
																		<input class="form-control" name="alt" value="" type="text" required>
																	</div>
																</div>
																<div class="row margint-30">
																	<div class="col-lg-12">
																		<button type="submit" class="btn btn-info btn-blue">Add Products</button>
																	</div>
																</div>
																 
															</div>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
									 
									<div id="cc-bill" class="tab-pane fade">
										<form method="post" action="action/act_updatecategory.php">
											<div class="border-box border-top-none-table">
												<div class="row Information-tab">
													<div class="col-lg-12 col-md-12 col-sm-12">
														<div class="border-box">
															<div class="row">
																<div class="col-md-12">
																	<div class="top-title border-bootm">
																		<h3 class="table-heading">Removed Products</h3>
																	</div>
																</div>
															</div>
															<div class="table-responsive log-table remove-product">
																<table class="table table-hover log-table" id="product-table">
																	<thead class="top-text">
																		<tr>
																			<th>#</th>
																			<th>Product Name</th>
																			<th>Description</th>
																			<th>Price</th>
																			<th>Weight</th>
																			<th>Quantity</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																	@php
																		$serial = 1;
																	@endphp
																	@foreach($productRDetail as $pro)
																		<tr>

																			<td> {{ $serial }}</td>
																			<td> {{ $pro->ProductName }}</td>
																			<td>{{ $pro->ProductCartDesc }}</td>
																			<td>{{ $pro->ProductPrice }}</td>
																			<td>{{ $pro->ProductWeight }}</td>
																			<td>{{ $pro->ProductStock }}</td>
																			@php 
																				$valll =  $pro->ProductID; 
																			@endphp
																			<td><a type="button" href="act_backToLiveProduct?pid={{ $valll }}" class="btn btn-danger">Set to Live</a></td>
																			 
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
																			<th>Description</th>
																			<th>Price</th>
																			<th>Weight</th>
																			<th>Quantity</th>
																			<th>Action</th>
															            </tr>
															        </tfoot>
																</table>
															</div>
														</div>
													</div>
													<!-- <div class="col-lg-4 col-md-5 col-sm-6 space-left">
														<div class="img-thumbnail cus-thumbnail">
															<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059949311!2d-74.25986972616433!3d40.69714940906666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1518003746633" height="255" frameborder="0" style="border:0; width:100%;" allowfullscreen></iframe>
														</div>
													</div> -->
												</div>
											</div>
										</form>
									</div>
									<!-- /Credit Card Billing Information -->
								</div>
							</div>
						</div>
						<!--   /////////////--   Add-ons Modal  ---///////////////////////////   --->
						<!-- Add -->
						<div class="modal fade" id="add_addons" tabindex="-1" role="dialog" aria-labelledby="add_addons" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						    <div class="modal-content">
							    <div class="modal-header">
							        <h5 class="modal-title" id="modalTitle"><b>Add Add-Ons On Products</b></h5>
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							          <span aria-hidden="true" data-dismiss="modal">&times;</span>
							        </button>
							      </div>
							      <div class="modal-body">
								  
									  <form class="form-horizontal" method="POST" action="act_addonsProduct" enctype="multipart/form-data">
								  		<input type="hidden" name="_token" value="{{ csrf_token() }}">
					          	<input type="Hidden" name="productid" id="addonProductId" value="">
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
					              	<select class="form-control" name="DiscountAddonsProduct">
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
					                <input type="Date" class="form-control" id="startDate" name="startDate" required>
					              </div>
					            </div>
					            <div class="form-group">
					              <label for="Start-Date" class="col-sm-3 control-label">End Date</label>
					              <div class="col-sm-9">
					                <input type="Date" class="form-control" id="startDate" name="endDate" required>
					              </div>
					            </div>
					            <div class="modal-footer">
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									        <input type="submit" name="addProductAddons" value="Add Addons" class="btn btn-primary">
								      </div>
							      </form>
					        </div> 
						    </div>
							</div>
						</div>
						<!---    /////////////////////////////////////////////   -->

						<!-- The Modal -->
						<div id="myModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="add_addons" aria-hidden="true">
						  <!-- Modal content -->
							<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
							  <div class="modal-content">
							  	<div class="modal-header">
								  	<h3>Update Product</h3>
								    <span class="close" data-dismiss="modal">&times;</span>
								  </div>
								  <div class="modal-body">
								  	<form method="post" action="act_updateProduct" enctype="multipart/form-data">
											<input type="hidden" name="_token" value="{{ csrf_token() }}">	
											<input type="Hidden" name="productId" id="productId" value="">		 
											<div class="row">
												<div class="col-lg-2 space-right">
													<label>Product Name</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" name="uproductname" id="uproductname" value="" type="text" required>
												</div>
												<div class="col-lg-2 space-right">
													<label>Cart Description</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" name="udescription" id="udescription"  value="" type="text" required>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-2 space-right">
													<label>Short Description</label>
												</div>
												<div class="col-lg-4">
													<textarea name="ushortDesc" id="ushortDesc" class="form-control" rows="4"></textarea>
												</div>
												<div class="col-lg-2 space-right">
													<label>Long Description</label>
												</div>
												<div class="col-lg-4">
													<textarea name="ulongDesc" id="ulongDesc" class="form-control" rows="6"></textarea> 
												</div>
											</div>
											<div class="row">
												<div class="col-lg-2 space-right">
													<label>Price</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" name="uprice" id="uprice"  value="" type="text">
												</div>
												<div class="col-lg-2 space-right">
													<label>Weight</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" name="uweight" id="uweight" value="" type="text" required>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-2 space-right">
													<label>Category</label>
												</div>
												<div class="col-lg-4">
													<select class="form-control" name="ucategorySel" id="categorySel">
													  <option value="-1">--Select Category--</option>
													  	@foreach($categories as $cat)

															    <option value="{{ $cat->CategoryID }}">{{ $cat->CategoryName }}</option>
														@endforeach

													</select>
												</div>
												<div class="col-lg-2 space-right">
													<label>Quantity</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" name="uquantity" id="uquantity" min="1" value="" type="number" required>
												</div>
											</div>
											 <div class="row">

												<div class="col-lg-2 space-right">
													<label>Image</label>
												</div>
												<div class="col-lg-5">
													<input type="file" name="image" id="uimage" multiple>
													<!-- <input class="form-control" name="uimage" id="uimage" value="" type="file" > -->
												</div>
												 
											</div>
											<div class="modal-footer">
												<div class="row margint-30">
													<div class="col-lg-12">
														<button type="submit" class="btn btn-info btn-blue">Update Products</button>
													</div>
												</div>
											</div>
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

@endsection
@section('script')
<script type="text/javascript">

	$(document).ready(function(){
		$('#product-table').DataTable();
	});
	$('#categorySel').on('change', function() {
		console.log($("#categorySel option:selected").text());
		var str = $("#categorySel option:selected").text();
  		$('#updatename').val(str);
	});

	function setProductId(e)
	{
		$('#addonProductId').val(e);
	}


</script>
@endsection