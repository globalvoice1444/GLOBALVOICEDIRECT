@extends('layouts.app')
@section('css')
<!-- GTM (funnel tracking, added 2026-11-06) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KDJWX9X9');</script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/gvd-checkout-skin.css?v=20261106">
<style>.main-footer,.footer-bottom,.header-wrap .page-title{display:none!important}body{background:#F5F7FA}</style>
@endsection
@section('content')
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDJWX9X9" height="0" width="0" style="display:none"></iframe></noscript>
<script>document.addEventListener('DOMContentLoaded',function(){window.dataLayer=window.dataLayer||[];dataLayer.push({event:'funnel_view',funnel_step:'membership'});document.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('a.gvd-big');if(a){dataLayer.push({event:'start_membership',funnel_step:'membership'});}},true);});</script>
<section class="header-wrap">
  <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
      <div class="container">
          <h2>Become A Member</h2>
      </div>
  </section>
  @include('includes.gvd-checkout-header')
</section>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<?php
  // Display-only: session keeps the raw JSON (RegisterController json_decodes it).
  $gvdPlans = [0=>['Small Business Standard',24.95],1=>['Small Business Premium',49.95],2=>['Enterprise',84.95]];
  $gvdSel = $_SESSION["SelectNumberInfo"] ?? null;
  $gvdNums = [];
  if ($gvdSel) { $raw = json_decode($gvdSel['number'] ?? '', true); $raw = is_array($raw) ? $raw : [$gvdSel['number'] ?? ''];
    foreach ($raw as $n) { $d = preg_replace('/\D/','',(string)$n); if (strlen($d)==11 && $d[0]=='1') $d = substr($d,1);
      $gvdNums[] = strlen($d)==10 ? '('.substr($d,0,3).') '.substr($d,3,3).'-'.substr($d,6) : e($n); } }
  $gvdPlan = ($gvdSel && isset($gvdPlans[(int)$gvdSel['package']])) ? $gvdPlans[(int)$gvdSel['package']] : null;
?>

<main class="gvd-co"><section class="gvd-hero"><span class="eyebrow pop">STEP 1 OF 4 &middot; MEMBERSHIP</span><h1 class="pop d1">Are You Ready To Become A Member?</h1><p class="pop d2">One small yearly fee unlocks your executive phone system, business-credit reporting and our full marketing toolkit.</p></section>
<div class="gvd-wrap"><div class="gvd-grid"><div class="gvd-card pop d1"><h2>Benefits of membership</h2><p class="mut">State of the art phone system with a boatload of features, and monthly payments reported to the major business credit bureaus.</p>
<ul class="gvd-list"><li>Financing option</li><li>Free marketing tool, 40+ business directory listings</li><li>Free email marketing / campaigns</li><li>Free script recording</li><li>Free fax service</li><li>SMS marketing</li></ul>
<p class="mut">Voice Broadcasting Platform: send your pre-recorded message anywhere in the USA and Canada. You might be approved for financing up to $7,500 with our easy Net 30 business credit line, without personal guarantees.</p>
								<?php 
									if(isset($_GET['referal_token'])){
										echo  '<a class="gvd-big" data-gtm="click_signup" href="credit-apps?referal_token='.$_GET['referal_token'].'">Become a member, $149/year &rarr;</a>';
									}elseif (isset($_GET['non-memberreferal_token'])) {
										echo  '<a class="gvd-big" data-gtm="click_signup" href="credit-apps?non-memberreferal_token='.$_GET['non-memberreferal_token'].'">Become a member, $149/year &rarr;</a>';
									} else{
										echo  '<a class="gvd-big" data-gtm="click_signup" href="credit-apps">Become a member, $149/year &rarr;</a>';
									}
								?>
</div><aside class="gvd-sum pop d2"><h3>ORDER SUMMARY</h3>
<?php if ($gvdPlan) { ?><div class="r"><span>Plan</span><b><?php echo e($gvdPlan[0]) ?></b></div><div class="r"><span>Monthly</span><b>$<?php echo number_format($gvdPlan[1],2) ?>/mo</b></div><?php } ?>
<?php if ($gvdNums) { ?><div class="r"><span>Your number</span><b><?php echo implode(', ', $gvdNums) ?></b></div><?php } ?>
<div class="r tot"><span>Due today</span><b>$149.00</b></div>
<div class="note">Annual membership. Your number locks in once payment completes.</div>
<div class="gvd-trust"><span>&#128274; Stripe secured</span><span>&#128200; Reports to business credit bureaus</span></div></aside></div></div></main>
@include('includes.gvd-checkout-footer')
@endsection