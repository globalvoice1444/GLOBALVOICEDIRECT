@extends('admin.layouts.layout')
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
                    <li class="breadcrumb-item">Categories</li>
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
		               	@if(session()->has("message"))
							<div class="alert alert-info">
									{{ session()->get('message') }}
							</div>
						@endif
		                <div class="card-header">
		                </div>
		      
			<div class="card-block" id="profile_card">

            <div class="row">
               <div class="col-md-12">
                  <div id="wizard">
                     <section>
                        <div class="container">
                             <ul class="nav nav-pills">
                            <li><a data-toggle="tab" href="#cinfo" class="active show">View Category</a></li>
                            <li><a data-toggle="tab" href="#login-info">Add Category</a></li>
                            <li><a data-toggle="tab" href="#cc-bill">Update Category</a></li>
                             </ul>
                        </div> 
                    </section> 
	                </div> 
	            </div> 
	        </div> 
	    </div>
			
			<div class="tab-content responsive">
				<div id="cinfo" class="tab-pane fade active show">
					<form method="post" action="">
						<div class="border-box border-top-none">
							<div class="row Information-tab">
								<div class="col-lg-8 col-md-7 col-sm-6">
									<div class="border-box">
										<div class="row">
											<div class="col-md-12">
												<div class="top-title border-bootm">
													<h3 class="table-heading">View Categories</h3>
												</div>
											</div>
										</div>
										<div class="form-inline">
											 
											 
											<div class="table-responsive log-table">
												<table class="table table-hover log-table" id="log-table">
													<thead>
														<tr class="head-tag">
															<th>#</th>
															<th>Category Name</th>
														</tr>
													</thead>
													<tbody>
														@php
															$sr = 1;
														@endphp
														@foreach($categories as $cat)
															<tr>
																<td>{{ $sr }}</td>
																<td>{{ $cat->CategoryName }}</td>
															</tr>
															@php
																$sr++;
															@endphp


														@endforeach
													
														 
													</tbody>
												</table>
											</div>
											 
										</div>
									</div>
								</div>
								<div class="col-lg-4 col-md-5 col-sm-6 space-left">
									<div class="img-thumbnail cus-thumbnail">
										<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059949311!2d-74.25986972616433!3d40.69714940906666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1518003746633" height="255" frameborder="0" style="border:0; width:100%;" allowfullscreen></iframe>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<!-- / Contact Information -->
				
				<div id="login-info" class="tab-pane fade">
					<form method="post" action="act_addcategory">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="border-box border-top-none">
							<div class="row Information-tab">
								<div class="col-lg-8 col-md-7 col-sm-6">
									<div class="border-box">
										<div class="row">
											<div class="col-md-12">
												<div class="top-title border-bootm">
													<h3 class="table-heading">Add Category</h3>
												</div>
											</div>
										</div>
										<div class="form-inline-table">
											 
											<div class="row">
												<div class="col-lg-4 space-right">
													<label class="category-heading">Category Name</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="categoryname" id="categoryname" value="" type="text">
												</div>
												 
											</div>
											 
											<div class="row margint-30">
												<div class="col-lg-12">
													<button type="submit" class="btn btn-info btn-blue">Add Category</button>
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
					<form method="post" action="act_updatecategory">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="border-box border-top-none">
								<div class="row Information-tab">
									<div class="col-lg-8 col-md-7 col-sm-6">
										<div class="border-box">
											<div class="row">
												<div class="col-md-12">
													<div class="top-title border-bootm">
														<h3 class="table-heading">Update Category</h3>
													</div>
												</div>
											</div>
											<div class="form-inline-table">
												 
												<div class="row">
													 <div class="col-lg-6 space-right">
														<label class="category-heading">Select Category</label>
													</div>
													<div class="col-lg-12">
														<div class="form-group">
  
														  <select class="form-control" name="categorySel" id="categorySel">
														  <option value="-1">--Select Category--</option>
														  @foreach($categories as $cat)
														  	<option value= "{{ $cat->CategoryID }}">{{ $cat->CategoryName }}</option>
														  @endforeach
														  	

														  </select>
														</div>
 
													</div>
												</div>

												<div class="row">
													<div class="col-lg-6 space-right">
														<label class="category-heading">Category Name</label>
													</div>
													<div class="col-lg-12">
														<input class="form-control" name="updatename" id="updatename" value="" type="text">
													</div>
													 
												</div>
												
												<div class="row margint-30">
													<div class="col-lg-12">
														<button type="submit" class="btn btn-info btn-blue">Update</button>
													</div>
												</div>

												 

											</div>
										</div>
									</div>
									<div class="col-lg-4 col-md-5 col-sm-6 space-left">
										<div class="img-thumbnail cus-thumbnail">
											<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059949311!2d-74.25986972616433!3d40.69714940906666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1518003746633" height="255" frameborder="0" style="border:0; width:100%;" allowfullscreen></iframe>
										</div>
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
				<!-- /Credit Card Billing Information -->
			</div>
		</div>
	</div>



</div>
@endsection


@section('script') 

<script type="text/javascript">
	$('#categorySel').on('change', function() {
		console.log($("#categorySel option:selected").text());
		var str = $("#categorySel option:selected").text();
  		$('#updatename').val(str);
	})
</script>
@endsection