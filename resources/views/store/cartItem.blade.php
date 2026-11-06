@extends('layouts.gvd-v3')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/gvd-v4.css') }}?v=20261101c">
@endsection
@section('content')
@verbatim

<div class="phead center"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg"></div><div class="wrap"><span class="eb rv">Cart Items</span><h1 class="rv d1"><span class="grad">Your shopping cart contains:</span></h1><p class="lead rv d2"></p></div></div>
@endverbatim
<?php $total=0;?>
<section style="padding-top:30px"><div class="wrap cartg" style="display:grid;gap:24px;align-items:start">
<div class="gb card" style="padding:26px;overflow:auto"><h3 style="margin-top:0">Products</h3><table class="cart cartlive"><thead><tr><th>S.No</th><th>Product</th><th>Quantity</th><th>Product Name</th><th>Service Charges</th><th>Price</th><th>Total</th><th>Remove</th><th>Update</th></tr></thead>
						<?php
		               
		        	//$_SESSION["cart_item"]
							if(session()->has('cart_item')){
						    $item_total = 0;
						    $total = 0;
							$i=1;
		                
						  foreach (session()->get('cart_item') as $item){
						    $id = $item["code"];
						     	$stmtLists =App\Http\Controllers\storeController::fetchItems($id);
						   
								$rowListsCount = count($stmtLists);

								if($rowListsCount > 0){
									foreach($stmtLists as $list){
										?>
										<form method="post" action="updateqty">
											{{csrf_field()}}
											<tr>
												<td><?php echo $i; ?></td>
												<td class="invert-image">
													<a href="#">
														<img src="/productImages/<?php echo $list->ProductImage?>" alt=" " style="max-width:60px;max-height:60px" />
													</a>
												</td>
												<td class="invert">
													<div class="quantity">
														<div class="quantity-select">
															<div class="form-group product-actions">
																<input type="number" class="form-control" style="width:70px;padding:8px;border:1px solid #D9E1E8;border-radius:10px" name="qty1" min="1" value="<?php echo $item["quantity"];?>">

																<input type="hidden" name="id" value="<?php echo $id; ?>" />
															</div>
														</div>
													</div>
												</td>
												<td class="invert"><?php echo $list->ProductName; ?></td>
												<td class="invert">$0</td>
												<td>
													<?php if($list->ProductID == $list->product_id)
													{
														if($list->addonFixedPrice != null)
														{
															$proPrice = $list->addonFixedPrice;
															echo "$".$proPrice;
														}
														else
														{
															$discount = $list->ProductPrice * $list->addonDiscount/100;
															$proPrice = $list->ProductPrice - $discount;
															echo "$".$proPrice;
														}
														?>
														<td class="invert">$<?php echo number_format(($item["quantity"] * $proPrice),2);?></td>

														<?php
													} 
													else
													{
														?>
														$<?php echo $list->ProductPrice; ?>
														</td>
														<td class="invert">$<?php echo number_format(($item["quantity"] * $list->ProductPrice),2);?></td>
														<?php 
													}
													?>
												<td><a href="delcart?remove=<?php echo $id; ?>">
														<span class="btn g" style="padding:6px 12px;font-size:13px">Remove</span>
													</a>
												</td>
												<td>
													<button type="submit" id="updatequantity" name="updateqnty" class="btn p" style="border:0;cursor:pointer;font:inherit;padding:6px 12px;font-size:13px">Update</button>
												</td>
											</tr>
										</form>

										<?php
										if($list->ProductID == $list->product_id)
										{
											$total = $total + ($item["quantity"] * $proPrice);
											$i++;
										}
										else
										{
											$total = $total + ($item["quantity"] * $list->ProductPrice);
											$i++;
										}
									}
								}
							}
							?>

						<?php } ?>
					</table></div>
<div class="gb card sum" style="padding:26px"><h4 style="margin:0 0 6px">Continue to Checkout</h4><div><span>Sub Total</span><span><?php echo number_format($total,2);?></span></div><div><span>Service Charges</span><span>$0.00</span></div><div><span>All Total</span><span>$<?php echo number_format($total,2); ?></span></div>
<a class="btn p lg" style="width:100%;justify-content:center;margin-top:16px" href="/checkout/">Checkout @verbatim<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>@endverbatim</a><a class="btn g" style="width:100%;justify-content:center;margin-top:10px" href="/store">Continue Shopping</a></div></div></section>
@verbatim
<section style="padding-top:40px"><div class="wrap"><div class="band gb"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg" style="opacity:.6"></div><h2>Make the switch to a VoIP phone system to cut costs</h2><p>Why not start right now? Join and start taking advantage of being a member of Global Voice Direct.</p><div class="ctas"><a class="btn w lg" href="https://app.globalvoicedirect.com/chose-pakage" data-gtm="click_signup">Join Now <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><a class="btn g lg" href="tel:+18773085803" data-gtm="click_phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>Call 1-877-308-5803</a></div></div></div></section>

@endverbatim
@endsection
