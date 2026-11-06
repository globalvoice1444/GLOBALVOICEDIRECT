@extends('layouts.app_old')
@section('content')
	
<div class="store-banr">
	<h2>VoIP phone service and phone systems</h2>
	<!-- <img src="images/slide-img.png" alt=""> -->
</div>

<div class="products store-page">
	

	<!--Our Products-->
    <section id="ourproducts">
        <div class="content-box-md">
        	<div class="container">
        		<div class="row">
                    <div class="col-md-12 text-center wow slideInDown" style="visibility: visible;animation-name: slideInDown;">
                        <div class="horizontal-heading">
                            <h2>Search Result: "{{$searched}}"</h2>
                        </div>
                    </div>      
                </div>
            </div>
        </div>
        <div id="items">
        	<div class="content-box-md">
        		<div class="container">
        			<div class="row">
        				<div class="col-md-1 product-category-sidebar">
        				
						</div>
	                    
							<!-- while($rowLists = $productLists->fetch()){?> -->
							<div class="col-md-10 product-section">
								<div class="row product-section-container">
									@if($productListsCount > 0)
										@foreach($execute as $productList)

												<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 wow fadeInLeft addon-tag" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeInLeft;">
											@if($productList->ProductID == $productList->product_id)
													<div class="addon-saletag-home">
														<img class="img-fluid" src="productImages/addonProductImages/Sale-tag.png">
													</div>
											 @endif
												<!-- Product item 01 -->
												<div class="products-item">
			                    					<div class="products-item-img" >
					                    			  <a href="/cart/cart?id= {{$productList->ProductID}}" title="">

														<img class="img-fluid" src="productImages/{{$productList->ProductImage}}" alt="{{$productList->ProductName}}">
														</a>
														<div class="hover-box">
															<ul class="add-to-links">
																<li>
																	<a href="single-product.php?id={{$productList->ProductID}}" class="quick-view">Quick View</a>
																</li>
																<li>
																	<a href="#" class="link-wishlist">Wishlist</a>
																</li>
															</ul>
														</div>
			                    					</div>
			                    					<p>{{$productList->ProductName}}</p>
			                    					@if($productList->ProductID == $productList->product_id)
			                    						<del>${{$productList->ProductPrice}}</del>
														@if($productList->addonFixedPrice !=null)
														<h2>${{$productList->addonFixedPrice}}.</h2>
														@else
														<?php $discount = $productList->addonDiscount*$productList->ProductPrice/100;
															?> <h2>${{$productList->ProductPrice-$discount}}</h2>
														@endif
													@else
					                    				<h2>${{$productList->ProductPrice}}</h2>
					                    			@endif
					                    			<div id="products-btn">
			                    						<a class="btn btn-item1 btn-products1" href="/cart/cart?id={{$productList->ProductID}}" title="ADD TO CART" role="button">ADD TO CART</a>
			                						</div>
		                						</div>
		                					</div>
									   
										@endforeach
									   @else
										<div><h2 class="text-center">No Product Found!</h2></div>					
									@endif
								</div>
								<div class="pagination-area ">
									<ul>
										
				            			@if($totalPages > 1)

					            		    @if(isset(request()->catId))

					            				@if($page > 1)
					            					<li><a class="page-Btn-p" href="store?catId={{ request()->catId }}&page={{--$page}}" style="width: 65px; margin-left: -36px;">Previous</a></li>
					            					<!-- page if -->	
					            				@endif
					            				@for($count = 1; $count <= $totalPages; $count++)
					            					<li><a class="@if(request()->page == $count)
					            					"active"
					            						@endif 
					            						href="store?catId=request()->catId&page=<?php echo $count; ?>"><?php echo $count ?></a></li>
					            				@endfor	
					            				@if($page < $totalPages)
					            					<li class="page-Btn-n"><a class="pageBtn" href="store?catId= {{request()->catId}}&page=<?php echo $page+1; ?>" style="width: 50px;">Next</a></li>
					            				@endif
					            			
					            			 @else
					            				@if($page > 1)
						            	 			<li class="nxtBtnPage"><a class="page-Btn-p" href="store?page=<?php echo $page-1; ?>" style="width: 65px; margin-left: -36px;">Previous</a></li>
						            	 		@endif
						            	 	@endif		
					            	 		<?php for($count = 1; $count <= $totalPages; $count++){
					            	 			if(!isset($_GET['page']))
						            			{
						            				$page = 1;
						            			}?>
						            			<li><a class="<?php if($page == $count){ echo 'active';} ?>" href="store?page=<?php echo $count; ?>"><?php echo $count ?></a></li><?php
							            		}
							            		if($page < $totalPages){?>
							            		
							            		
							            			<li class="page-Btn-n"><a class="pageBtn" href="store?page=<?php echo $page+1; ?>" style="width: 50px;">Next</a></li><?php
							            		}?>
								        @endif
						        	</ul>
					    		</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
										        							
@endsection