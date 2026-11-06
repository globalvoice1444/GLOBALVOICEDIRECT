@extends('layouts.app')
@section('css')
<style type="text/css">
	#referral-table_paginate,#referral-table_filter {
	    float: right;
	}
	#referral-table_length label {
	    display: inline-flex !important;
	}
	#referral-table_length select {
	    margin: 0px 10px;
	}
	#referral-table_info {
	    margin-top: 30px;
	    margin-left: 8px;
	}
	#referral-table {
	    margin-top: 15px;
	}
</style>
@stop
@section('content')

<div class="main-dashboard-container">

	<div class="container">

		<div class="dashboard-container">

			<div class="row">

				<div class="col-md-8">

					<?php if(isset($_GET['error'])) { ?>

						<div class="alert alert-danger">

						  <b>Error!</b> <?php echo $_GET['error']; ?>

						</div>

					<?php }if(isset($_GET['message'])){ ?>

						<div class="alert alert-success">

						  <?php echo $_GET['message']; ?>

						</div>

					<?php } ?>
					@if(session()->has('error'))

						<div class="alert alert-danger">

						  <b>Error!</b> {{session()->get('error')}}

						</div>
					@endif
					@if(session()->has('message'))

						<div class="alert alert-success">

						  {{session()->get('message')}}

						</div>

					@endif

					<h2 class="referral-title">AFFILIATE DASHBOARD</h2>

					<div class="referral-container">

						<div class="referral-table">

							<table class="table" id="referral-table">

								<thead>

									<tr>

										<th>No.</th>

										<th>Referral Name</th>

										<th>Joined Date</th>

										<th>Reward Status</th>

									</tr>

								</thead>

								<tbody>

									@php

									$count = 1;

									@endphp

									@foreach($affiliateusers as $user)

										<tr>

											<td>{{$count++}}</td>

											<td>{{$user->user_nicename}}</td>

											<td>{{$user->created_at}}</td>

											@if ($user->rewards_status == 0)

												<td><span class="badge pending">Pending</span></td>

											@else

												<td><span class="badge success">Success</span></td>

											@endif

										</tr>

									@endforeach

								</tbody>

							</table>

						</div>

					</div>

				</div>

				<div class="col-md-4">

					<div class="Balance-area">

						<form action="aff-withdraw" method="post" class="form-inline">

							{{csrf_field()}}

							<input type="hidden" name="affiliate_id" value="{{$affiliateBalance->affiliate_id??0 }}">

							<button class="btn withdraw-btn btn-3 btn-3e" type="submit" <?php if (isset($affiliateBalance->balance)) {
									if ($affiliateBalance->balance < 10) {
										echo 'disabled';
									}
							}else{
								echo 'disabled';
							}  ?>>Withdraw</button>

						</form>

						<label class="balance-lbl">Balance:</label>

						<span class="balance-amount"><sup>$</sup>{{$affiliateBalance->balance??0}}</span>

					</div>

					<div class="affiliate-vector">

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

@stop

@section('scripts')
<script type="text/javascript">
	$('#referral-table').DataTable();
</script>

@stop