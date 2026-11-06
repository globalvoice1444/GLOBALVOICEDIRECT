@extends('layouts.user.user_main')
@section('css') 
    
 @endsection
 @section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
 <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="https://dev.globalvoicedirect.com/users/dist/grapesjs-blocks-basic.min.js"></script>
    <style>
      body, html{ height: 100%; margin: 0; font-size: 14px;}

        .gjs-block-svg {
           width: 61%;
        }

		.gjs-block-svg-path {
			fill: white;
		}
		#gjs {
			height: 100vh !important;
		}
		.campaigngroupNames .row {
			margin-bottom: 20px;
		}
		button.btn.btn-info.btn-blue, a.btn.btn-info {
			background: #444444;
		}
		.gjs-pn-options {
		    right: 25%;
		    top: 0px;
		}
		.gjs-pn-views {
		    width: 20% !important;
		}
		.gjs-pn-views-container {
		    width: 20%;
		}
		.gjs-cv-canvas {
		    width: 80% !important;  
		}
		.email-container {
		    background: #fff;
		    border: 1px solid #bcbbbb;
		}
		.top-wrapper {
		    padding: 15px;
		}
		.col-lg-3.space-right {
		    margin: 15px 0px 0 0px;
		    font-size: 18px;
		    color: #4fc3f7;
		}
		select#campaignName, input#subject {
		    color: #4fc3f7;
		    border: 1px solid #4fc3f7;
		    margin-top: 12px;
		}
		button.btn.btn-info.btn-blue, a.btn.btn-info {
		    background: #4fc3f7;
		    margin-top: 15px;
		    padding: 9px 20px 9px 20px;
		    border-radius: 6px;
		}
		select#tempName {
		    color: #4fc3f7;
		    border: 1px solid #4fc3f7;
		    margin-top: 20px;
		}
		.col-lg-4.first-button {
		    max-width: 28% !important;
		}
		/*.loader {
		  border: 16px solid #f3f3f3;
		  border-radius: 50%;
		  border-top: 16px solid blue;
		  border-bottom: 16px solid blue;
		  width: 120px;
		  height: 120px;
		  -webkit-animation: spin 2s linear infinite;
		  animation: spin 2s linear infinite;
		}

		@-webkit-keyframes spin {
		  0% { -webkit-transform: rotate(0deg); }
		  100% { -webkit-transform: rotate(360deg); }
		}

		@keyframes spin {
		  0% { transform: rotate(0deg); }
		  100% { transform: rotate(360deg); }
		}*/

    </style>
    <div class="store-banr">
		<h4>Email Campaign</h4>
	</div>
    <div class="email-container">
    	<div class="top-wrapper">
		    
		  	<div class="container">
		  		<?php if(isset($_GET['message'])){ ?>
					<div class="alert alert-info" role="alert">
					     <?php echo $_GET['message']; ?>
					</div>
				<?php } ?>
		  		 
		  		<div class="campaigngroupNames">
		  			<!-- <form action="/emailCampaign/action/sendEmails.php" method="post" name="emailSubmit" id="emailSubmit"> -->
		  				<div class="row col-md-12">
							<div class="col-lg-3 space-right">
								<label>Select Campaign Name</label>
							</div>
							<div class="col-lg-6">
								<select name="campaignName" id="campaignName" class="form-control">
								 	<?php if($smsGroupDetailCount > 0){ 
								      foreach($smsGroupDetail as $rowLists)	
											{ ?>
								 	<option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>
								 	 <?php }}?>
								</select>
							</div>
							<!-- <div class="col-lg-3">
								<button onclick="submitFormData(this)" class="btn btn-info btn-blue">Send Message</button>
							</div> -->
						</div>
						<div class="row col-md-12">
							<div class="col-lg-3 space-right">
								<label>Email Subject</label>
							</div>
							<div class="col-lg-6">
								<input type="text" name="subject" id="subject" value="" class="form-control">
							</div>
						</div>
		  				<div class="row col-md-12">
							<div class="col-lg-3 space-right">
								<label>Select Template Name(Optional)</label>
							</div>
							<div class="col-lg-6">
								<select name="tempName" id="tempName" class="form-control">
									<option value="-1">select template name</option>
								 	<?php if($emailDetailCount > 0){
								 	 foreach($emailDetail as $rowLists) 
											{ ?>
								 	<option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->email_name; ?></option>
								 	 <?php }}?>
								</select>
							</div>
							<div class="col-lg-3">
								<button onclick="submitFormData(this)" class="btn btn-info btn-blue">Send Message</button>
							</div>
						</div>
						<div class="row col-md-12">
							<div class="col-lg-3">
							</div>
							
							<div class="col-lg-4 first-button">
								<button onclick="saveAndSendEmailTemplate(this)" class="btn btn-info btn-blue">Save Template And Send Email</button>
							</div>
							<div class="col-lg-3">
								<button onclick="saveEmailTemplate(this)" class="btn btn-info btn-blue">Save Template For Later</button>
							</div>
						</div>

		  				                                                     
	  			</div>
	    <div id="gjs" style="height:100vh;">
	    </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">

      var editor = grapesjs.init({
        height: '100%',
        noticeOnUnload: 0,
        storageManager:{autoload: 0},
        container : '#gjs',
        fromElement: true,
        canvas: {styles: ['./styles.css']},
        plugins: ['gjs-blocks-basic'],
        pluginsOpts: {
          'gjs-blocks-basic': {}
        }
      });
      console.log(editor.runCommand('gjs-get-inlined-html'));

   //    editor.setComponents("<h2>Ali Raza</h2>");
	  // editor.setStyle("h2{font-size:50px;}");
	  // editor.store();

      window.editor = editor;

      function submitFormData(e){
      	var formCampaignName = $('#campaignName').val();
      	var sbjct = $('#subject').val();
      	var templateHtml = editor.getHtml();
      	var templateCss = editor.getCss();
      	if(sbjct != ""){
      		$.ajaxSetup({
      			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      		});
      		$.ajax({
	      		url: "/sendEmailTemp", 
	      		method: "post",
	      		data: {grpId:formCampaignName, tmpHtml:templateHtml, tmpCss:templateCss, subject: sbjct},
	      		success: function(result){
	      			window.location.replace("?message="+result);
			    	//window.location = "?message="+result;
				}
			});
      	}else{
      		alert("Please enter the subject.");
      	}
      	  
      	// console.log(editor.runCommand('gjs-get-inlined-html'));
      	// console.log(templateHtml);
      	// console.log(templateCss);
      	return false;
    };

    function saveEmailTemplate(e){
      	var tempName = prompt("Please enter template name.", "");
      	if(tempName != null || tempName != ""){
	      	var templateHtml = editor.getHtml();
	      	var templateCss = editor.getCss();
           //alert();
	      	$.ajax({
	      		url: "actions/saveEmailTemp", 
	      		method: "post",
	      		data: {name:tempName, tmpHtml:templateHtml, tmpCss:templateCss},
	      		success: function(result){
	      			window.location.replace("?message="+result);
			    	//window.location = "?message="+result;
				}
			});
	    }else{
	    	alert("Please enter the Email Template Name.");
	    }
      	// console.log(editor.runCommand('gjs-get-inlined-html'));
      	// console.log(templateHtml);
      	// console.log(templateCss);
      	return false;
    };

    function saveAndSendEmailTemplate(e){
      	var sbjct = $('#subject').val();
      	if(sbjct != ""){
	      	var tempName = prompt("Please enter template name.", "");
	      	var formCampaignName = $('#campaignName').val();
	      	if(tempName != null || tempName != ""){
		      	var templateHtml = editor.getHtml();
		      	var templateCss = editor.getCss();

		      	$.ajaxSetup({
      			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      		});
		      	
		      	$.ajax({
		      		url: "/saveAndSendEmailTemp", 
		      		method: "post",
		      		data: {grpId:formCampaignName, name:tempName, tmpHtml:templateHtml, tmpCss:templateCss, subject: sbjct},
		      		success: function(result){
		      			window.location.replace("?message="+result);
				    	//window.location = "?message="+result;
					}
				});
		    }else{
		    	alert("Please enter the Email Template Name.");
		    }
		}else{
			alert("Please enter the Email Campaign Subject.");
		}
      	// console.log(editor.runCommand('gjs-get-inlined-html'));
      	// console.log(templateHtml);
      	// console.log(templateCss);
      	return false;
    };


    </script>
    <script type="text/javascript">
    	 
		$( "#tempName" ).change(function() {
			if($(this).val() != "-1"){
				  	$.ajaxSetup({
      			headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      		});
		      	
			    $.ajax({
	                url: '/getTemplates',
	                type: "POST",
	                data: { 'id' : $(this).val() },
	                success: function(data)
	                {
	                	if(data != ""){
	                		var response = JSON.parse(data);
	                		editor.setComponents(response['email_content']);
							editor.setStyle(response['css']);
							editor.store();
	                	}
	                },
	                error: function () {

	                }
	            });
			}
		});	

	var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b><a href="/send_email"><span>Send Email</span></a></b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/send_email"]').parents().addClass('active');			 
					
    </script>
</div>
@endsection


