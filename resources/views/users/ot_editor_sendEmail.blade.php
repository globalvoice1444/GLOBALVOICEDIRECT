@extends('layouts.user.user_main')
@section('needs_ckeditor', '1')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
<div class="col-sm-12">
<h4>Create a New Email</h4>

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
              <div class="send_email">
                <form class="form-material" method="post" action="/act_ot_sendEmail" enctype="multipart/form-data" name="miniFax" >
                  {{csrf_field()}}
                    <div class="form-group">
                      <div class="form-group form-primary">
                          
                          <select name="grpId" class="form-control fill">
                            <?php if($smsGroupDetailCount > 0){ 
                              foreach($smsGroupDetail as $rowLists )
                                { ?>
                            <option value="<?php echo $rowLists->id; ?>"><?php echo $rowLists->group_name; ?></option>
                             <?php }}?>
                          </select>
                          <span class="form-bar"></span>
                          <label class="float-label">Select Campaign</label>
                      </div>
                    </div>
                    <div class="form-group form-primary">
                      <input class="form-control" name="subject" id="subject" value="" type="text" required />
                      <span class="form-bar"></span>
                      <label class="float-label">Subject</label>
                    </div>
                    <div class="form-group form-primary">
                      <textarea name="editor1" id="editor1"  rows="10" cols="80" placeholder="Add Email Content" required></textarea>
                    </div>
                    <div class="mt-20 form-group form-primary">
                      <button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
                        <i class="fa fa-paper-plane"></i> Send Email
                      </button>
                    </div>
                  </form>
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
<script type="text/javascript" src="public/users/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
  CKEDITOR.replace( 'editor1' );
</script>
<script type="text/javascript">
    $(document).ready( function () {
        /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');
        breadcrumb.text('Create a New Email');
        breadcrumb.prop("href", "ot_editor_sendEmail");*/
        var breadcrumb = $('.breadcrumb');
      breadcrumb.append('<li class="breadcrumb-item"><b><a href="/ot_editor_sendEmail"><span>Create a New Email</span></a></b></li>');
      $('.main-menu ul').find('.active').removeClass('active');
      $('.main-menu').find('a[href="/ot_editor_sendEmail"]').parents().addClass('active');
      } );
</script>
@endpush

