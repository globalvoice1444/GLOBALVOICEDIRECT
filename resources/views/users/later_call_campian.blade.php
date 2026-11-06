@extends('layouts.user.user_main')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 
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

            background: #4fc3f7 !important;

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

        border-color: #fff;

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
    #form-div, #progressbar {
    /* width: 24em; */
    margin: 1em auto;
    background: none !important;
    border: none !important;
    padding: 1em;
    color: #000000;
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
    #cbform input, #file {
	    padding: 1px 2px;
	    float: right;
	    margin-bottom: 15px;
	    background: #ffffff;
	    padding-top: 10px;
	    padding-bottom: 10px;
	    border-style:none;
	    border-radius:6px;
	}
	span.fa.fa-calendar {
	    padding-top: 11px !important;
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

    .panel-body {
	    margin: 0px 5px;
	    padding: 50px 70px;
        background-color: #dfe4e7 !important;
	    border-radius: 6px;
    }
    .dataTables_wrapper .dataTables_length select {
	    border-style: none !important;
	    padding: 5px 30px 5px 5px !important;
	    border-radius: 6px !important;
	}
    .dataTables_wrapper .dataTables_filter input {
	    margin-left: 0.5em;
	    border-style: none !important;
	    border-radius: 6px !important;
	    padding: 5px 15px 5px 15px !important;
	}   
    table.dataTable.no-footer {
	    border-style: none !important;
	    background: #fff !important;
	    border-radius: 6px !important;
	    margin-top: 22px !important;
	}
	table#contact_table {
	    border-style: none !important;
	    background: #fff !important;
	    border-radius: 6px !important;
	    margin-top: 22px !important;

	}

    .table>thead>tr>th{
	    padding: 10px 55px;
	    color: #134576;
	    text-transform: uppercase;

	}
	.table>tbody>tr>td{
	 	padding: 10px 55px;
	    color: #000;
	    text-transform: capitalize;
	}
    div.dataTables_wrapper div.dataTables_info {
	    display: inline-block;
	    font-weight: 600 !important;
	}
	.dataTables_wrapper .dataTables_paginate .paginate_button {
	    box-sizing: border-box;
	    display: inline-block;
	    min-width: 1.5em;
	    padding: 0.5em 1em;
	    margin-left: 10px;
	    text-align: center;
	    text-decoration: none !important;
	    cursor: pointer;
	}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
	    color: #333 !important;
	    border: 1px solid #979797;
	    background-color: white;
	    border-radius: 6px;
	    background: linear-gradient(to bottom, #fff 0%, #dcdcdc 100%);
	    box-shadow: none !important;
	}
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
	    cursor: default;
	    color: #fff !important;
	    border: 1px solid transparent;
	    background: #134576;
	    box-shadow: none;
	    border-radius: 6px;
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
    button.multiselect.dropdown-toggle.btn.btn-default {
	    width: 598px;
	    background: white;
	    padding-top: 10px !important;
	    padding-bottom: 10px !important;
	    border-style: none;
	    border-radius: 6px;
	    margin-bottom: 15px !important;
    }
    ul.multiselect-container.dropdown-menu.show {
        width: 360px;
    }
    .title h3{
    font-size: 25px !important;
    margin-top: 20px;
    margin-bottom: 10px;
    line-height: 1.4em !important;
    font-weight: 300;
}

/* inputs */

/*.container .bmd-form-group .bmd-label-static {
    top: .35rem;
    left: 0;
    font-size: .875rem;
}*/

/*.container .bmd-form-group .form-control, .bmd-form-group input::placeholder, .bmd-form-group label {
    line-height: 1.1;
}*/

/*.container .form-control, .is-focused .form-control {
    background-image: linear-gradient(0deg,#9c27b0 2px,
    rgba(156,39,176,0) 0),linear-gradient(0deg,#d2d2d2 1px,hsla(0,0%,82%,0) 0) !important;
}*/

.is-focused [class*=" bmd-label"], .is-focused [class^=bmd-label] {
    color: #9c27b0 !important;
}
/*.form-control {
    background: no-repeat bottom,50% calc(100% - 1px);
    background-size: 0 100%,100% 100%;
    border: 0;
    height: 36px;
    transition: background 0s ease-out;
    padding-left: 0;
    padding-right: 0;
    border-radius: 0;
    font-size: 14px !important;
}*/

/* dropdown */

.dropdown-menu.bootstrap-datetimepicker-widget.open {
    opacity: 1;
    transform: scale(1);
    top: 0;
}

.bootstrap-datetimepicker-widget.dropdown-menu {
    padding: 4px;
    width: 19em;
}

.bootstrap-datetimepicker-widget .list-unstyled {
    margin: 0;
}

.sr-only,
.bootstrap-datetimepicker-widget .btn[data-action="incrementHours"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="incrementMinutes"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="decrementHours"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="decrementMinutes"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="showHours"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="showMinutes"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="togglePeriod"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="clear"]::after,
.bootstrap-datetimepicker-widget .btn[data-action="today"]::after,
.bootstrap-datetimepicker-widget .picker-switch::after,
.bootstrap-datetimepicker-widget table th.prev::after,
.bootstrap-datetimepicker-widget table th.next::after {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.bootstrap-datetimepicker-widget {
    list-style: none;
}

.bootstrap-datetimepicker-widget a:hover {
    box-shadow: none !important;
}

.bootstrap-datetimepicker-widget a .btn:hover {
    background-color: transparent;
}

.bootstrap-datetimepicker-widget.dropdown-menu {
    padding: 4px;
    width: 19em;
}

@media (min-width: 768px) {
    .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
        width: 38em;
    }
}

@media (min-width: 991px) {
    .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
        width: 38em;
    }
}

@media (min-width: 1200px) {
    .bootstrap-datetimepicker-widget.dropdown-menu.timepicker-sbs {
        width: 38em;
    }
}

.bootstrap-datetimepicker-widget.dropdown-menu.bottom:before,
.bootstrap-datetimepicker-widget.dropdown-menu.bottom:after {
    right: auto;
    left: 12px;
}

.bootstrap-datetimepicker-widget.dropdown-menu.top {
    margin-top: auto;
    margin-bottom: 27px;
}

.bootstrap-datetimepicker-widget.dropdown-menu.top.open {
    margin-top: auto;
    margin-bottom: 27px;
}

.bootstrap-datetimepicker-widget.dropdown-menu.pull-right:before {
    left: auto;
    right: 6px;
}

.bootstrap-datetimepicker-widget.dropdown-menu.pull-right:after {
    left: auto;
    right: 7px;
}

.bootstrap-datetimepicker-widget .list-unstyled {
    margin: 0;
}

.bootstrap-datetimepicker-widget a[data-action] {
    padding: 0;
    margin: 0;
    border-width: 0;
    background-color: transparent;
    color: #9c27b0;
    box-shadow: none;
}

.bootstrap-datetimepicker-widget a[data-action]:hover {
    background-color: transparent;
}

.bootstrap-datetimepicker-widget a[data-action]:hover span {
    background-color: #eee;
    color: #9c27b0;
}

.bootstrap-datetimepicker-widget a[data-action]:active {
    box-shadow: none;
}

.bootstrap-datetimepicker-widget .timepicker-hour,
.bootstrap-datetimepicker-widget .timepicker-minute,
.bootstrap-datetimepicker-widget .timepicker-second {
    width: 40px;
    height: 40px;
    line-height: 40px;
    font-weight: 300;
    font-size: 1.125rem;
    margin: 0;
    border-radius: 50%;
}

.bootstrap-datetimepicker-widget button[data-action] {
    width: 38px;
    height: 38px;
    margin-right: 3px;
    padding: 0;
}

.bootstrap-datetimepicker-widget .btn[data-action="incrementHours"]::after {
    content: "Increment Hours";
}

.bootstrap-datetimepicker-widget .btn[data-action="incrementMinutes"]::after {
    content: "Increment Minutes";
}

.bootstrap-datetimepicker-widget .btn[data-action="decrementHours"]::after {
    content: "Decrement Hours";
}

.bootstrap-datetimepicker-widget .btn[data-action="decrementMinutes"]::after {
    content: "Decrement Minutes";
}

.bootstrap-datetimepicker-widget .btn[data-action="showHours"]::after {
    content: "Show Hours";
}

.bootstrap-datetimepicker-widget .btn[data-action="showMinutes"]::after {
    content: "Show Minutes";
}

.bootstrap-datetimepicker-widget .btn[data-action="togglePeriod"]::after {
    content: "Toggle AM/PM";
}

.bootstrap-datetimepicker-widget .btn[data-action="clear"]::after {
    content: "Clear the picker";
}

.bootstrap-datetimepicker-widget .btn[data-action="today"]::after {
    content: "Set the date to today";
}

.bootstrap-datetimepicker-widget .picker-switch {
    text-align: center;
    border-radius: 3px;
    font-size: 0.875rem;
}

.bootstrap-datetimepicker-widget .picker-switch::after {
    content: "Toggle Date and Time Screens";
}

.bootstrap-datetimepicker-widget .picker-switch td {
    padding: 0;
    margin: 0;
    height: auto;
    width: auto;
    line-height: inherit;
}

.bootstrap-datetimepicker-widget .picker-switch td span {
    line-height: 2.5;
    height: 2.5em;
    width: 100%;
    border-radius: 3px;
    margin: 2px 0px !important;
}

.bootstrap-datetimepicker-widget table {
    width: 100%;
    margin: 0;
}

.bootstrap-datetimepicker-widget table.table-condensed tr>td {
    text-align: center;
}

.bootstrap-datetimepicker-widget table td>div,
.bootstrap-datetimepicker-widget table th>div {
    text-align: center;
}

.bootstrap-datetimepicker-widget table th {
    height: 20px;
    line-height: 20px;
    width: 20px;
    font-weight: 500;
}

.bootstrap-datetimepicker-widget table th.picker-switch {
    width: 145px;
}

.bootstrap-datetimepicker-widget table th.disabled,
.bootstrap-datetimepicker-widget table th.disabled:hover {
    background: none;
    color: rgba(0, 0, 0, 0.12);
    cursor: not-allowed;
}

.bootstrap-datetimepicker-widget table th.prev span,
.bootstrap-datetimepicker-widget table th.next span {
    border-radius: 3px;
    height: 27px;
    width: 27px;
    line-height: 28px;
    font-size: 12px;
    border-radius: 50%;
    text-align: center;
}

.bootstrap-datetimepicker-widget table th.prev::after {
    content: "Previous Month";
}

.bootstrap-datetimepicker-widget table th.next::after {
    content: "Next Month";
}

.bootstrap-datetimepicker-widget table th.dow {
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    font-size: 12px;
    text-transform: uppercase;
    color: rgba(0, 0, 0, 0.87);
    font-weight: 400;
    padding-bottom: 5px;
    padding-top: 10px;
}

.bootstrap-datetimepicker-widget table thead tr:first-child th {
    cursor: pointer;
}

.bootstrap-datetimepicker-widget table thead tr:first-child th:hover span,
.bootstrap-datetimepicker-widget table thead tr:first-child th.picker-switch:hover {
    background: #eee;
}

.bootstrap-datetimepicker-widget table td>div {
    border-radius: 3px;
    height: 54px;
    line-height: 54px;
    width: 54px;
    text-align: center;
}

.bootstrap-datetimepicker-widget table td.cw>div {
    font-size: .8em;
    height: 20px;
    line-height: 20px;
    color: #999;
}

.bootstrap-datetimepicker-widget table td.day>div {
    height: 30px;
    line-height: 30px;
    width: 30px;
    text-align: center;
    padding: 0px;
    border-radius: 50%;
    position: relative;
    z-index: -1;
    color: #3C4858;
    font-size: 0.875rem;
}

.bootstrap-datetimepicker-widget table td.minute>div,
.bootstrap-datetimepicker-widget table td.hour>div {
    border-radius: 50%;
}

.bootstrap-datetimepicker-widget table td.day:hover>div,
.bootstrap-datetimepicker-widget table td.hour:hover>div,
.bootstrap-datetimepicker-widget table td.minute:hover>div,
.bootstrap-datetimepicker-widget table td.second:hover>div {
    background: #eee;
    cursor: pointer;
}

.bootstrap-datetimepicker-widget table td.old>div,
.bootstrap-datetimepicker-widget table td.new>div {
    color: #999;
}

.bootstrap-datetimepicker-widget table td.today>div {
    position: relative;
}

.bootstrap-datetimepicker-widget table td.today>div:before {
    content: '';
    display: inline-block;
    border: 0 0 7px 7px solid transparent;
    border-bottom-color: #9c27b0;
    border-top-color: rgba(0, 0, 0, 0.2);
    position: absolute;
    bottom: 4px;
    right: 4px;
}

.bootstrap-datetimepicker-widget table td.active>div,
.bootstrap-datetimepicker-widget table td.active:hover>div {
    background-color: #9c27b0;
    color: #fff;
    box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(156, 39, 176, 0.4);
}

.bootstrap-datetimepicker-widget table td.active.today:before>div {
    border-bottom-color: #fff;
}

.bootstrap-datetimepicker-widget table td.disabled>div,
.bootstrap-datetimepicker-widget table td.disabled:hover>div {
    background: none;
    color: rgba(0, 0, 0, 0.12);
    cursor: not-allowed;
}

.bootstrap-datetimepicker-widget table td span {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    margin: 3px 3px;
    cursor: pointer;
    border-radius: 50%;
    text-align: center;
}

.bootstrap-datetimepicker-widget table td span:hover {
    background: #eee;
}

.bootstrap-datetimepicker-widget table td span.active {
    background-color: #9c27b0;
    color: #fff;
}

.bootstrap-datetimepicker-widget table td span.old {
    color: #999;
}

.bootstrap-datetimepicker-widget table td span.disabled,
.bootstrap-datetimepicker-widget table td span.disabled:hover {
    background: none;
    color: rgba(0, 0, 0, 0.12);
    cursor: not-allowed;
}

.bootstrap-datetimepicker-widget .timepicker-picker span,
.bootstrap-datetimepicker-widget .timepicker-hours span,
.bootstrap-datetimepicker-widget .timepicker-minutes span {
    border-radius: 50% !important;
}

.bootstrap-datetimepicker-widget.usetwentyfour td.hour {
    height: 27px;
    line-height: 27px;
}

.btn.btn-primary {
    color: #fff !important;
    background-color: #9c27b0 !important;
    border-color: #9c27b0;
    box-shadow: 0 2px 2px 0 rgba(156, 39, 176, 0.14), 0 3px 1px -2px rgba(156, 39, 176, 0.2), 0 1px 5px 0 rgba(156, 39, 176, 0.12);
}

.btn.btn-primary:hover {
    box-shadow: 0 14px 26px -12px rgba(156, 39, 176, 0.42), 0 4px 23px 0px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(156, 39, 176, 0.2);
}
span.input-group-addon {
    position: absolute;
    right: 5px;
    font-size: 18px;
    z-index: 999999999;
}
  </style>
@endsection
@section('content')
    <div class="container">

        <h4>Audio Broadcast for Later Date and Time</h4>

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

                                Audio Broadast for Later Date and Time

                            </a>

                        </h4>

                    </div>

                    <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">

                        <div class="panel-body">

                            <center>
                                <h3>Audio Broadcast</h3>
                                <div id="form-div">
                                <form id="cbform" method="post" name="main" action="/submitcallschedule">
                                    {{csrf_field()}}
                                    <input name="Action" value="Connect Us Now!" type="hidden">
                                    <input type="hidden" id="List_of_Campaignsid" name="List_of_Campaigns" value="0">
                                    <div>
                                        <!-- <label for="acct" accesskey="A">Account:</label>
                                        <input id="acct" name="Account" maxlength="15" type="text"><br>
                                    
                                        <label for="pwd" accesskey="P">Password:</label>
                                        <input id="pwd" name="Password" maxlength="15" type="password"><br> -->
                                    
                                        <label for="ivn" class="labelCampaignName text-primary" accesskey="H">IVR number:</label>
                                         
                                        <input id="ivn" class="form-control" name="IVR_Number" maxlength="15" type="text" required><br>
                                    
                                        <label for="list" class="labelCampaignName text-primary" accesskey="S">Select Audio Campaign:</label>
                                        <select id="List_of_Campaigns" class="form-control" name="List_of_Campaigns1" multiple="multiple">
                                            <?php if($audioGroupDetailCount > 0){ foreach($audioGroupDetail as $rowLists)

                                            { ?>

                                            <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>

                                            <?php }}?>
                                        </select>
                                         <label for="list" class="labelCampaignName text-primary" accesskey="S">Numbers list:</label>
                                        <textarea id="list" class="form-control" name="List_of_Numbers" rows="3" cols="20"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class='col-sm-6'>
                                            <div class="form-group">
                                                 <label for="list" class="labelCampaignName text-primary" accesskey="S">Date Time:</label>
                                                <div class='input-group date' id='datetimepicker5'>
                                                    <input type='text' class="form-control" name="datetime" required />
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-calendar" aria-hidden="true"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class='col-sm-6'>
                                            <div class="form-group">
                                                <label for="list" class="labelCampaignName text-primary" accesskey="S">Upload File:</label>
                                                <div class='input-group date' id='datetimepicker5'>
                                                    <input type="file" class="form-control" name="file" id="file" onchange="file_upload(this.form,'check.php','list'); return false;" >
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="row">
                                        <input id="submitButton" class="btn btn-info" value="Schedule For Later DateTime" type="submit">
                                    </div>
                                </form>
                                <!-- <form id="upload" action="">
                                    <input type="file" class="form-control" name="file" id="file" onchange="file_upload(this.form,'check.php','list'); return false;" >
                                    <div id="upload_status"></div>
                                </form> -->
                                </div>

                                <!-- <div id="progressbar">
                                    <div>
                                    <label for="status" accesskey="M">Status: <span id="status"></span></label><br>
                                    <label for="counter" accesskey="R">Calls initiated: <span id="counter"> 0 </span><span id="total"> out of 0</span></label><br>
                                    <label for="calling" accesskey="C">Now calling: <span id="calling"></span></label><br></div>
                                    <div style="border: 1px solid black; width:100%; height:1.5em;margin-top:25px;">
                                        <div id="progress" style="height:0.5em; width:0%; background-color:#135791; float:left;"></div>
                                    </div>
                                </div> -->
                            </center>

                                <!-- <li><i class="fas fa-chevron-right"></i><a href="#compose_sms" data-toggle="modal">Compose Message</a></li>

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_send_sms_from_csv" data-toggle="modal">From File Upload</a></li> -->

                        </div>

                    </div>

                    <div class="panel panel-default">

                        <div class="panel-heading" role="tab" id="headingTwo">

                            <h4 class="panel-title">

                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                                    <i class="more-less fa fa-plus"></i>

                                    View Active Schedules

                                </a>

                            </h4>

                        </div>

                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">

                            <div class="panel-body">

                                <div class="table-responsive log-table">

                                    <table class="table table-hover table-responsive log-table" id="ContactsTable">

                                        <thead>

                                            <tr>

                                                <th>#</th>

                                                <th>IVR</th>

                                                <th>Schedule Date Time</th>

                                                <th>Created At</th>

                                                <th>Action</th>

                                            </tr>

                                        </thead>

                                        <tbody>
                 
                                            <?php if($campaignDetCount > 0){

                                                $serial = 1;

                                                foreach($campaignDet as $rowLists)

                                                {

                                            ?>
                                            <td><?php echo $serial; ?></td>
                                            <td><?php echo $rowLists->ivr; ?></td>
                                            <td><?php echo $rowLists->scheduale_time; ?></td>
                                            <td><?php echo $rowLists->created_at; ?></td>
                                            <?php $valll =  $rowLists->id; ?>

                                            <td><a type="button" href="actions/act_removeaudioschedule.php?cid=<?php echo $valll; ?>" class="btn btn-danger">Delete Contact</a></td>
                                            <?php $serial += 1;} }?>

                                        </tbody>
                                    </table>


                            </div>

                        </div>

                    </div>

                </div>
                

    </div><!-- panel-group -->

</div><!-- container -->





    </div>
 
</div>
@endsection
@push('scripts')
<script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/plugins/moment.min.js"></script>
<script src="https://rawgit.com/creativetimofficial/material-kit/master/assets/js/plugins/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('#datetimepicker5').datetimepicker({
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
            // defaultDate: "11/1/2013",
            // disabledDates: [
            //     moment("12/25/2013"),
            //     new Date(2013, 11 - 1, 21),
            //     "11/22/2013 00:53"
            // ]
        });
    });
</script>
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
            $.ajax({
                url:'actions/getaudionumbers.php',
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
        $.ajax({
            url:'check.php',
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
            a = 'call.php?First_Phone_Number='+numbers[i]+'&Second_Phone_Number='+ivn+'&host='+host;
            number = numbers[i];
            $.ajax({
                url:'call.php?First_Phone_Number='+numbers[i]+'&Second_Phone_Number='+ivn+'&host='+host,
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
            window.location.href = "active_calls.php?ivrnumber="+ivn;
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

        $('#cbform').submit(function(){
            $('#List_of_Campaignsid').val($('#List_of_Campaigns').val());
            this.submit();
        });

        /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');

        breadcrumb.text('Audio Broadcast');

        breadcrumb.prop("href", "/users/index.php");*/
        var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b><span>Audio Broadcast</span></b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/later_calls_campaign"]').parents().addClass('active');

        $('#ContactsTable').DataTable();

        $('#List_of_Campaigns').multiselect({

            includeSelectAllOption: true

        });

    } );

</script>
@endpush