@extends('layouts.user.user_main')
@section('content')




<div class="col-sm-12">

	<div class="card">

		<div class="card-header">

			<h5>Payments</h5>

		</div>

		<div class="card-block" id="payments_card">

			<div class="row">

				<div class="col-md-12">

					<div id="wizard">

						<section>

							<table class="table table-responsive" id="payments-table">

								<thead>

									<tr>

										<th>#</th>

										<th>Date</th>

										<th>Amount</th>

										<th>Status</th>

									</tr>

								</thead>

								<tbody>

									<?php
                                    
										$serial = 1;

										foreach($rowLists as $row)
										{ 
							 		 ?>

							 		<tr>

										<td><?php echo $serial; ?></td>

										<td><?php echo $row->created_at ?></td>

										<td>$<?php echo $row->transaction_amount ?></td>

										<td><span class="paid_status">Paid <?php echo $row->created_at; ?>- Thank you!</span></td>

									</tr>

									<?php $serial = $serial + 1;} ?>



									<!-- <tr>

										<td>1</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="paid_status">PAID</span></td>

									</tr>

									<tr>

										<td>2</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="paid_status">PAID</span></td>

									</tr>

									<tr>

										<td>3</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="paid_status">PAID</span></td>

									</tr>

									<tr>

										<td>4</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="paid_status">PAID</span></td>

									</tr>

									<tr>

										<td>5</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="paid_status">PAID</span></td>

									</tr>

									<tr>

										<td>6</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="pending_status">PENDING</span></td>

									</tr>

									<tr>

										<td>7</td>

										<td>01/18/2019</td>

										<td>$241.23</td>

										<td><span class="pending_status">PENDING</span></td>

									</tr> -->

								</tbody>

							</table>

						</section>

					</div>

				</div>

			</div>

		</div>

	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

	/*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');

	breadcrumb.text('Payments');

	breadcrumb.prop("href", "/payment")*/
	var breadcrumb = $('.breadcrumb');
            breadcrumb.append('<li class="breadcrumb-item"><b><span>Payments</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="/payment"]').parents().addClass('active');

	$(document).ready( function () {

	    $('#payments-table').DataTable();

	} );

</script>
@endpush