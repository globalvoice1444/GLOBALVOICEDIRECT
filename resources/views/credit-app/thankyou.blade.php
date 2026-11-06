@extends('layouts.app_old')
@section('css')
<style type="text/css">
   .p-center-align {
   		text-align: center;
   }
   .jumbotron {
   		background-color: #fff;
   }
</style>
@endsection
@section('scripts')
 
@endsection
@section('content')
@if(isset($message))
<div class="jumbotron text-center">
  <h1 class="display-3">Thank You!</h1>
  <p class="lead p-center-align"><strong>Please check your email</strong> for further instructions on how to complete your account setup.Click the activation link to activate you account.</p>
  <hr>
  <p class="p-center-align">
    Having trouble? <a href="https://globalvoicedirect.com/support/">Contact us</a>
  </p>
  <p class="lead p-center-align">
    <a class="btn btn-primary btn-sm" href="/" role="button">Continue to homepage</a>
  </p>
</div>
@else
	<div class="jumbotron text-center">
	  <h1 class="display-3">Thank You!</h1>
	  <p class="lead p-center-align"><strong></p>
	  <hr>
	  <p class="p-center-align">
	    Having trouble? <a href="https://globalvoicedirect.com/support/">Contact us</a>
	  </p>
	  <p class="lead p-center-align">
	    <a class="btn btn-primary btn-sm" href="/" role="button">Continue to homepage</a>
	  </p>
	</div>
@endif

@if(isset($message) and isset($orderid))
	{{-- GVD 2026-11-06: signup_complete only on post-payment redirect (RegisterController redirects here with message+orderid after successful checkout) --}}
	<script>window.dataLayer=window.dataLayer||[];window.dataLayer.push({event:'signup_complete',funnel_step:'signup_complete'});
	if(!window.google_tag_manager||!window.google_tag_manager['GTM-KDJWX9X9']){(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KDJWX9X9');}</script>
	<?php $sscid = ! empty( $_COOKIE['shareasaleSSCID'] ) ? $_COOKIE['shareasaleSSCID'] : ''; ?>
	<img src="https://www.shareasale.com/sale.cfm?tracking={{$orderid}}&amount=149&merchantID=96812&transtype=sale&sscidmode=6&sscid={{$sscid}}" width="1" height="1">

	@push('scripts')
	 <script defer async type="text/javascript" src="https://shareasale-analytics.com/j.js"></script>
	 <script type="text/javascript">
	 	function shareasaleGetCookie(e){var r=e+"=";var a=decodeURIComponent(document.cookie);var o=a.split(";");for(var n=0;n<o.length;n++){var t=o[n];while(t.charAt(0)==" "){t=t.substring(1)}if(t.indexOf(r)==0){return t.substring(r.length,t.length)}}return""}
	 </script>
	@endpush
@endif
@endsection
