
@extends('layouts.user.user_main')
@section('css')
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="css/smsCampaign.css">
    <style type="text/css">

        .panel-group {

            background: #ffffff;

            width: 100%;

            height: auto;

            border: 2px solid #ddd;

            box-shadow: 1px 3px 14px grey;

            padding: 45px;

        }

        .panel-heading {

            background: #4fc3f7;

            border-radius: 10px;

            padding: 8px;

            margin-bottom: 10px;

        }

        /*******************************

* ACCORDION WITH TOGGLE ICONS

* Does not work properly if "in" is added after "collapse".

*******************************/

    .panel-group .panel {

        border-radius: 0;

        box-shadow: none;

        border-color: #EEEEEE;

    }
  .panel-title {

        font-size: 14px;

    }

 
    .panel-title > a {

        display: block;

        text-decoration: none;

        color: #fff;

        margin-top: 10px;

        padding: 5px;



    }



    .more-less {
        /* float: right; */
        color: #fff;
        margin-right: 10px;
    }

    .panel-body {

        background-color: #dfe4e7;

        padding: 10px;

        margin: 10px 0px;

        border-radius: 6px;

    }
    #form-div, #progressbar {
	    margin: 1em auto;
	    background: #dfe4e7 !important;
	    border: 1px solid #dfe4e7 !important;
	    padding: 1em;
	}
    #cbform input, #file {
	    padding: 1px 2px;
	    margin-bottom: 15px;
	    padding-top: 10px;
	    padding-bottom: 10px;
	    border-style: none;
	    border-radius: 6px;
	}
	#cbform label {
	    float: left;
	    clear: both;
	    text-align: right;
	    margin-bottom: 10px;
	    font-size: 16px;
	    color: #134576 !important;
	    font-weight: 700;
	}
	button.multiselect.dropdown-toggle.btn.btn-default {
	    width: 703px;
	    background: white;
	    padding-top: 10px !important;
	    padding-bottom: 10px !important;
	    border-style: none;
	    border-radius: 6px;
	    margin-bottom: 15px !important;
	}
	
    #cbform textarea {
	    padding: 1px 2px;
	    border-style: none;
	    border-radius: 6px;
	    margin-bottom: 10px;
	}
	#cbform input#submitButton {
	    padding: 10px 24px !important;
	    margin-bottom: 15px;
	    background-color: #134576 !important;
	    border-radius: 6px;
	    border-style: none;
	    text-transform: uppercase;
	}
	#cbform input#submitButton:hover {
	    padding: 10px 24px !important;
	    margin-bottom: 15px;
	    background-color: #b0cb02 !important;
	    border-radius: 6px;
	    border-style: none;
	    text-transform: uppercase;
	}
	#progressbar label {
	    float: left;
	    width: 100%;
	    text-align: left;
	    clear: both;
	    margin-bottom: 10px;
	    padding-left: 87px;
	    font-size: 16px;
	    color: #134576 !important;
	    font-weight: 600;
	}
        



    /* ----- v CAN BE DELETED v ----- */

    body {

        background-color: #26a69a;

    }

    .panel-body li {
        list-style: none;
        margin-left: 30px;
        font-size: 18px;
        font-weight: 600;
        color: #4fc3f7;
        margin-bottom: 5px;
    }

    .demo {

        padding-top: 60px;

        padding-bottom: 110px;

    }



    .demo-footer {

        position: fixed;

        bottom: 0;

        width: 100%;

        padding: 15px;

        background-color: #212121;

        text-align: center;

    }



    .demo-footer > a {

        text-decoration: none;

        font-weight: bold;

        font-size: 14px;

        color: #fff;

    }

       

    .send_sms_campaign, .compose_sms_modal,.send_sms_from_csv {

        border-radius: 5px;

        border: 1px solid #dadada;

        padding: 20px;

        padding-bottom: 60px;

        margin-bottom: 10px;

        height: auto;

    }

    .send_sms_individual {

        border-radius: 5px;

        border: 1px solid #dadada;

        padding: 20px;

        margin-bottom: 5px;

        height: 360px;

    }

    .title-composeSms {

        padding: 3px 0px;

        color: #00bcd4;

        margin-bottom: 17px;

        text-align: center;





    }

    .modal-header {

        background-color: #4fc3f7;

        color: #fff;

    }

    .add_individual_modal{

        border-radius: 5px;

        border: 1px solid #dadada;

        padding: 20px;

        margin-bottom: 5px;

        height: 968px;

    }

    .sent_sms_table_modal{

        border-radius: 5px;

        border: 1px solid #dadada;

        padding: 20px;

        margin-bottom: 5px;

        height: auto;

    }

    .add_individual_modal{

        border-radius: 5px;

        border: 1px solid #dadada;

        padding: 20px;

        margin-bottom: 5px;

        height: 400px;

    }

    .add_campaign_modal{

        height: 175px;

    }

    #creditHistoryTable tbody tr td {

        padding: 15px 49px;

    }

    .alert-info a {
        font-weight: 600;
        color: #3f4d67;
    }
    .panel-body a {
        color: #4fc3f7;
        font-size: 15px;
    }
    i.fas.fa-chevron-right {
        margin-right: 10px;
    }
    .format-labels {
        margin-top: 6px;
        color: #00bcd4;
    }
    .format-labels p {
        color: #00bcd4;
    }
    .format-labels a {
        font-weight: 600;
        font-size: 15px;
        color: #27627c;
    }
    @media only screen and (min-width: 320px) and (max-width: 667px){

        .compose_sms_modal,.send_sms_from_csv{

          height: 400px;

        }

        .send_sms_individual{

          height: 380px;

        }

        .seend_sms_campaign{

          height: 360px;

        }

        #creditHistoryTable tbody tr td {

        padding: 13px 20px;

    }

    }
    ul.multiselect-container.dropdown-menu.show {
        width: 360px;
    }

    </style>
@endsection
@section('content')
    <div class="container">

        <h4>Audio Broadcast</h4>

        <?php if(isset( $_GET['message'])){ ?>

            <div class="alert alert-info">

              <strong>Info!</strong> <?php echo $_GET['message']; ?>.

            </div>

        <?php } ?>

            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            
                <div class="panel panel-default">

                    <div class="panel-heading" role="tab" id="headingOne">

                        <h4 class="panel-title">

                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" class="collapsed">

                                <i class="more-less fa fa-minus"></i>

                                Audio Broadast

                            </a>

                        </h4>

                    </div>

                    <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">

                        <div class="panel-body">

                           
                            <center>
                                <h3>Audio Broadcast</h3>
                                <div id="form-div">
                                <form id="cbform" method="post" name="main" action="">
                                    <input name="Action" value="Connect Us Now!" type="hidden">
                                    <div>
                                        <!-- <label for="acct" accesskey="A">Account:</label>
                                        <input id="acct" name="Account" maxlength="15" type="text"><br>
                                    
                                        <label for="pwd" accesskey="P">Password:</label>
                                        <input id="pwd" name="Password" maxlength="15" type="password"><br> -->
                                    
                                        <label for="ivn" class="labelCampaignName text-primary" accesskey="H">IVR number:</label>
                                         
                                        <input id="ivn" class="form-control" name="IVR_Number" maxlength="15" type="text"><br>
                                    
                                        <label for="list" class="labelCampaignName text-primary" accesskey="S">Select Audio Campaign:</label>
                                        <select id="List_of_Campaigns" class="form-control" name="List_of_Campaigns" multiple="multiple">
                                            <?php if($audioGroupDetailCount > 0){ foreach($audioGroupDetail as $rowLists)

                                            { ?>

                                            <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>

                                            <?php }}?>
                                        </select>
                                         <label for="list" class="labelCampaignName text-primary" accesskey="S">Numbers list:</label>
                                        <textarea id="list" class="form-control" name="List_of_Numbers" rows="3" cols="20"></textarea>
                                    </div>
                                    <input id="submitButton" class="btn btn-info float-right" onClick="go();" value="Connect" type="button">
                                </form>
                                <form id="upload" action="">
                                    {{csrf_field()}}
                                    <input type="file" class="form-control" name="file" id="file" onchange="file_upload(this.form,'/check','list'); return false;" >
                                    <div id="upload_status"></div>
                                </form>
                                </div>

                                <div id="progressbar">
                                    <div>
                                    <label for="status" accesskey="M">Status: <span id="status"></span></label><br>
                                    <label for="counter" accesskey="R">Calls initiated: <span id="counter"> 0 </span><span id="total"> out of 0</span></label><br>
                                    <label for="calling" accesskey="C">Now calling: <span id="calling"></span></label><br></div>
                                    <div style="border-radius: 6px;width: 703px;height: 1.5em;margin-top: 43px;background-color: #fff;padding-top: 35px;margin-left: -5px;">
                                        <div id="progress" style="height:0.5em; width:0%; background-color:#135791; float:left;"></div>
                                    </div>
                                </div>
                            </center>

                                <!-- <li><i class="fas fa-chevron-right"></i><a href="#compose_sms" data-toggle="modal">Compose Message</a></li>

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_send_sms_from_csv" data-toggle="modal">From File Upload</a></li> -->

                        </div>

                    </div>

                </div>
                

    </div><!-- panel-group -->

</div><!-- container -->





    </div>
 
</div>
@endsection
@push('scripts')
<script type="text/javascript"><!--

    var counter = 1;
    var i = 0;
    var acc = "";
    var pass = "";
    var ivn = "";
    var numbers = "";
    var host = "";
    var t = "";

    var newscript = function(url) {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
        document.getElementsByTagName('head')[0].removeChild(script);
        clearTimeout(t);
    }
    
    function file_upload(form, action_url, div_id) {
        var iframe = document.createElement("iframe");
        iframe.setAttribute("id", "upload_iframe");
        iframe.setAttribute("name", "upload_iframe");
        iframe.setAttribute("style", "display:none;");
     
        // Add to document...
        form.parentNode.appendChild(iframe);
        window.frames['upload_iframe'].name = "upload_iframe";
     
        iframeId = document.getElementById("upload_iframe");
     
        // Add event...
        var eventHandler = function () {

                if (iframeId.detachEvent) { iframeId.detachEvent("onload", eventHandler); }
                else { iframeId.removeEventListener("load", eventHandler, false); }
     
                // Message from server...
                var content = iframeId.contentDocument.body.innerHTML;
                if (content == '') { var status = '<span style="color:red">Error has occured</span>'; }
                else { var status = '<span style="color:green">Successfully uploaded</span>'; }

                document.getElementById(div_id).innerHTML = content;
                document.getElementById('upload_status').innerHTML = status;
     
                // Del the iframe...
                setTimeout('iframeId.parentNode.removeChild(iframeId)', 250);
            }
     
        if (iframeId.addEventListener) iframeId.addEventListener("load", eventHandler, true);
        if (iframeId.attachEvent) iframeId.attachEvent("onload", eventHandler);
     
        // Set properties of form...
        form.setAttribute("target", "upload_iframe");
        form.setAttribute("action", action_url);
        form.setAttribute("method", "post");
        form.setAttribute("enctype", "multipart/form-data");
        form.setAttribute("encoding", "multipart/form-data");
     
        // Submit the form...
        form.submit();
     
        document.getElementById(div_id).innerHTML = "Uploading...";
    }

    function go() {
        i = 0;
        counter = 1;

        document.getElementById('progress').style.width = 0 + '%';
        document.getElementById('counter').innerHTML = 0;
        document.getElementById('calling').innerHTML = "";
        document.getElementById('total').innerHTML = " out of 0";

        // acc = document.getElementById("acct").value;
        // pass = document.getElementById("pwd").value;
        ivn = document.getElementById("ivn").value;
        if($('#list').val() != ""){
            numbers = document.getElementById("list").value.replace(/ /g,'').split("\n"); // get the values, remove all spaces
            if (document.main.IVR_Number.value) {
             
                if (numbers[0] != "") { // check if there is at least one number in the list
                    document.getElementById('status').innerHTML = "please wait...";
                    check(acc,pass);
                } else {
                    alert("Please specify at least one number to call");
                }
            } else {
                alert("IVR is not specified");
            }
        }else{
            numbers = $('#List_of_Campaigns').val();
            $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
            $.ajax({
                url:'/getaudionumbers',
                method:'post',
                data:{ids:JSON.stringify(numbers)},
                success: function(data){
                    console.log(data);
                    if(data != ""){
                        numbers = JSON.parse(data);
                        if (document.main.IVR_Number.value) {
                 
                            if (numbers[0] != "") { // check if there is at least one number in the list
                                document.getElementById('status').innerHTML = "please wait...";
                                check(acc,pass);
                            } else {
                                alert("Please specify at least one number to call");
                            }
                        } else {
                            alert("IVR is not specified");
                        }
                    }else{
                        alert("Audio Campaign Group is empty.Please add subscribers in the audio campaign group.");
                    }
                    
                }
            });
        }
        console.log(ivn);
        console.log(numbers);
             
    }

    function check(acc,pass) {
         $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
        $.ajax({
            url:'/public/check.php',
            method:'GET',
            success: function(data){
                var result = data.split(",");
                document.getElementById('status').innerHTML = result[1];
                if (result[0] == "ok") {
                    document.getElementById('status').innerHTML = "authorization successful";
                    if (result[1]) {
                        host = result[1];
                        call();
                    }
                }
                else if (result[0] == "blocked") {
                    document.getElementById('status').innerHTML = "the account " + acc + " is blocked";
                }
                else if (result[0] == "credit_exceeded") {
                    document.getElementById('status').innerHTML = "the account " + acc + " is credit exceeded";
                }
                else if (result[0] == "expired") {
                    document.getElementById('status').innerHTML = "the account " + acc + " is expired";
                }
                else {
                    document.getElementById('status').innerHTML = "account ID or password is incorrect";
                }
            }
        });
        //return false;
        // if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
        //     xmlhttp = new XMLHttpRequest();
        // }
        // else { // code for IE5, IE6
        //     xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        // }

        // xmlhttp.onreadystatechange = function() {
        //     if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        //         var result = xmlhttp.responseText.split(",");
        //         document.getElementById('status').innerHTML = result[1];
        //         if (result[0] == "ok") {
        //             document.getElementById('status').innerHTML = "authorization successful";
        //             if (result[1]) {
        //                 host = result[1];
        //                 call();
        //             }
        //         }
        //         else if (result[0] == "blocked") {
        //             document.getElementById('status').innerHTML = "the account " + acc + " is blocked";
        //         }
        //         else if (result[0] == "credit_exceeded") {
        //             document.getElementById('status').innerHTML = "the account " + acc + " is credit exceeded";
        //         }
        //         else if (result[0] == "expired") {
        //             document.getElementById('status').innerHTML = "the account " + acc + " is expired";
        //         }
        //         else {
        //             document.getElementById('status').innerHTML = "account ID or password is incorrect";
        //         }
        //     }
        // }

        // if (xmlhttp == null) {
        //     alert ("Your browser does not support AJAX");
        //     return;
        //     } else {
        //     xmlhttp.open("GET","check.php", true);
        //     xmlhttp.send();
        // }
    }

    function call() {
        document.getElementById('total').innerHTML = " out of " + numbers.length;

        if (i < numbers.length) {
            count = (i+1)/numbers.length;
            a = '/public/call.php?First_Phone_Number='+numbers[i]+'&Second_Phone_Number='+ivn+'&host='+host;
            number = numbers[i];
            $.ajax({
                url:'/public/call.php?First_Phone_Number='+numbers[i]+'&Second_Phone_Number='+ivn+'&host='+host,
                method:'GET',
                success:function(){
                    drawProgress(count, number);
                    t = setTimeout("call()", 1000);
                    i++;
                }
            });
            // xmlhttp.open("GET",a, true);
            // xmlhttp.send();
            
        }
        else {
            alert("Called " + numbers.length + " number(s)");
            window.location.href = "/active_calls?ivrnumber="+ivn;
        }
    }

    function drawProgress(count, number) {
        document.getElementById('progress').style.width = count * 100 + '%';
        document.getElementById('counter').innerHTML = counter++;
        document.getElementById('calling').innerHTML = number;
    }

</script>
<script type="text/javascript">
 
    function toggleIcon(e) {

        $(e.target)

            .prev('.panel-heading')

            .find(".more-less")

            .toggleClass('fa-plus fa-minus');

    }

    $('.panel-group').on('hidden.bs.collapse', toggleIcon);

    $('.panel-group').on('shown.bs.collapse', toggleIcon);

</script>

<script type="text/javascript">

    $(document).ready( function () {
        $('#List_of_Campaigns').change(function(){
            var selected_option = $('#List_of_Campaigns').val();
            if(selected_option == ""){
                $('#list').val("");
                $('#list').removeAttr("readonly");
            }else{
                $('#list').val("");
                $('#list').attr("readonly",'true');
            }
             
        });

        /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');
        var thisLink = '/user/audioCampaign';
        breadcrumb.text('Audio Broadcast');
        breadcrumb.prop("href", thisLink);*/
         var breadcrumb = $('.breadcrumb');
        breadcrumb.append('<li class="breadcrumb-item"><b><span>Audio Broadcast</span></b></li>');
        //$('.main-menu ul').find('.active').removeClass('active');
        $('.main-menu').find('a[href="/user/audioCampaign"]').parents().addClass('active');

        //$('.main-menu ul').find('.active').removeClass('active');
        //$('.main-menu').find('a[href="'+thisLink+'"]').parents().addClass('active');

        $('#ContactsTable').DataTable();

        $('#List_of_Campaigns').multiselect({

            includeSelectAllOption: true

        });


    } );

</script>
@endpush




