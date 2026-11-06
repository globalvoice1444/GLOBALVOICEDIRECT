@extends('layouts.user.user_main')

@section('css')
<style type="text/css">

  .form_group_campaign{

    width: 100%;

    padding: 20px;

    justify-content: center;

  }

  .labelCampaignName{

    margin-right: 30px;

  }

  .card-header.header-primary {

     background-color: #134576;
     text-align: center;
     font-size: 18px;
     font-weight: 700;
     text-transform: uppercase;
     border-radius:6px 6px 0 0;

  }

  #campaignName {

    width: 50%;
    margin-right: 15px;
    height: 45px;
    background-color: #dfe4e7;
  }

  .tab-content {

      margin: 0px 5px;

      padding: 50px 70px;

      background-color: #dfe4e7;

  }

  .bulk_contacts{

    justify-content: center;

    margin-left: 0px;

  }
  .dataTables_wrapper .dataTables_length select{
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

  .contacts_container {



    margin-bottom: 0px;

  }
  h6 {
    font-size: 16px;
    color: #17a2b8;
    font-size: 16px;
    color: #134576 !important;
    font-weight: 700;
    margin-left: -15px;
    margin-top: 20px;
}
  .card .card-block p {
    line-height: 1.4;
    font-size: 16px;
    margin-left: -15px;
    margin-top: 20px;
}
  h3#exampleModalLabel {
    color: #00bcd4!important;
    margin-left: -15px;
    margin-bottom: 18px;
}
  input#addrow {
    background-color: #134576 !important;
    border-color: #134576 !important;
    color: #fff;
    cursor: pointer;
    -webkit-transition: all ease-in 0.3s;
    transition: all ease-in 0.3s;
    border-radius: 6px !important;
    text-transform: uppercase;
}
input#addrow:hover {
    background-color: #b0cb02 !important;
    border-color: #b0cb02 !important;
    color: #fff;
    cursor: pointer;
    -webkit-transition: all ease-in 0.3s;
    transition: all ease-in 0.3s;
    border-radius: 6px !important;
    text-transform: uppercase;
}
button.btn.btn-info.btn-info {
    border-radius: 6px;
    background-color: #b0cb02;
    border-color: #b0cb02;
    text-transform: uppercase;
}
button.btn.btn-info.btn-info:hover {
    border-radius: 6px;
    background-color: #134576;
    border-color: #134576;
    text-transform: uppercase;
}
button.btn.btn-primary {
    border-radius: 6px;
    background-color: #134576;
    border-color: #134576;
    text-transform: uppercase;
    margin-bottom: 20px;
    margin-left: -15px;
}
button.btn.btn-primary:hover {
    border-radius: 6px;
    background-color: #b0cb02;
    border-color: #b0cb02;
    text-transform: uppercase;
    margin-bottom: 20px;
    margin-left: -15px;
}
input.form-control {
    border-radius: 6px;
    /* border-style: none; */
    /* padding-top: 10px; */
    /* padding-bottom: 10px; */
    border: 1px solid #dfe4e7;
    /*margin-left: -15px;*/
}
label.col-form-label {
    font-size: 16px;
    color: #134576 !important;
    font-weight: 700;
    margin-left: -15px;
}

select.form-control:not([size]):not([multiple]) {
    height: calc(2.25rem + 6px);
    border: 1px solid #dfe4e7;
    margin-left: -15px;
}

  .table td, .table th {
    padding: none !important;
    vertical-align: top;
    border-top:none !important;
}
  .contacts_container {
    border:none !important;
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 50px;
}
  label.my-1.mr-2.text-info {
    font-size: 16px;
    color: #134576 !important;
    font-weight: 700;
    margin-left: -15px;
}
select.form-control.fill {
    border: 1px solid #dee2e6;
    margin-left: -15px;
    height:none !important;
}
  .table-responsive {
    display: inline-block;
    width: 100%;
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0 15px 0 15px;
}
  th.text-info {
    color: #134576 !important;
    text-transform: uppercase;
    padding-left: 30px;
}
  button.campaign-grp-btn.btn.btn-info.btn-blue.campaign_add_btn {
    background-color: #134576;
    border-color: #134576;
    border-radius: 6px;
    text-transform: uppercase;
    margin-top: 20px;
    margin-left: 20px;
}
button.campaign-grp-btn.btn.btn-info.btn-blue.campaign_add_btn:hover {
    background-color: #b0cb02;
    border-color: #b0cb02;
    border-radius: 6px;
    text-transform: uppercase;
    margin-top: 20px;
}
  label.labelCampaignName.text-primary {
    font-size: 16px;
    color: #134576 !important;
    font-weight: 700;
}
  .card {
    border-radius: 6px;
    -webkit-box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
    box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
    border: none;
    margin-bottom: 30px;
    -webkit-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
    display: block;
}

  table#contact_table {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-top: 22px !important;

}

 #contact_table>thead>tr>th{

      padding: 10px 55px;
      color: #134576;
      text-transform: uppercase;

  }
 #contact_table>tbody>tr>td{
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

  .nav-pills li a {
    padding: 17px 35px !important;
  }
  ul.nav.nav-pills {
    margin-left: 10%;
  }
  h6 {
    font-size: 16px;
    color: #17a2b8;
  }
  @media only screen and (min-width: 768px) and (max-width: 1024px){

    .form_group_campaign{

      width: 100%;

    }

     #campaignName {

      width: 69%;

      height: 40px;

    }

    .campaign_add_btn{

      margin-left: 32.4%;

    }

    .bulk_contacts{

      margin-left: 0px;

    }

    #contact_table>tbody>tr>td, #contact_table>thead>tr>th {

        padding: 10px 21px;

    }

  }

  @media only screen and (max-width: 667px){

    .form_group_campaign {

      width: 100%;

      padding: 0px;

    }

    #campaignName {

      width: 100%;

      height: 40px;

    }

    .tab-content{

      padding: 28px 15px;

    }

    .bulk_contacts{

      margin-left: 0px;

    }

}

@media only screen and (max-device-width: 667px) and (min-device-width: 320px){

  .nav.nav-pills {

      height: 175px;

  }

      #contact_table>tbody>tr>td, #contact_table>thead>tr>th {

        padding: 10px 16px;

    }

}

</style>
@endSECTION
<!-- All Content goes here -->
@section('content')
<h4>Audio Campaign</h4>

<div class="row">

   <div class="col-sm-12">

      <div class="card">

         <div class="card-header">

         </div>

         <div class="card-block" id="profile_card">

            <div class="row">

               <div class="col-md-12">

                  <?php if(isset($_GET['message'])){ ?>

                    <div class="alert alert-info" role="alert">

                         <?php echo $_GET['message']; ?>

                    </div>

                  <?php } ?>

                  <div id="wizard">

                     <section>

                        <div class="container">

                          <ul class="nav nav-pills">

                            <li><a class="active show" data-toggle="pill" href="#menu1">View All Subscribers</a></li>

                            <!-- <li><a data-toggle="pill" href="#menu2">Add Single Contact</a></li> -->

                            <li><a data-toggle="pill" href="#menu3">Add Numbers</a></li>

                            <li><a data-toggle="pill" href="#menu4">Upload from CSV File</a></li>

                          </ul>

                          <div class="tab-content">

                            <div id="menu1" class="tab-pane fade in active show">

                              <div class="contacts-table">

                                 <table class="table table-hover table-responsive" id="contact_table">

                                    <thead>

                                        <tr>

                                            <th>#</th>

                                            <th>First Name</th>

                                            <th>Last Name</th>

                                            <th>Number</th>

                                            <th>Campaign Group</th>

                                            <th>Created At</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php if($numberDetCount > 0){

                                          $serial = 1;

                                          foreach($numberDet as $rowLists)
                                          {

                                        ?>

                                          <tr>

                                             <td><?php echo $serial; ?></td>

                                              <?php  

                                                $nameArray = explode(',', $rowLists->name);

                                              ?>

                                              <td><?php echo $nameArray[0]; ?></td>

                                              <?php if(isset($nameArray[1])){ ?>

                                              <td><?php echo $nameArray[1]; ?></td>

                                              <?php }else{ ?>

                                              <td></td>

                                              <?php } ?>

                                              <td><?php echo $rowLists->number; ?></td>

                                              <td><?php echo $rowLists->group_name; ?></td>


                                              <td><?php echo $rowLists->created_at; ?></td>

                                              <?php $valll =  $rowLists->id; ?>

                                              <td><a type="button" onclick="deleteContact(<?php echo $valll; ?>)" class="btn btn-danger">Delete Contact</a></td>                                              
                                          </tr>

                                          <?php $serial = $serial + 1;}} ?>

                                      </tbody>

                                 </table>

                              </div>   

                           </div>

                            <div id="menu2" class="tab-pane fade">

                              <div class="card">

                                <div class="card text-white">

                                  <div class="card-header header-primary">Add Number</div>

                                    <div class="card-body">

                                    <div class="individual-contact-form">

                                       <form method="POST" action="actions/act_add_email_contacts.php" enctype="multipart/form-data" id="contacts_form">

                                          <div class="row justify-content-center">

                                                <div class="col-md-6">

                                                   <h3 class="modal-title text-info" id="exampleModalLabel">Add Individual Number</h3>

                                                </div>

                                          </div>

                                          <input type="hidden" name="form-method" value="4">

                                          <div class="row justify-content-center">

                                             <div class="col-md-6">

                                                <div class="form-group">

                                                   <label for="contactfirstName" class="col-form-label text-info">First Name:</label>

                                                   <input type="text" class="form-control" name="contactfirstName" id="contactfirstName" required>

                                                </div>

                                             </div>

                                          </div>

                                          <div class="row justify-content-center">

                                             <div class="col-md-6 ">

                                                <div class="form-group">

                                                   <label for="lname" class="col-form-label text-info">Last Name:</label>

                                                   <input type="text" class="form-control"  name="contactlastName" required>

                                                </div>

                                             </div>

                                          </div>

                                          <div class="row justify-content-center">

                                             <div class="col-md-6">

                                                <div class="form-group">

                                                   <label for="email" class="col-form-label text-info">Email:</label>

                                                   <input type="email" class="form-control" name="contactEmail" required>

                                                </div>

                                             </div>

                                          </div>

                                          <div class="row justify-content-center">

                                             <div class="col-md-6">

                                                <button type="submit" class="btn btn-primary">Add Contact</button>

                                             </div>

                                          </div>

                                       </form>

                                    </div>

                                  </div>

                                </div>

                              </div>

                            </div>

                            <div id="menu3" class="tab-pane fade">
                               <div class="card">

                                <div class="card text-white">

                                  <div class="card-header header-primary">Add New Audio Campaign</div>

                                  <div class="card-body">

                                    <form method="POST" action="saveAudioBroadcastNumber" class="compaign_form form-inline" name="campaign-group">
                                    {{csrf_field()}}
                                      <input type="hidden" name="form-method" value="1">

                                      <div class="form-group form_group_campaign">

                                        <label for="campaignName" class="labelCampaignName text-primary">Campaign Name</label>

                                        <input class="form-control" name="campaignName" id="campaignName" value="" type="text" required>

                                         <button type="submit" class="campaign-grp-btn btn btn-info btn-blue campaign_add_btn">Add new Audio Campaign</button>

                                      </div>

                                     

                                    </form>

                                  </div>

                                </div>

                              </div>
                              <div class="card">

                                <div class="card text-white">

                                  <div class="card-header header-primary">Add Multiple Numbers</div>

                                  <div class="card-body">

                                    <div class="contacts_container">

                                      <form method="POST" action="saveAudioBroadcastNumber" enctype="multipart/form-data" onsubmit="validateContacts()">
                                      {{csrf_field()}}
                                        <input type="hidden" name="form-method" value="3">

                                         <div class="bulk_contacts">
                                          <div class="form-group col-md-4">

                                            <label class="my-1 mr-2 text-info" for="campaignName">Select Campaign</label>

                                            <select name="campaignName" class="form-control">

                                              <?php if($audioGroupDetailCount > 0){ 
                                                foreach($audioGroupDetail as $rowLists)

                                                  { ?>

                                              <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>

                                               <?php }}?>

                                             </select>

                                          </div>
                                          <table id="bulk_contacts_table" class=" table table-responsive order-list">

                                              <thead id="bulk_contacts_table_header text-info">

                                                  <tr>

                                                      <th class="text-info">First Name</th>

                                                      <th class="text-info">Last Name</th>

                                                      <th class="text-info">Number</th>

                                                      <th class="text-info">Action</th>

                                                  </tr>

                                              </thead>

                                              <tbody>

                                                  <tr>

                                                    <td>

                                                      <input type="text" name="contactfirstName[]" class="form-control" required />

                                                    </td>

                                                    <td>

                                                      <input type="text" name="contactlastName[]" class="form-control" required />

                                                    </td>

                                                    <td>

                                                      <input type="number" name="contactNumber[]" minlength="10" class="form-control" required />

                                                    </td>

                                                    <td><a class="deleteRow"></a></td>

                                                  </tr>

                                              </tbody>

                                              <tfoot>

                                                  <tr>

                                                      <td colspan="3" style="text-align: left;">

                                                          <input type="button" class="btn btn-block btn-success" id="addrow" value="Add Row" />

                                                      </td>

                                                      <td colspan="2" style="text-align: left;">

                                                          <button type="submit" class="btn btn-info btn-info" >Add Subscribers</button>

                                                      </td>

                                                  </tr>

                                                  <tr>

                                                    <td colspan="2"></td>

                                                  </tr>

                                              </tfoot>

                                          </table>

                                        </div>

                                      </form>

                                    </div>

                                  </div>

                                </div>

                              </div>

                            </div>

                            <div id="menu4" class="tab-pane fade">

                              <div class="card">

                                <div class="card text-white">

                                  <div class="card-header header-primary">Upload Subscribers From CSV</div>

                                    <div class="card-body">

                                       <div class="individual-contact-form">

                                          <form method="POST" action="saveAudioBroadcastNumber" enctype="multipart/form-data">
                                          {{csrf_field()}}
                                            <div class="row justify-content-center">

                                               <div class="col-md-8">

                                                  <h3 class="modal-title text-info" id="exampleModalLabel">Add Subscribers</h3>

                                               </div>



                                            </div>

                                               <input type="hidden" name="form-method" value="2">

                                               <div class="row justify-content-center">

                                                  <div class="col-md-8">

                                                    <div class="form-group">

                                                           <label for="campaignName" class="col-form-label text-info">Select Audio Campaign Group:</label>

                                                           <select name="campaignName" class="form-control">

                                                          <?php if($audioGroupDetailCount > 0){ 
                                                            foreach($audioGroupDetail2 as $rowLists)

                                                          { ?>

                                                           <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>

                                                            <?php }}?>

                                                         </select>

                                                     </div>

                                                  </div>

                                               </div>

                                               <div class="row justify-content-center">
                                                  <div class="col-md-8">
                                                     <div class="form-group">
                                                           <label for="upload_from_csv" class="col-form-label">Upload CSV File:</label>
                                                           <input class="form-control" type="file" name="campaignContacts" accept=".csv" required />
                                                     </div>
                                                  </div>
                                               </div>


                                               <div class="row justify-content-center">

                                                  <div class="col-md-8">

                                                     <button type="submit" class="btn btn-primary">Add Subscribers</button>

                                                  </div>
                                               </div>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-8">
                                                        <h6>Supported Format:</h6>
                                                        <p><strong>column1 </strong> = Subscriber First Name. <strong> column2 </strong> = Subscriber Last Name</p>
                                                        <p><strong>column3 </strong> = Subscriber number <a href="/sampleCsvContacts" target="_blank">see the example</a></p>
                                                    </div>
                                                </div>
                                            </form>

                                          </div>

                                        </div>

                                      </div>

                                    </div>

                                  </div>

                               </div>

                            </div>

                          </section>

                        </div>

                      </div>

                    </div>

                 </div>

              </div>

           </div>

        </div> 
@endsection
@push('scripts')
<script>
function deleteContact($id)
{

  if(confirm('Are you sure you want to delete this contact?'))
  {
    window.location.href="/audiosubscriberDelete?cid="+$id+"";
  }
  else
  {
  // console.log('2');
  }

}
        // email add bulk

        var counter = 2;



        $("#addrow").on("click", function () {
        
      //  alert();
       // return false;
          var newRow = $("<tr>");

          var cols = "";

          cols += '<td><input type="text" name="contactfirstName[]" class="form-control" required/></td>';

          cols += '<td><input type="text" name="contactlastName[]" class="form-control" /></td>';

          cols += '<td><input type="number" name="contactNumber[]" class="form-control" minlength="10" required/></td>';



          cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';

          newRow.append(cols);

          $("table.order-list").append(newRow);

          counter++;

      });

      $("table.order-list").on("click", ".ibtnDel", function (event) {

        $(this).closest("tr").remove();       

        counter -= 1;

      });
      /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');

        breadcrumb.text('Audio Campaign');

        breadcrumb.prop("href", "/audiosubscriber");*/
        var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b><a href="/"<span>Home</span></a></b></li>');
    //$('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/audiosubscriber"]').parents().addClass('active');
</script>

<script type="text/javascript">

      $(document).ready( function () {

          $('#contact_table').DataTable();

      } );

</script>

@endpush