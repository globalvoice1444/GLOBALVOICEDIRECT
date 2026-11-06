@extends('layouts.user.user_main')
@section('css')
    <style type="text/css">

        .panel-group {

            background: #ffffff;

            height: auto;

            border-radius: 8px;
            
            box-shadow: 0px 2px 6px #c1c1c1;

            padding: 45px;

            width: 70%;
            
            margin: auto;

        }
        .modal-dialog {

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

        padding: 5px;



    }

    .more-less {
        /* float: right; */
        color: #fff;
        margin-right: 10px;
    }

    .panel-body {

        background-color: #e8e8e8;

        padding: 10px;

        margin: 10px 0px;

    }

    .add-contact-button {
        position: relative;
    }
    .add-contact-button button {
        position: absolute;
        right: 10px;
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
    .view-messages-modal{
        max-width: 939px !important;
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
    #sms_compaign_card {
        padding: 1% 5% 6%;
    }
   
    .modal .modal-content .modal-footer{
        box-shadow: none;
        padding: 10px 0px 0px;
    }
    @media ( max-width: 1024px ){
        #sms_compaign_card {
            padding: 1% 5% 10%;
        }
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
        .panel-group {
            padding: 34px 30px;
            width: 100%;
        }
        #sms_compaign_card {
            padding: 1% 8% 10%;
        }

    }

    </style>
    @endsection
    @section('content')

    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <h5>SMS Campaign</h5>
            </div>
            <div class="card-block" id="sms_compaign_card">

                <?php if(isset( $_GET['message'])){ ?>

                    <div class="alert alert-info">

                      <strong>Info!</strong> <?php echo $_GET['message']; ?>.

                    </div>

                <?php } ?>

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                  
                    <div class="credit-info col-md-12">
                        <div class="main-credit-container">
                            <div class="credit-balance"><span>Credit :</span> <span class="balance"><?php echo $TotNumOfSMS; ?></span></div>
                            <div class="quote-count"><span>Daily Quota Used : </span> <span class="quote"> <?php echo $smsDetCount; ?> of 1000</span></div>
                        </div>
                    </div>

                    <div class="panel panel-default">

                        <div class="panel-heading" role="tab" id="headingOne">

                            <h4 class="panel-title">

                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">

                                    <i class="more-less fa fa-plus"></i>

                                    Send SMS

                                </a>

                            </h4>

                        </div>

                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">

                            <div class="panel-body">

                               

                                    <li><i class="fas fa-chevron-right"></i><a href="#compose_sms" data-toggle="modal">Compose Message</a></li>

                                    <li><i class="fas fa-chevron-right"></i><a href="#modal_send_sms_from_csv" data-toggle="modal">From File Upload</a></li>

                            </div>

                        </div>

                    </div>


                    <div class="panel panel-default">

                        <div class="panel-heading" role="tab" id="headingTwo">

                            <h4 class="panel-title">

                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                                    <i class="more-less fa fa-plus"></i>

                                    Sent SMS

                                </a>

                            </h4>

                        </div>

                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">

                            <div class="panel-body">

                               <li><i class="fas fa-chevron-right"></i><a href="/viewSmsDetail">View</a></li>

                              <!--  <li><i class="fas fa-chevron-right"></i><a href="#">Download</a></li> -->

                            </div>

                        </div>

                    </div>


                    <div class="panel panel-default">

                        <div class="panel-heading" role="tab" id="headingThree">

                            <h4 class="panel-title">

                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                                    <i class="more-less fa fa-plus"></i>

                                    Add Contact By

                                </a>

                            </h4>

                        </div>

                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">

                            <div class="panel-body">

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_add_new_campaign" data-toggle="modal">Add New Campaign Group</a></li>

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_add_contact_individual" data-toggle="modal">Add Individual Contact</a></li>
                                <li><i class="fas fa-chevron-right"></i><a href="#modal_add_contact_individual_group" data-toggle="modal">Add Individual Contact to Group</a></li>

                                 <li><i class="fas fa-chevron-right"></i><a href="#modal_upload_csv" data-toggle="modal">Upload CSV</a></li>

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_table_view_contacts" data-toggle="modal">Views</a></li>

                            </div>

                        </div>

                    </div>

                    <div class="panel panel-default">

                        <div class="panel-heading" role="tab" id="headingFour">

                            <h4 class="panel-title">

                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">

                                    <i class="more-less fa fa-plus"></i>

                                    Top Up Balance

                                </a>

                            </h4>

                        </div>

                        <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">

                            <div class="panel-body">

                                <li><i class="fas fa-chevron-right"></i><a href="buy_credit">Buy Credit</a></li>

                                <li><i class="fas fa-chevron-right"></i><a href="#modal_table_credit_history" data-toggle="modal">Credit History</a></li>

                            </div>

                        </div>

                    </div>

                </div><!-- panel-group -->

            </div><!-- card block -->

        </div>
        <!-- card end -->
    </div>


<div class="modal fade" id="compose_sms" tabindex="-1" role="dialog" aria-labelledby="compose_smsTitle" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="compose_smsTitle">Compose Message</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="send_sms_campaign">

            <form method="POST" action="smscampaign/sendsms" enctype="multipart/form-data" name="group-contact">
            {{csrf_field()}}
                <input type="hidden" name="form-method" value="1">

                <h5 class="title-composeSms">Send SMS to Campaign Group</h5>

                <hr>

                <div class="form-group">

                     <label class="text-info">Select Campaign Name</label>

                    <select name="campaignName" class="form-control">
                        <?php if($smsGroupDetailCount > 0){ 
                            foreach( $smsGroupDetail as $rowLists)
                            { ?>
                        <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>
                        <?php }}?>
                    </select>

                </div>

                    

                <div class="form-group">

                     <label class="text-info">Message</label>

                     <textarea class="form-control" name="smstext" id="smstext" maxlength="2000" required></textarea>

                </div>

                <button class="btn btn-info float-right">Send Message</button>

            </form>
 
        </div>

         <div class="send_sms_individual">

            <form method="POST" action="smscampaign/sendsms" enctype="multipart/form-data" name="group-contact">
            {{csrf_field()}}
                <input type="hidden" name="form-method" value="2">

                <h5 class="title-composeSms">Send SMS Individual</h5>

                <hr>

                <div class="form-group">

                     <label class="text-info">To</label>

                     <input class="form-control" name="smscontactnumber" id="smscontactnumber" minlength="10" maxlength="10" value="" type="text" required>

                     <p style="color: red;">*Please enter the number without country code</p>

                </div>

                    
                <div class="form-group">

                     <label class="text-info">SMS Text</label>

                     <textarea class="form-control" name="smstext" id="smstext" maxlength="2000" required></textarea>

                </div>

                <button class="btn btn-info float-right" type="submit">Send SMS</button>

            </form>

        </div>

            

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

   





<div class="modal fade" id="modal_send_sms_from_csv" tabindex="-1" role="dialog" aria-labelledby="send_sms_from_csv_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="send_sms_from_csv_title">Compose Message</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="compose_sms_modal">

            <form method="POST" action="smscampaign/sendsms" enctype="multipart/form-data" name="group-contact">
              {{csrf_field()}}
                <input type="hidden" name="form-method" value="3">

                <h5 class="title-composeSms">Send SMS to CSV File Contacts</h5>

                <hr>



                <div class="form-group">

                     <label class="text-info">Upload CSV File</label>

                     <input class="form-control" type="file" name="campaignContacts" accept=".csv" required />

                </div>

                    

                <div class="form-group">

                    <label class="text-info">Message</label>

                    <textarea class="form-control" name="smstext" id="smstext" maxlength="2000" required></textarea>

                </div>

                <button class="btn btn-info float-right">Send Message</button>

            </form>

            <div class="form-group">    

                <label>Supported Format:</label>

                <p>column1 = number with country code without + sign. <a href="/sampleCsvContacts" target="_blank">see the example</a></p>

            </div>

        </div>

         <div class="send_sms_from_csv">

            <form method="POST" action="smscampaign/sendsms" enctype="multipart/form-data" name="group-contact">
             {{csrf_field()}}
                <input type="hidden" name="form-method" value="4">



                <h5 class="title-composeSms">Send SMS From CSV</h5>

                <hr>

                <div class="form-group">

                     <label class="text-info">Upload CSV File</label>

                   <input class="form-control" type="file" name="campaignContacts" accept=".csv" required />

                </div>

                

                <button class="btn btn-info float-right" type="submit">Send SMS</button>

            </form>

            <div class="form-group">
                <label>Supported Format:</label>
                <p>column1 = number without country code.</p><p>column2 = SMS Text. <a href="/sampleontactandcontentCsv" target="_blank">see the example</a></p>

            </div>

        </div>

            

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>



<div class="modal fade" id="modal_table_sent_sms" tabindex="-1" role="dialog" aria-labelledby="modal_table_sent_sms_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg view-messages-modal" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="modal_table_sent_sms_title">View Messages</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

            <div class="sent_sms_table_modal">

                <input type="hidden" name="form-method" value="3">



                <h5 class="title-composeSms">All Sent Messages</h5>

                <hr>



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

                                <td><a type="button" href="removeSmsText/<?php echo $valll; ?>" class="btn btn-danger">Delete Contact</a></td>

                                 

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

            </div>        

        </div>

        <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

        </div>

    </div>

  </div>

</div>





<div class="modal fade" id="modal_add_contact_individual" tabindex="-1" role="dialog" aria-labelledby="add_contact_individual_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="add_contact_individual_title">Add Contact</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="add_individual_modal">

          <form  method="POST" action="/contactAction" name="Individual-contacts">
            {{csrf_field()}}

              <input type="hidden" name="form-method" value="4">

              <h5 class="title-composeSms">Individual Contact</h5>

              <hr>
              

              <div class="form-group">

                   <label class="text-info">First Name</label>

                   <input class="form-control" type="text" name="contactfirstName" required />

              </div>

              <div class="form-group">

                   <label class="text-info">Last Name</label>

                   <input class="form-control" type="text" name="contactlastName" required />

              </div>

              <div class="form-group">

                  <label class="text-info">Contact Number</label>

                  <input class="form-control" type="text" name="contactNumber" minlength="10" placeholder="877 308 5803" required />

                   

              </div>
            <div class="modal-footer">
                <button class="btn btn-info" type="submit">Add Contact</button>
            </div>

          </form>

        </div>  

      </div>

    </div>

  </div>

</div>

<div class="modal fade" id="modal_add_contact_individual_group" tabindex="-1" role="dialog" aria-labelledby="add_contact_individual_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="add_contact_individual_title">Add Contact</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="add_individual_modal">
            <form method="POST" action="/contactAction" name="Individual-contacts">
            {{csrf_field()}}
              <input type="hidden" name="form-method" value="3">

              <h5 class="title-composeSms">Individual Contact</h5>

              <hr>


               <div class="form-group">

                   <label class="text-info">Select Campaign Name</label>

                    <select name="campaignName" class="form-control">
                        <?php if($smsGroupDetailCount > 0){ foreach($smsGroupDetail as $rowLists)
                            { ?>
                        <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>
                        <?php }}?>
                    </select>

              </div>
              <div class="form-group">

                   <label class="text-info">First Name</label>

                   <input class="form-control" type="text" name="contactfirstName" required />

              </div>

              <div class="form-group">

                   <label class="text-info">Last Name</label>

                    <input class="form-control" type="text" name="contactlastName" required />


              </div>


              <div class="form-group">

                  <label class="text-info">Contact Number</label>

                  <input class="form-control" type="text" name="contactNumber" minlength="9" maxlength="9" placeholder="877 308 5803" required />
                  <span style="color: red;">*Please Enter the Number without country Code.</span>

              </div>
              <div class="modal-footer">
                <button class="btn btn-info" type="submit">Add Contact To Group</button>
              </div>

          </form>

        </div>  

      </div>

    </div>

  </div>

</div>



<div class="modal fade" id="modal_add_new_campaign" tabindex="-1" role="dialog" aria-labelledby="add_contact_individual_title" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="add_contact_individual_title">Campaign Groups</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="add_campaign_modal">

                    <form method="POST" action="/contactAction" class="compaign_form" name="campaign-group">
                      {{csrf_field()}}
                        <input type="hidden" name="form-method" value="1">

                        <div class="form-group form_group_campaign">

                            <label for="campaignName" class="labelCampaignName text-primary">Campaign Name</label>

                            <input class="form-control" name="campaignName" id="campaignName" value="" type="text" required>

                        </div>

                        <button type="submit" class="campaign-grp-btn btn btn-info btn-blue campaign_add_btn">Add new Campaign</button>

                    </form>

                </div>  

            </div>

        </div>

    </div>

</div>



<div class="modal fade" id="modal_upload_csv" tabindex="-1" role="dialog" aria-labelledby="modal_upload_csv_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="modal_upload_csv_title">Add Contacts</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="add_individual_modal">

          <form method="POST" action="/contactAction" enctype="multipart/form-data" name="group-contact">
              {{csrf_field()}}
             <input type="hidden" name="form-method" value="2">



                <h5 class="title-composeSms">Upload from CSV</h5>

                <hr>



                <div class="form-group">

                    <label class="text-info">Select Campaign Name</label>

                    <select name="campaignName" class="form-control">
                        <?php if($smsGroupDetailCount > 0){ foreach($smsGroupDetail as $rowLists)
                                { ?>
                        <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>
                         <?php }}?>
                    </select>

                </div>

                <div class="form-group">

                    <label class="text-info">Upload CSV File</label>

                    <input class="form-control" type="file" name="campaignContacts" accept=".csv" required />
                    <div class="format-labels">
                        <label >Supported Format:</label>
                        <p><strong>column1 </strong> = Contact First Name. <strong> column2 </strong> = Contact Last Name</p>
                        <p><strong>column3 </strong> = number without country code <a href="sampleCsvContacts" target="_blank">see the example</a></p>
                    </div>

                </div>
              
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Add Contacts to Group</button>
                </div>

          </form>

        </div>  

      </div>

    </div>

  </div>

</div>

<div class="modal fade" id="modal_table_view_contacts" tabindex="-1" role="dialog" aria-labelledby="modal_table_view_contacts_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg " role="document">

    <div class="modal-content sms-sent-modal">

      <div class="modal-header">

        <h4 class="modal-title" id="modal_table_view_contacts_title">View Contacts</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

            <div class="sent_sms_table_modal">

                <input type="hidden" name="form-method" value="3">



                <h5 class="title-composeSms">All Contacts</h5>

                <hr>


                <div class="table-responsive log-table">

                    <table class="table table-hover table-responsive log-table" id="ContactsTable">

                      <thead>

                        <tr>

                          <th>#</th>

                          <th>First Name</th>

                          <th>Last Name</th>

                          <th>Contact Number</th>

                          <th>Campaign Group</th>

                          <th>Created At</th>

                          <th>Action</th>

                        </tr>

                      </thead>

                      <tbody>

                       

                      <?php if($smsContactDetCount > 0){

                        $serial = 1;

                        foreach($smsContactDet as $rowLists)

                        {

                       ?>

                        <tr>



                          <td><?php echo $serial; ?></td>

                          <?php  

                            $nameArray = explode(',', $rowLists->contact_name);

                          ?>

                          <td><?php echo $nameArray[0]; ?></td>

                          <?php if(isset($nameArray[1])){ ?>

                          <td><?php echo $nameArray[1]; ?></td>

                          <?php }else{ ?>

                          <td></td>

                          <?php } ?>

                          <td><?php echo $rowLists->contact_number; ?></td>
                        @inject('object', 'App\Http\Controllers\UserPanelController')
                          <?php 
                               $campainGrpDet=$object::getCampainGrpDetByGroupId($rowLists->group_id);
                               
                            

                            $campainGrpDetCount = count($campainGrpDet);

                            if($campainGrpDetCount > 0){

                              $campainGrpDetail = $campainGrpDet;

                            }

                          ?>

                          <td><?php if( $campainGrpDetCount > 0 ){ 
                          
                            echo $campainGrpDetail[0]->group_name; } ?></td>

                          <td><?php echo $rowLists->created_at; ?></td>

                          <?php $valll =  $rowLists->id; ?>
                            <td>
                                <form action="/deleteSmsContact" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" id="cid" name="cid" value="{{$valll}}">
                                    <button type="submit" href="removeSmsContact" class="btn btn-danger">Delete Contact</button>
                                </form>
                            </td>
                        </form>

                        </tr>

                        <?php $serial = $serial + 1;}} ?>

                      </tbody>

                        <tfoot>

                            <tr>
                                <th>#</th>

                                  <th>First Name</th>

                                  <th>Last Name</th>

                                  <th>Contact Number</th>

                                  <th>Campaign Group</th>

                                  <th>Created At</th>

                                  <th>Action</th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>        
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>

    </div>

  </div>

</div>







<div class="modal fade" id="modal_table_credit_history" tabindex="-1" role="dialog" aria-labelledby="modal_table_credit_history_title" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h4 class="modal-title" id="modal_table_credit_history_title">View Transactions</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

            <div class="sent_sms_table_modal">

                <input type="hidden" name="form-method" value="3">



                <h5 class="title-composeSms">All Transactions</h5>

                <hr>



                <div class="table-responsive log-table">

                    <table class="table table-hover table-responsive log-table" id="creditHistoryTable">

                      <thead>

                        <tr>

                          <th>#</th>

                          <th>Transaction Id</th>

                          <th>Transaction Amount</th>

                          <th>Created At</th>

                        </tr>

                      </thead>

                      <tbody>

                       

                      <?php if($smsCreditDetailCount > 0){

                        $serial = 1;

                        foreach($smsCreditDetail as $rowLists)

                        {

                       ?>

                        <tr>



                          <td><?php echo $serial; ?></td>

                          <td><?php echo $rowLists->transaction_id; ?></td>

                          <td>$<?php echo $rowLists->transaction_amount; ?></td>

                          <td><?php echo $rowLists->created_at; ?></td>

                           

                        </tr>

                        <?php $serial = $serial + 1;}} ?>

                         

                      </tbody>

                      <tfoot>

                              <tr>

                                  <th>#</th>

                          <th>Transaction Id</th>

                          <th>Transaction Amount</th>

                          <th>Created At</th>

                              </tr>

                          </tfoot>

                    </table>

                </div>

            </div>        

        </div>

        <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

        </div>

    </div>

  </div>

</div>
@endsection
@push('scripts')
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

        var breadcrumb = $('.breadcrumb');

        breadcrumb.append('<li class="breadcrumb-item"><b><span>SMS Campaign</span></b></li>');
        $('.main-menu ul').find('.active').removeClass('active');
        $('.main-menu').find('a[href="/smscampaign"]').parents().addClass('active');
        $('#smsContactTable').DataTable();

         $('#ContactsTable').DataTable();

         $('#creditHistoryTable').DataTable();

    } );

</script>

@endpush



