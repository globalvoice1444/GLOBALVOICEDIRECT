@extends('layouts.user.user_main')
@section('needs_ckeditor', '1')
@section('css')
<style type="text/css">
	img.imageprevuri {
	    margin: 10px 10px 0px 0px;
	}
	a.btn.btn-danger.waves-effect {
	    width: 100%;
	}
	li.nav-item.mail-section.createaccount {
	    margin-top: 30px;
	}
	.fax_card{
		background-color: #4fc3f7;
	}
	.card .fax_form_header {
	    background-color: #3092bf;
	}
	.create_acc_btn{
		background-color: #3092bf;
	}
	.form-control {
	    height: 40px;
	    border: none;
	}
	input.btn.btn-info.btn-xs.waves-effect.waves-light {
	    margin-top: 19px;
	}
	/*#video_section{
		background-image: url('images/monitor.png');
	    background-repeat: no-repeat;
	    background-position: center;
	    background-size: 100%;
	}
	#video_section iframe {
	    width: 100%;
	    padding: 12px 15px;
	    margin-top: 9px;
	    height: 330px;
	}*/
	div.desktop-wrapper {
	    position: relative;
	    padding-top: 25px;
	    padding-bottom: 67.5%;
	    height: 0;
	}
	.desktop-wrapper iframe {
	    box-sizing: border-box;
	    background: url(http://img01.deviantart.net/05b6/i/2011/030/8/5/apple_led_cinema_screen_by_fisshy94-d38e3o5.png) center center no-repeat;
	    background-size: contain;
	    padding: 3.4% 9.5% 18.6% 10.2%;
	    position: absolute;
	    top: 0;
	    left: 0;
	    width: 100%;
	    height: 100%;
	}
	a.btn-link.fax-video-totutial {
	     
	    font-size: 16px;
	    color: #4fc3f7;
	    cursor: pointer;
	}
	.coversampleclick {
		font-size: 14px !important;
    font-weight: 800 !important;
    color: #00bcd4 !important;
	}
	.fax-video {
	    text-align: center;
	    padding: 0px 0px 22px;
	}
	@media (max-device-width: 1024px){
		.desktop-wrapper iframe{
			padding: 3.4% 6.8% 19% 7.3%;
		}
	}
	#my_camera{
		width: 320px;
		height: 240px;
		border: 1px solid black;
	}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
@section('content')

    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <h5>Fax</h5>
            </div>

			<div class="card-block email-card">
				<?php if(isset($_GET['message'])){ ?>
					<div class="alert alert-info">
					  <strong>Info!</strong> <?php echo $_GET['message'] ?>
					</div>
				<?php } ?>
				<?php if(session()->get('faxdocumentsMessage') != NULL){ ?>
					<div class="alert alert-info">
					  <strong>Info!</strong> <?php echo session()->get('faxdocumentsMessage'); ?>
					</div>
				<?php session()->forget('faxdocumentsMessage'); } ?>
				<div class="fax-video">
					<!--<a class="btn-link fax-video-totutial" data-toggle="modal" data-target="#videomodal">Click Here to see How To Send Faxes</a>-->
				</div>
				<div class="row">
					<div class="col-lg-12 col-xl-3">
						<div class="user-body">
							<div id="wizard">
							    <section>
							        <div class="container">
										
										<ul class="page-list nav nav-tabs flex-column">
											@if(session()->get('vfaxuserDetCount') > 0)
											
												<li class="nav-item p-20 text-center">
													<a  data-toggle="pill" href="#compose_fax" class="btn btn-danger waves-effect" onclick="offTheCamera()">Quick-fax</a>
									 			</li>
												
												<li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#office_style_editor" onclick="offTheCamera()">
														<i class="fa fa-file-text"></i> Office-style online editor
													</a>
												</li>
												<?php if(!isset($_SESSION['coverpage'])){ ?>
													<li class="nav-item mail-section">
														<a class="nav-link waves-effect d-block" data-toggle="pill" href="#coverpagecreate" onclick="offTheCamera()">
															<i class="fa fa-building"></i> Cover page create
														</a>
													</li>
												<?php } ?>
												<li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#upload_own_file" onclick="offTheCamera()">
														<i class="fa fa-cloud-upload"></i> Upload your own file
													</a>
												</li>
												<li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#camera_upload" onclick="uploadCamerActive()">
														<i class="fa fa-cloud-upload"> </i>Camera Upload 
													</a>
												</li>
												<?php if(session()->get('faxdocuments') != NULL and sizeof(session()->get('faxdocuments')) > 0) {?>
												
													<li class="nav-item mail-section">
														<a class="nav-link waves-effect d-block active show" data-toggle="pill" href="#send_doc_fax" onclick="offTheCamera()">
															<i class="fa fa-paper-plane"></i> Send Fax
														</a>
													</li>
												<?php } ?>
												<!--<li class="nav-item mail-section">-->
												<!--	<?php if(session()->get('faxdocuments') != NULL and sizeof(session()->get('faxdocuments')) > 0) {?>-->
												<!--		<a class="nav-link waves-effect d-block" data-toggle="pill" href="#inbox_fax" onclick="offTheCamera()">-->
												<!--	<?php }else{ ?>-->
												<!--		<a class="nav-link waves-effect d-block active show" data-toggle="pill" href="#inbox_fax" onclick="offTheCamera()">-->
												<!--	<?php } ?>-->
												<!--		<i class="fas fa-inbox"></i> Inbox-->
														 
												<!--	</a>-->
												<!--</li>-->
												<!-- <li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#draft_fax">
														<i class="fas fa-save"></i> Drafts
													</a>
												</li> -->
												<li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#sent_fax" onclick="offTheCamera()">
														<i class="fas fa-paper-plane"></i> Sent
													</a>
												</li>
												<!-- <li class="nav-item mail-section">
													<a class="nav-link waves-effect d-block" data-toggle="pill" href="#trash_fax">
														<i class="fas fa-trash"></i> Trash
														<span class="label label-info float-right">30</span>
													</a>
												</li> -->
											<?php ?>
											@else
											@endif
										</ul>
									</div>
								</section>
							</div>
						</div>
					</div>

						<?php $vfaxuserDetCount=session()->get('vfaxuserDetCount');?>
						<?php if($vfaxuserDetCount > 0){ ?>
						<div class="tab-content" id="fax_list">
							<?php if(session()->get('faxdocuments') != NULL and sizeof(session()->get('faxdocuments')) > 0){ ?>
								<div  class="tab-pane fade" id="inbox_fax">
							<?php }else{ ?>
								<div  class="tab-pane fade in active show" id="inbox_fax">
							<?php } ?>
								<div class="table-responsive">
									<table class="table" id="inbox_table">
										<thead>
											<tr>
												<th>Fax ID</th>
												<th>Date</th>
												<th>Source</th>
												<th>Destination</th>
												<th>Status</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(sizeof($incomingfaxes) > 0 and isset($incomingfaxes['data'])){ 
												foreach ($incomingfaxes['data'] as $value) {  
											?>	
											<?php if($value["status"] != "deleted") { ?>
												<tr>
													<td><?php echo $value["faxid"]; ?></td>
													<td><?php echo $value["date"]; ?></td>
									      			<td><?php echo $value["source"]; ?></td>
									      			<td><?php echo $value["destination"]; ?></td>
									      			<td><?php echo $value["status"]; ?></td>
													<td>
														<a target='_blank' href='/viewfaxattachment?faxid=<?php echo $value["faxid"]; ?>'><button type="button" class="btn btn-success btn-xs waves-effect waves-light">
														    <i class="fa fa-file-pdf fa-w-12 fa-lg" aria-hidden="true"></i>
														</button></a>
														<a href='/deleteselectedfax?faxid=<?php echo $value["faxid"]; ?>' onclick="return confirm('Are you sure, you want to remove?')" /><button type="button" class="btn btn-danger btn-xs waves-effect waves-light"><i class="fas fa-trash"></i>
														</button></a>
													</td>
												</tr>
											<?php } ?>
											<?php } } ?>
										</tbody>
									</table>
								</div>
							</div>
							 
							<div  class="tab-pane fade" id="sent_fax">
								<div class="table-responsive">
									<table class="table" id="sent_table">
										<thead>
											<tr>
												<th>Job id</th>
										        <th>Date</th>
										        <th>Source</th>
										        <th>Destination</th>
										        <th>Status</th>
										        <th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if(sizeof($response) > 0 and $response['data']!=null){ 
									      		foreach ($response['data'] as $value) {  
									      	?>	
											<tr>
												<td><?php echo $value["jobid"]; ?></td>
								      			<td><?php echo $value["date"]; ?></td>
								      			<td><?php echo $value["source"]; ?></td>
								      			<td><?php echo $value["destination"]; ?></td>
								      			<td><?php if($value["status"] == "Success"){ echo "Successfully Sent";}else{ echo $value["status"]; } ?></td>
								      			<td>
													<a target='_blank' href='/viewfaxattachment?jobid=<?php echo $value["jobid"]; ?>'><button type="button" class="btn btn-success btn-xs waves-effect waves-light">
													    <i class="fa fa-file-pdf fa-w-12 fa-lg" aria-hidden="true"></i>
													</button></a>
												</td>
												<!-- <td>
													<button type="button" class="btn btn-success btn-xs waves-effect waves-light">
													    <i class="fa fa-file-pdf fa-w-12 fa-lg"></i>
													</button>
													<button type="button" class="btn btn-danger btn-xs waves-effect waves-light">
														<i class="fas fa-trash"></i>
													</button>
												</td> -->
											</tr>
											<?php } } ?>
										</tbody>
										
									</table>
								</div>
							</div>
							 
							<div  class="tab-pane fade" id="compose_fax">
								<form method="post" action="/uploadquickfax" enctype="multipart/form-data" name="miniFax">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group form-primary">
													<input class="form-control fill" name="fromFax" id="fromFax" value="<?php echo $vfaxuserData->accemails; ?>" type="text" />
													<span class="form-bar"></span>
													<label class="float-label">From</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group form-primary">
													<input class="form-control" name="toFax" id="toFax" maxlength="11" minlength="10" value="" type="text" required />
													<span class="form-bar"></span>
													<label class="float-label">To</label>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group form-primary">
										<input class="form-control" name="faxReceiver" id="faxReceiver" value="" type="text" required />
										<span class="form-bar"></span>
										<label class="float-label">Receiver Name</label>
									</div>
									<div class="form-group form-primary">
										<input class="form-control" name="file" id="file" value="" type="file" multiple required />
									</div>
									<div class="mt-20 form-group form-primary">
										<button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
											<i class="fa fa-paper-plane"></i> Send Fax
										</button>
									</div>
								</form>
							</div>
							<div  class="tab-pane fade" id="office_style_editor">
								<form method="post" action="/uploadvfacofficetypeeditor" enctype="multipart/form-data" name="miniFax" >
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="form-group form-primary">
										<input class="form-control" name="documentname" id="documentname" value="" type="text" required />
										<span class="form-bar"></span>
										<label class="float-label">Document Name</label>
									</div>
									<div class="form-group form-primary">
										<!-- <input class="form-control" name="file" id="file" value="" type="file" required /> -->
										<textarea name="editor1" id="editor1"  rows="10" cols="80" placeholder="Add Your Content" required></textarea>
									</div>
									<div class="mt-20 form-group form-primary">
										<button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
											<i class="fa fa-paper-plane"></i> Upload Document
										</button>
									</div>
								</form>
							</div>
							<div  class="tab-pane fade" id="coverpagecreate">
								<form method="post" action="/coverpagecreatefax" enctype="multipart/form-data" name="miniFax" >
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group form-primary">
													<input class="form-control fill" name="fromFax" id="fromFax" value="<?php echo $vfaxuserData->accemails ?>" type="text" readonly/>
													<span class="form-bar"></span>
													<label class="float-label">Fax Source Number</label>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group form-primary">
													<input class="form-control fill" name="fromFaxname" id="fromFaxname" value="" type="text" />
													<span class="form-bar"></span>
													<label class="float-label">Fax From</label>
												</div>
											</div>
											
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group form-primary">
												<input class="form-control" name="toFax" id="toFax" maxlength="11" minlength="10" value="" type="text" required />
												<span class="form-bar"></span>
												<label class="float-label">Number Faxing To</label>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group form-primary">
												<input class="form-control" name="faxReceiver" id="faxReceiver" value="" type="text" required />
												<span class="form-bar"></span>
												<label class="float-label">Name Fax To</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group form-primary">
												<input class="form-control" name="coverpagesubject" id="coverpagesubject" value="" type="text" required />
												<span class="form-bar"></span>
												<label class="float-label">Cover Page Subject</label>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group form-primary">
												<input class="form-control" name="pcontactnumber" id="pcontactnumber" value="" type="text" required />
												<span class="form-bar"></span>
												<label class="float-label">Persons Contact Number</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group form-primary">
												<input class="form-control" name="date" id="date" value="<?php echo date('m-d-Y'); ?>" type="text" required />
												<span class="form-bar"></span>
												<label class="float-label">Date</label>
											</div>
										</div>
									</div>
									<div class="form-group form-primary">
										<textarea class="form-control" rows="4" name="comment" id="comment" required></textarea>
										<span class="form-bar"></span>
										<label class="float-label">Comments</label>
									</div>
									<div class="mt-20 form-group form-primary">
										<button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
											<i class="fa fa-paper-plane"></i> Upload Document
										</button>
									</div>
									<div class="row">
										<span>To view a sample of what this page will look like, <a class="coversampleclick" href="/samplecover" target="_blank"> click here. </a> </span>
									</div>
								</form>
							</div>
							<div  class="tab-pane fade" id="upload_own_file">
								<form method="post" action="/uploadownfilevfax" enctype="multipart/form-data" name="miniFax">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="form-group form-primary">
										<input class="form-control" name="file" id="file" value="" type="file" required />
									</div>
									<div class="mt-20 form-group form-primary">
										<button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
											<i class="fa fa-paper-plane"></i>  Upload Document
										</button>
									</div>
								</form>
							</div>
							<div  class="tab-pane fade" id="camera_upload">
								<form method="post" enctype="multipart/form-data" name="miniFax" id="miniFax">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<input type="hidden" name="image" class="uploadSnap">
									<div id="my_camera"></div>
									<input class="btn btn-info btn-xs waves-effect waves-light" type=button value="Take Snapshot" onClick="take_snapshot()">
									<input class="btn btn-info btn-xs waves-effect waves-light" type=button value="Clear Snapshots" onClick="clear_snapshot()">
									<div id="results" >
										
									</div>
									<div class="mt-20 form-group form-primary">
										<button type="submit" id="uploadOwnImageDoc" class="btn btn-info btn-xs waves-effect waves-light">
											<i class="fa fa-paper-plane"></i>  Upload Document
										</button>
									</div>
								</form>
							</div>

							<?php if(session()->get('faxdocuments') != NULL and sizeof(session()->get('faxdocuments')) > 0){ ?>
								<div  class="tab-pane fade in active show" id="send_doc_fax">
									<form method="post" id="sendalldocfax" enctype="multipart/form-data" name="miniFax">
										<div class="form-group">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group form-primary">
														<input class="form-control fill" name="fromFax" id="fromFax" value="<?php echo $vfaxuserData->accemails; ?>" type="text" />
														<span class="form-bar"></span>
														<label class="float-label">From</label>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group form-primary">
														<input class="form-control" name="toFax" id="toFax" maxlength="11" minlength="10" value="" type="text" required />
														<span class="form-bar"></span>
														<label class="float-label">To</label>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group form-primary">
											<input class="form-control" name="faxReceiver" id="faxReceiver" value="" type="text" required />
											<span class="form-bar"></span>
											<label class="float-label">Receiver Name</label>
										</div>
										<div class="form-group form-primary">
											<label class="float-label">Documents Name</label>
											<table class="table attrTable">

												<?php foreach (session()->get('faxdocuments') as $value) {
												?>
												    <tr>
												        <td class="docname"><?php echo $value['docname']; ?></td>
												        <td class="docsrc" style="display: none"><?php echo $value['src']; ?></td>
												        <td class="docfile" style="display: none"><?php echo $value['filename']; ?></td>
												        <td class="docpath" style="display: none"><?php echo $value['docpath']; ?></td>
												        
												        <td>
												            <a href="javascript:;" class="up"><i style="font-size: 18px;" class="fa fa-level-up"></i>Up</a>
												            <a href="javascript:;" class="down"><i style="font-size: 18px;" class="fa fa-level-down"></i>Down</a>
												        </td>
												        <td><a href="/removefaxdocument?name=<?php echo $value['filename'];?>&type=<?php echo $value['src'];?>" onclick="return confirm('Are you sure, you want to remove?')"><i style="font-size: 18px;" class="fa fa-times"></i>Cancel</a></td>
												    </tr>
												<?php } ?>
											</table>
											<!-- <input class="form-control" name="file" id="file" value="" type="file" multiple required /> -->
										</div>
										<div class="mt-20 form-group form-primary">
											<button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
												<i class="fa fa-paper-plane"></i> Send Fax
											</button>
											<a href="/removeallfaxdocument" onclick="return confirm('Are you sure, you want to remove all?')" class="btn btn-info btn-xs waves-effect waves-light">Cancel Fax</a>
										</div>
									</form>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<?php if($vfaxuserDetCount <= 0){ ?>
					<div class="row">
						<!--<div class="col-md-6 col-sm-12">-->
						<!--	<div class="container">-->
						<!--	    <div class="desktop-wrapper">-->
						<!--	        <iframe width="560" height="315" src="//www.youtube.com/embed/f890SC1schE" frameborder="0" allowfullscreen></iframe>-->
						<!--	    </div>-->
						<!--	</div>-->
						<!--</div>-->
						<div class="col-md-6 col-sm-12">
							 <div class="card text-white">
                                <div class="card-header header-primary fax_form_header">New Fax Account</div>
                                	<div class="card-body fax_card">
										<form method="post" action="/act_newfaxacc" >
											{{csrf_field()}}
											<div class="form-group">
												<div class="form-group form-primary">
													<label class="float-label">Select Area code</label>
													<div class="input-group">
                                                      <input type="text" id="localareacode" class="form-control" placeholder="561 or 855" aria-label="Search">
                                                      <button class="btn btn-primary statedet" type="button">Search</button>
                                                    </div>
													<span class="form-bar"></span>
												</div>
												<div class="form-group form-primary">
													<label class="float-label">Select Fax Number</label>
													<select class="form-control" name="did" id="did">
														<option value="-1">select Fax Number</option>
													</select>
													<span class="form-bar"></span>
													 <label class="float-label">To</label> 
												</div>
											</div>
											<div class="form-group form-primary">
												<label class="float-label">Name</label>
												<input type="text" name="accountName" id="accountName" class="form-control" value=""  required />
												
											</div>
											<div class="form-group form-primary">
												<label class="float-label">Email</label>
												<input type="email" name="accountEmail" id="accountEmail" class="form-control" value="" required />
											</div>
											<div class="mt-20 form-group form-primary">
												<button type="submit" class="btn btn-info btn-block btn-xs waves-effect waves-light create_acc_btn">
													<i class="fa fa-paper-plane"></i> Create Account
												</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
		<!-- Modal -->
  <div class="modal fade" id="videomodal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
	        <iframe width="420" height="315"
				src="https://www.youtube.com/embed/tgbNymZ7vqY">
			</iframe>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
				
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script language="JavaScript">
Webcam.set({
	width: 320,
	height: 240,
	image_format: 'jpeg',
	jpeg_quality: 90
});
function uploadCamerActive(){
	Webcam.attach( '#my_camera' );
}
function offTheCamera(){
	Webcam.reset('#my_camera');
}
//Webcam.attach( '#my_camera' );

function take_snapshot() {
	
	// take snapshot and get image data
	Webcam.snap( function(data_uri) {
	// display results in page
		$('#results').append('<img class="imageprevuri" src="'+data_uri+'"/>');
		// document.getElementById('results').innerHTML = '<img id="imageprev" class="imageprevuri" src="'+data_uri+'"/>';
		$('.uploadSnap').val(data_uri);
	} );
}

function clear_snapshot(){
	$('#results').empty();
}

function saveSnap(){
	 $(".photo").each(function() {  
	   imgsrc = this.src;
	   console.log(imgsrc);
	  });
 // Get base64 value from <img id='imageprev'> source
 //var base64image = document.getElementById("imageprev").src;

 return false;

 console.log(base64image);

 Webcam.upload( base64image, 'uploadownfilevfaxCam', function(code, text) {
  console.log('Save successfully');
  //console.log(text);
 });

}
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#miniFax').submit(function(){
			var uriArray = [];
			$(".imageprevuri").each(function() {  
			   imgsrc = this.src;
			   uriArray.push(imgsrc);
			   console.log(imgsrc);
			 });
			$.ajax({
				url: "/uploadImageUri",
        		method: "POST",
        		data: {'_token':'{{ csrf_token() }}','imageData': JSON.stringify(uriArray)},
        		success: function(data){
        			window.location.replace("/user/fax_mod");
        		},
        		error: function(){
        			console.log("Something Went Wrong. Please try again. Thansk!");
        		}
			});
		 	return false;
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#sendalldocfax').submit(function(){
			var ary = [];
	        $(function () {
	            $('.attrTable tr').each(function (a, b) {
	                var docnam = $('.docname', b).text();
	                var docsrc = $('.docsrc', b).text();
	                var docfile = $('.docfile', b).text();
	                var path = $('.docpath', b).text();
	                ary.push({ docname: docnam, docpath: path, filename: docfile, src:docsrc });
	               
	            });
	             //alert(JSON.stringify( ary));
	            // console.log(JSON.stringify( ary));
	            var formData = $( '#sendalldocfax' ).serializeArray();
	        	// console.log(formData);
	        	console.log(ary);
	        	console.log(formData);
	        	$.ajax({
	        		url: "/uploadvfaxall",
	        		method: "POST",
	        		data: {'_token':'{{ csrf_token() }}','formdata': JSON.stringify(formData), 'tabledata': JSON.stringify( ary)},
	        		success: function(data){
	        			window.location.replace("/user/fax_mod?message="+data);
	        		}

	        	});
	        });
	        return false;
		});
	});
</script>
<script type="text/javascript">
	var breadcrumb = $('.breadcrumb');
        breadcrumb.append('<li class="breadcrumb-item"><b><span>Fax</span></b></li>');
        $('.main-menu ul').find('.active').removeClass('active');
        $('.main-menu').find('a[href="/user/fax_mod"]').parents().addClass('active').addClass('pcoded-trigger');
	$(document).ready( function () {
	    $('#inbox_table').DataTable({
	    	"order": [[ 1, "desc" ]]
	    });
	    $('#draft_table').DataTable();
	    $('#sent_table').DataTable({
	    	"order": [[ 1, "desc" ]]
	    });
	    $('#trash_table').DataTable();

	} );
</script>
<script type="text/javascript">
	$(document).ready(function(){
		var sentfaxstatus = $('#sentFaxTable').DataTable();
		sentfaxstatus.order( [ 1, 'desc' ] ).draw();
		var incomingfaxtable = $('#incomingListTable1').DataTable();
		incomingfaxtable.order( [ 1, 'desc' ] ).draw();
		$( ".statedet" ).click(function() {
			$.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
		   $.ajax({
                url: '/act_getDids',
                type: "POST",
                data: { 'state' : $('#localareacode').val() },
                success: function(data)
                {
                    var response = JSON.parse(data);
                    console.log(response);
                    if(response.success == 1){
                          $didoptions = "<option value='-1'>select did</option>";
                          $.each(response.data , function( key, value ) {
                             $didoptions += "<option value='"+value+"'>"+value+"</option>";
                          });
                          $('#did').html($didoptions);
                    }else{
                       
                    }
                },
                error: function () {

                }
            });
		});
	});
</script>
<script type="text/javascript">
  CKEDITOR.replace( 'editor1' );
</script>
<script type="text/javascript">
	$(document).ready(function(){
	    $(".up,.down").click(function(){
	        var row = $(this).parents("tr:first");
	        if ($(this).is(".up")) {
	            row.insertBefore(row.prev());
	        } else {
	            row.insertAfter(row.next());
	        }
	    });
	});
</script>
@endpush


