	<?php

	include '../includes/header.php';
	?>
	<div class="store-banr">
		<h2>Please Login to Your Admin Account</h2>
	</div>
	<div class="container">
		<div class="alert alert-info">
  			<?php if(isset($_GET["message"])){echo $_GET["message"]; }?>
		</div>
		<div class="panel panel-default input-modal">
			<div class="panel-heading">
				<div class="title-circle">
					<p>Login</p>
				</div>
			</div>
			<div class="panel-body text-left">
				<!-- <div class="form-group">
					<p>Before you can login, you must active your account with the code sent to your email address.</p>
					<p>If you did not receive this email, please check your junk/spam folder.
						Click <a href="#" title="">here</a> to resend the activation email.
					</p>
				</div> -->
				<form method="post" action="/admin/action/loginAction.php">
					<div class="form-group">
						<label>User Name:</label>
						<input type="text" name="user" class="form-control" required="user">
					</div>
					<div class="form-group">
						<label>password:</label>
						<input type="password" name="pass" class="form-control" required="pass">
					</div>
					<div class="form-group input-action">
						<!-- <div class="checkbox">
							<label>
								<input type="checkbox" value="">
								<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
								Remember
							</label>
						</div> -->
						<!-- <a href="#" title="">Forgot Password</a> -->
					</div>
					<button type="submit" name="login" class="btn btn-custom btn-blue">Login</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include '../includes/footer.php';?>