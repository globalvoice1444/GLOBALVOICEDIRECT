
@extends('layouts.app_old')

@section('content')

<div class="form-gap"></div>
<div class="container resetpass">
  @if(session()->has('message')) 
	<div class="alert alert-success">
    {{session()->get('message')}}
	</div>
  @endif
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="text-center">
                  <h3><i class="fa fa-lock fa-4x"></i></h3>
                  <h2 class="text-center">Admin Forgot Password?</h2>
                  <p>You can reset your password here.</p>
                  <div class="panel-body">

                    <form id="register-form" role="form" autocomplete="off" class="form" method="post" action="actionresetnewadminpassword">
                    {{csrf_field()}}
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                          <input id="password" name="password" placeholder="New Password" class="form-control"  type="password" required />
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                          <input id="password1" name="repassword" placeholder="Re-enter The Password" class="form-control"  type="password" required />
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                      </div>
                      <?php if(isset($_GET['em']) and isset($_GET['code'])){ ?>
                        <input type="hidden" class="hide" name="email" id="email" value="<?php echo $_GET['em'] ;?>">
                        <input type="hidden" class="hide" name="token" id="token" value="<?php echo $_GET['code'] ;?>">
                      <?php } ?>
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
	</div>
</div>
@endsection