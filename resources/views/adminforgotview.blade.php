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
    <div class="col-md-4 col-md-offset-4 forgot-rapper">
      <img src="https://dev.globalvoicedirect.com/images/gvdlogo.png" alt="">
      <div class="panel panel-default">
        <div class="panel-heading"><span class="login-text">ADMIN FORGOT PASSWORD</span></div>
        <div class="panel-body">

          <div class="col-12 col-sm-12 col-md-12">
        
          <div class="col-12 col-sm-12 col-md-12 fm-p">
            <form id="register-form" role="form" autocomplete="off" class="form" method="post" action="requestforresetpassword">
              {{ csrf_field() }}
              <div class="form-group">
                <input id="email" name="email" placeholder="email address" class="form-control sn-fm username-input"  type="email" required />
              </div>
              <div class="form-group fogot-footer-buttons">
                <button class="form-control btn btn-default footer-btn goback-btn" onclick="goback()">GO BACK</button>
                <input type="submit" value="RESET PASSWORD" class="form-control btn btn-default footer-btn submit-btn" name="recover-submit">
              </div>
            </form>
          </div>
        </div>

        </div>
      </div>
            <!-- <div class="panel panel-default">
              <div class="panel-body">
                <div class="text-center">
                  <h3><i class="fa fa-lock fa-4x"></i></h3>
                  <h2 class="text-center">Forgot Password?</h2>
                  <p>You can reset your password here.</p>
                  <div class="panel-body">

                    <form id="register-form" role="form" autocomplete="off" class="form" method="post" action="/action/resetPassword.php">

                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                          <input id="email" name="email" placeholder="email address" class="form-control"  type="email" required />
                        </div>
                      </div>
                      <div class="form-group">
                        <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                      </div>

                      <input type="hidden" class="hide" name="token" id="token" value="">
                    </form>

                  </div>
                </div>
              </div>
            </div> -->
    </div>
  </div>
</div>
@push('scripts')
<script type="text/javascript">
  function goback(){
    if(confirm("Are you sure you want to navigate away from this page?"))
    {
        history.go(-1);
    }
  }     
</script>
@endpush
@endsection