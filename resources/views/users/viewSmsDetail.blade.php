@extends('layouts.user.user_main')
@section('css')

<link rel="stylesheet" type="text/css" href="css/smsCampaign.css">

<style type="text/css">

	.modal_show_sms_detail_header {

	    background-color: #4fc3f7;

	    color: #fff;

	}

	.viewSmsWizard{

	    border: 1px solid #ccc;

	    padding: 30px;

	    border-radius: 10px;

	    box-shadow: 0px 0px 6px 0px #CCC;

	}

	th {

	    text-align: center;

	}
	.ellipsis::after{
		display: none;
	}

</style>
@endsection
@section('content')
<div class="col-sm-12">

<h4>View SMS Detail</h4>



	<div class="card">
		
		<div class="card-block" id="orders_card">

			<div class="row">

				<div class="col-md-12">

					<?php if(isset($_GET['message'])){ ?>

	                    <div class="alert alert-info" role="alert">

	                         <?php echo $_GET['message']; ?>

	                    </div>

                  	<?php } ?>

					<div id="wizard" class="viewSmsWizard">

						<section>

							<div class="table-responsive log-table">

			                    <table class="table table-hover log-table" id="smsContactTable">

			                        <thead>

			                            <tr>

			                                <th>#</th>

			                                <th>To</th>

			                                <th>Status</th>

			                                <th>Created At</th>

			                                <th>Action</th>

			                                <th>Action</th>

			                            </tr>

			                        </thead>

			                        <tbody>

			                         

			                        <?php if($smsSentDetCount > 0){

			                            $serial = 1;

			                            foreach($smsSentDet as $rowLists)

			                            {

			                         ?>

			                            <tr>



			                                <td><?php echo $serial; ?></td>

			                                <td><?php echo $rowLists->sent_to; ?></td>

			                                <?php if($rowLists->accepted){ ?>

			                                <td><span class="green-text">Sent</span></td>

			                            <?php }else { ?>

			                                <td><span class="red-text">Not Sent</span></td>

			                                <?php } ?>

			                                <td><?php echo $rowLists->created_at; ?></td>

			                                <?php $valll =  $rowLists->id; ?>

			                                <td><a type="button" onclick="getsmsDetail('<?php echo $valll;?>')" href="#modal_show_sms_detail" data-toggle="modal" class="btn btn-info btn-blue">View SMS</a></td>

			                                <td><a type="button" href="#" class="btn btn-danger" onclick="deleteContact(<?php echo $valll; ?>)">Delete Contact</a></td>

			                                 

			                            </tr>

			                            <?php $serial = $serial + 1;}} ?>

			                             

			                        </tbody>

			                        <tfoot>

			                            <tr>

			                                <th>#</th>

			                                <th>To</th>

			                                <th>Status</th>

			                                <th>Created At</th>

			                                <th>Action</th>

			                                <th>Action</th>

			                            </tr>

			                        </tfoot>

			                    </table>

			                </div>

						</section>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>





<!-- Modal show sms detail -->

 <div class="modal fade" id="modal_show_sms_detail" tabindex="-1" role="dialog" aria-labelledby="add_contact_individual_title" aria-hidden="true">

      <div class="modal-dialog modal-dialog-centered modal-md" role="document">

        <div class="modal-content">

          <div class="modal-header modal_show_sms_detail_header">

            <h4 class="modal-title" id="add_contact_individual_title">SMS Detail</h4>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>

          <div class="modal-body">

            <div class="add_individual_modal">

                  <h5 class="title-composeSms">SMS Sent</h5>

                  <hr>

                    <div class="row">

                        <div class="col-lg-12">

                            <!-- <p id="sms_date"></p> -->

                            <div class="form-group">

                                   <label class="text-info">Date:</label>

                                   <input class="form-control" id="sms_date" type="text"  disabled />

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-lg-12">

                            <!-- <p id="sms_date"></p> -->

                            <div class="form-group">

                                   <label class="text-info">To:</label>

                                   <input class="form-control" id="sms_to" type="text"  disabled />

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-lg-12">

                            <!-- <p id="sms_date"></p> -->

                            <div class="form-group">

                                   <label class="text-info">Status:</label>

                                   <input class="form-control" id="sms_status" type="text"  disabled />

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-lg-12">

                            <!-- <p id="sms_date"></p> -->

                            <div class="form-group">

                                   <label class="text-info">Message Text:</label>

                                   <textarea maxlength="200" class="form-control" id="sms_text" type="text"  disabled ></textarea>

                            </div>

                        </div>

                    </div>

            </div>  

        </div>

    </div>

</div>

    </div>
    <!-- modal delete -->
    <div class="modal fade" id="deleteModal" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
      <form action="deleteContact" method="post">
        {{csrf_field()}}
        <input type="hidden" id="delId" name="delId">
        <div class="modal-body">
 
          <label>Are you Sure you want to delete contact?</label>
         <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
         <button type="submit" class="btn btn-danger">Delete</button>
         
        </div>
      </form>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
    <!-- end -->
@endsection
@push('scripts')
<script type="text/javascript">
function getsmsDetail(id) {
  $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
            $.ajax({
               url: '/getSmsSentDet',
               type: 'POST',
               data: {
                  id: id
               },
               success: function(data) {
                  var queryData = JSON.parse(data);
                 // console.log(queryData);
                  //console.log(queryData['sent_to']);
                  $('#sms_date').val(queryData['created_at']);
                  $('#sms_to').val(queryData['sent_to']);
                  if(queryData['accepted']){
                     $('#sms_status').addClass('green-text');
                     $('#sms_status').val("Sent");
                  }else{
                     $('#sms_status').addClass('red-text');
                     $('#sms_status').val("Not Sent");
                  }
                  $('#sms_text').val(queryData['sent_sms']);
                   
               },
               error: function() {
                   alert("Something Went Wrong...Try Again.");
               },
               
            });
         
         }
         function deleteContact($id)
         {
            //alert($id);
            $('#delId').val($id);
            $('#deleteModal').modal('show');
         }
          //actions/act_removeSmsText.php?tid= 

         
    $(document).ready( function () {

        $('#smsContactTable').DataTable();



        var breadcrumb = $('.breadcrumb');

        breadcrumb.append('<li class="breadcrumb-item"><a href="smscampaign">SMS Campaign</a></li><li class="breadcrumb-item"><b><span>View SMS Detail</span></b></li>');
        $('.main-menu ul').find('.active').removeClass('active');
        $('.main-menu').find('a[href="/smscampaign"]').parents().addClass('active');
    } );

</script>
@endpush



