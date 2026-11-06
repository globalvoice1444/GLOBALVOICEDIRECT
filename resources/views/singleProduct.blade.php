@extends('layouts.gvd-v3')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/gvd-v4.css') }}?v=20261101c">
@endsection
@section('content')
@verbatim
@endverbatim
@if($productDet)
<section style="padding-top:140px"><div class="wrap two" style="align-items:start">
<div class="gb" style="padding:40px;display:grid;place-items:center;background:#fff"><img class="heroimg" style="max-width:380px" src="{{ asset('/productImages/' . $productDet->ProductImage)}}" alt="{{ $productDet->ProductName }}"></div>
<div><span class="eb">Our Features</span><h1 style="margin-top:14px;font-size:44px">{{ $productDet->ProductName}}</h1><p class="lead">{{ $productDet->ProductCartDesc }}</p>
<div class="gb card" style="padding:24px;margin:20px 0"><div class="sum"><div><span>Product code :</span><b>{{ $productDet->ProductSKU}}</b></div><div><span>Product Price :</span><span style="color:#C23A00">{{ $productDet->ProductPrice}}</span></div></div></div>
<form method="post" action="/cart/postcart">{{ csrf_field() }}<input type="hidden" name="id" value="{{ $productDet->ProductID}}">
<div style="display:flex;gap:12px;align-items:center"><label for="quantity" style="margin:0">Qty</label><div class="in" style="width:90px;margin:0"><input id="quantity" name="quantity" type="number" value="1" min="1" max="10"></div><button type="submit" class="btn p lg" style="border:0;cursor:pointer;font:inherit;font-weight:600">Add to cart @verbatim<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>@endverbatim</button></div></form>
<div class="tabs" style="display:flex;gap:8px;margin-top:34px"><a class="btn p" href="#description" data-tab="description">Description</a><a class="btn g" href="#Specific" data-tab="Specific">Specifications</a></div>
<div class="gb card" style="padding:24px;margin-top:12px"><div id="description"><p style="margin:0">{{ $productDet->ProductLongDesc}}</p></div><div id="Specific" style="display:none"><p style="margin:0">{{ $productDet->ProductLongDesc}}</p></div></div></div></div></section>
@else
<div class="phead center"><div class="wrap"><h1>Oops!</h1><h2>Product Not Found</h2><p class="lead">Sorry, Requested Product not found!</p></div></div>
@endif
@verbatim
<section style="padding-top:40px"><div class="wrap"><div class="band gb"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg" style="opacity:.6"></div><h2>Make the switch to a VoIP phone system to cut costs</h2><p>Why not start right now? Join and start taking advantage of being a member of Global Voice Direct.</p><div class="ctas"><a class="btn w lg" href="https://app.globalvoicedirect.com/chose-pakage" data-gtm="click_signup">Join Now <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><a class="btn g lg" href="tel:+18773085803" data-gtm="click_phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>Call 1-877-308-5803</a></div></div></div></section>

@endverbatim
@endsection
@push('scripts')
<script>document.querySelectorAll('.tabs [data-tab]').forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();document.querySelectorAll('.tabs [data-tab]').forEach(function(x){x.className='btn g';document.getElementById(x.dataset.tab).style.display='none'});a.className='btn p';document.getElementById(a.dataset.tab).style.display='block'})});</script>
@endpush
