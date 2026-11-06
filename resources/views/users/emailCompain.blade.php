@extends('layouts.user.user_main')
@section('css')
<style type="text/css">

  .form_group_campaign{

    width: 80%;

    padding: 20px;

    justify-content: center;

  }

  .labelCampaignName{

    margin-right: 30px;

  }

  .card-header.header-primary {

     background-color: #4fc3f7;

  }

  #campaignName {

    width: 79%;

    height: 45px;

  }

  .tab-content {

      margin: 0px 5px;

      padding: 28px 34px;

      background-color: #f3f1f1;

  }

  .bulk_contacts{

    justify-content: center;

    margin-left: 83px;

  }

  

  .contacts_container {



    margin-bottom: 0px;

  }

  #contact_table>tbody>tr>td, #contact_table>thead>tr>th{

      padding: 10px 55px;

  }
  .nav-pills li a {
    padding: 17px 35px !important;
  }
  ul.nav.nav-pills {
    margin-left: 10%;
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

@endsection
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
<!-- All Content goes here -->

<div class="col-sm-12">
    <div class="card">

        <div class="card-header">
            <h5>Email Campaign</h5>
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

                            <li><a class="active show" data-toggle="pill" href="#menu1">View All Contacts</a></li>

                            <!-- <li><a data-toggle="pill" href="#menu2">Add Single Contact</a></li> -->

                            <li><a data-toggle="pill" href="#menu3">Add Bulk Contacts</a></li>

                            <li><a data-toggle="pill" href="#menu4">Upload from .CSV File</a></li>

                          </ul>

                          <div class="tab-content">

                              <div id="menu1" class="tab-pane fade in active show">

                                <div class="contacts-table">

                                   <table class="table table-hover table-responsive" id="contact_table">

                                      <thead>

                                          <tr>

                                             <th scope="col">#</th>

                                             <th scope="col">Name</th>

                                             <th scope="col">Email</th>

                                             <th scope="col">Campaign Group</th>

                                             <th scope="col">Action</th>

                                          </tr>

                                      </thead>

                                      <tbody>

                                          <?php if($smsContactDetCount > 0){

                                            $serial = 1;

                                            foreach($smsContactDet as $rowLists )

                                            {

                                          ?>

                                            <tr>



                                               <td><?php echo $serial; ?></td>

                                               <?php  

                                                  $nameArray = explode(',', $rowLists->contact_name);

                                               ?>

                                               <td><?php echo $nameArray[0]; ?></td>



                                               <td><?php echo $rowLists->contact_address; ?></td>
                                              @inject('service', 'App\Http\Controllers\UserPanelController')
                                               <?php 
                                          
                                            $campainGrpDet=$service::GetcampainGrpDet($rowLists->group_id);
                                             if($campainGrpDet !=null)
                                             {
                                              $campainGrpDetCount=1;
                                              //greather than
                                             }
                                             else
                                             {
                                              $campainGrpDetCount=0;
                                             }
                                             

                                            
                                                  // var_dump($campainGrpDetCount);
                                                   //die();

                      

                                                  if($campainGrpDetCount > 0){

                                                     $campainGrpDetail =$campainGrpDet;

                                                  }
                                               /*var_dump($campainGrpDetail->group_name); die();*/
                                               ?>

                                               <td><?php if( $campainGrpDetCount > 0 )
                                               {
                                             echo $campainGrpDetail->group_name;
                                              ; } ?></td>

                                                      <?php $valll =  $rowLists->id; ?>
                                               <td>
                                                  <form action="/deleteEmailContact" method="POST">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" id="cid" name="cid" value="{{$valll}}">
                                                    <button type="submit" class="btn btn-danger" id="delete_contact_btn">Delete Contact</a>
                                                  </form>
                                              </td>
          
                                                

                                            </tr>

                                            <?php $serial = $serial + 1;}} ?>

                                        </tbody>

                                   </table>

                                </div>   

                              </div>

                              <div id="menu2" class="tab-pane fade">

                                <div class="card">

                                  <div class="card text-white">

                                    <div class="card-header header-primary">Add Contact</div>

                                      <div class="card-body">

                                      <div class="individual-contact-form">

                                         <form method="POST" action="/act_add_email_contacts" enctype="multipart/form-data" id="contacts_form">
                                         {{csrf_field()}}
                                            <div class="row justify-content-center">

                                                  <div class="col-md-6">

                                                     <h3 class="modal-title text-info" id="exampleModalLabel">Add Individual Contact</h3>

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

                                <div class="text-white">

                                  <div class="card-header header-primary">Add New Campaign</div>

                                  <div class="card-body">

                                    <form method="POST" action="/act_add_email_contacts" class="compaign_form form-inline" name="campaign-group">
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

                              <div class="card">

                                <div class="text-white">

                                  <div class="card-header header-primary">Add Multiple Contacts</div>

                                  <div class="card-body">

                                    <div class="contacts_container">

                                      <form method="POST" action="/act_add_email_contacts" enctype="multipart/form-data" onsubmit="validateContacts()">
                                        {{csrf_field()}}

                                        <input type="hidden" name="form-method" value="3">

                                         <div class="bulk_contacts">

                                          <div class="form-group col-md-4">

                                            <label class="my-1 mr-2 text-info" for="campaignName">Select Campaign</label>

                                            <select name="campaignName" class="form-control">
                                           
                                              <?php if($smsGroupDetailCount > 0){ foreach($smsGroupDetail as $rowLists)

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

                                                      <th class="text-info">Email</th>

                                                      <th class="text-info">Action</th>



                                                  </tr>

                                              </thead>

                                              <tbody>

                                                  <tr>

                                                    <td>

                                                      <input type="text" name="contactfirstName1" class="form-control" required />

                                                    </td>

                                                    <td>

                                                      <input type="text" name="contactlastName1" class="form-control" required />

                                                    </td>

                                                    <td>

                                                      <input type="email" name="contactEmail1"  class="form-control" required />

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

                                                          <button type="submit" class="btn btn-info btn-info" >Add Contacts</button>

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

                                <div class="text-white">

                                  <div class="card-header header-primary">Upload Contacts From CSV</div>

                                        <div class="card-body">

                                            <div class="individual-contact-form">

                                                <form method="POST" action="/act_add_email_contacts" enctype="multipart/form-data">
                                                  {{csrf_field()}}
                                                  <div class="row justify-content-center">

                                                     <div class="col-md-6">

                                                        <h3 class="modal-title text-info" id="exampleModalLabel">Add Contacts</h3>

                                                     </div>

                                                  </div>

                                                   <input type="hidden" name="form-method" value="2">

                                                   <div class="row justify-content-center">

                                                      <div class="col-md-6">

                                                         <div class="form-group">

                                                               <label for="campaignName" class="col-form-label text-info">Select Campaign Group:</label>

                                                               <select name="campaignName" class="form-control">

                                                               <?php if($smsGroupDetailCount > 0){
                                                               
                                                                foreach($smsGroupDetail as $rowLists)

                                                                     { ?>

                                                               <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>

                                                                <?php }}?>

                                                             </select>

                                                         </div>

                                                      </div>

                                                   </div>

                                                   <div class="row justify-content-center">
                                                      <div class="col-md-6">
                                                         <div class="form-group">
                                                               <label for="upload_from_csv" class="col-form-label">Upload CSV File:</label>
                                                               <input class="form-control" type="file" name="campaignContacts" accept=".csv" required />
                                                         </div>
                                                      </div>
                                                   </div>


                                                   <div class="row justify-content-center">

                                                      <div class="col-md-6">

                                                         <button type="submit" class="btn btn-primary">Add Contacts</button>

                                                      </div>

                                                   </div>
                                                    <div class="row justify-content-center">
                                                        <div class="col-md-6">
                                                            <h6>Supported Format:</h6>
                                                            <p>Column 1 = First Name, Column 2 = Last Name<br>Column 3 = Email <a class="text-info" href="sampleCsv" target="_blank">see the example</a></p>
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
</div>
@endsection
@push('scripts')
<script>

        // email add bulk

        var counter = 2;



        $("#addrow").on("click", function () {

          var newRow = $("<tr>");

          var cols = "";

          cols += '<td><input type="text" class="form-control" name="contactfirstName' + counter + '" required/></td>';

          cols += '<td><input type="text" class="form-control" name="contactlastName' + counter + '"/></td>';

          cols += '<td><input type="email" class="form-control" name="contactEmail' + counter + '" required/></td>';



          cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';

          newRow.append(cols);

          $("table.order-list").append(newRow);

          counter++;

      });

      $("table.order-list").on("click", ".ibtnDel", function (event) {

        $(this).closest("tr").remove();       

        counter -= 1;

      });

</script>

<script type="text/javascript">

      $(document).ready( function () {
          var breadcrumb = $('.breadcrumb');

          breadcrumb.append('<li class="breadcrumb-item"><b><span>Email Campaign</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="emailCampaign"]').parents().addClass('active').addClass('pcoded-trigger');
          $('#contact_table').DataTable();

      } );

</script>
@endpush