@extends('layouts.gvd-v3')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/gvd-v4.css') }}?v=20261101c">
@endsection
@section('content')
@verbatim

<div class="phead center"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg"></div><div class="wrap"><span class="eb rv">OUR SPECIALTY</span><h1 class="rv d1"><span class="grad">Our Best Selling Products</span></h1><p class="lead rv d2">Voip Phones</p></div></div>
@endverbatim
<section style="padding-top:40px"><div class="wrap"><div class="cats"><a class="{{ request()->has('catId') ? '' : 'on' }}" href="/store">All Products</a>@if($rowListsCount > 0)@foreach($stmtLists as $list)<a class="{{ request()->catId == $list->CategoryID ? 'on' : '' }}" href="/store?catId={{$list->CategoryID}}">{{$list->CategoryName}}</a>@endforeach @endif</div>
@if($productListsCount > 0)
<div class="prods">@foreach($productListsQueryExecute as $productList)<div class="gb prod rv"><div class="im"><img src="/public/productImages/{{$productList->ProductImage}}" alt="{{$productList->ProductName}}"></div><h4>{{$productList->ProductName}}</h4><div class="pr">@if($productList->ProductID == $productList->product_id)<del style="font-size:15px;color:#69737E;font-weight:500;margin-right:6px">${{$productList->ProductPrice}}</del>@if($productList->addonFixedPrice !=null)${{$productList->addonFixedPrice}}.@else<?php $discount = $productList->addonDiscount*$productList->ProductPrice/100; ?>${{$productList->ProductPrice-$discount}}@endif @else${{$productList->ProductPrice}}@endif</div><div class="act"><a class="btn g" href="single-product?id={{$productList->ProductID}}">Quick View</a><a class="btn p" href="/cart/cart?id={{$productList->ProductID}}">Add to cart</a></div></div>@endforeach</div>
@else
<div><h2 class="center">No Product Found!</h2></div>
@endif
@if($totalPages > 1)<div class="pager">@if(isset(request()->catId))@if($page > 1)<a href="store?catId={{ request()->catId }}&page={{ $page-1 }}">&lsaquo;</a>@endif @for($count = 1; $count <= $totalPages; $count++)<a class="{{request()->page == $count ? 'on': ''}}" href="store?catId=request()->catId&page=<?php echo $count; ?>"><?php echo $count ?></a>@endfor @if($page < $totalPages)<a href="store?catId= {{request()->catId}}&page=<?php echo $page+1; ?>">&rsaquo;</a>@endif @else @if($page > 1)<a href="store?page=<?php echo $page-1; ?>">&lsaquo;</a>@endif @endif<?php for($count = 1; $count <= $totalPages; $count++){ if(!isset($_GET['page'])){ $page = 1; } ?><a class="<?php if($page == $count){ echo 'on';} ?>" href="store?page=<?php echo $count; ?>"><?php echo $count ?></a><?php } if($page < $totalPages){ ?><a href="store?page=<?php echo $page+1; ?>">&rsaquo;</a><?php } ?></div>@endif
</div></section>
@verbatim
<section style="padding-top:40px"><div class="wrap"><div class="band gb"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg" style="opacity:.6"></div><h2>Make the switch to a VoIP phone system to cut costs</h2><p>Why not start right now? Join and start taking advantage of being a member of Global Voice Direct.</p><div class="ctas"><a class="btn w lg" href="https://app.globalvoicedirect.com/chose-pakage" data-gtm="click_signup">Join Now <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><a class="btn g lg" href="tel:+18773085803" data-gtm="click_phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>Call 1-877-308-5803</a></div></div></div></section>

@endverbatim
@endsection
