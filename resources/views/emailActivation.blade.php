@extends('layouts.app_old')
@section('content')
<div class="store-banr">
	<h2>Please Activate Your Account</h2>
</div>
<div class="container">

	<?php
		if (isset($_GET["message"])) {
			echo '<div class="alert alert-danger">';
			echo $_GET["message"];
			echo "</div>";

		}
	?>

	<div class="row">
		<div class="col-md-4 col-md-offset-4 forgot-rapper">
			<img src="images/gvdlogo.png" alt="">
			<div class="panel panel-default">
			  <div class="panel-heading"><span class="login-text">LOGIN TO YOUR ACCOUNT</span></div>
			  <div class="panel-body">

			  	<div class="col-12 col-sm-12 col-md-12">
				
					<div class="col-12 col-sm-12 col-md-12 fm-p">
						<form method="post" action="/loginAction">
							{{csrf_field()}}
							<div class="form-group">
								<input type="text" class="form-control sn-fm username-input" name="user" placeholder="Username">
							</div>
					
							<div class="form-group">
								<input type="password" class="form-control sn-fm password-input" name="pass" placeholder="Password">
							</div>	
							<div class="form-group">
								<input type="submit" value="LOGIN" class="form-control btn btn-default" name="login">
							</div>
						</form>
					</div>
					
					<div class="col-12 col-sm-12 col-md-12">
						<p class="login-popup-text">
							<a class="sn-a" href="/package-pricing">Register</a>-
							<a class="sn-a" href="/forgotpassword">Forget Password</a>
						</p>
					</div>
				</div>

			  </div>
			</div>
		</div>
		
	</div>

</div>
@endsection