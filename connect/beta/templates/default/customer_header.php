<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
	<head>
		<title><?php echo $text[strtoupper($layout)]; ?></title>
	
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta charset="utf-8">
	
		<link href="<?php echo $path; ?>css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="<?php echo $path; ?>css/bootstrap-switch.css" rel="stylesheet" media="screen">
		<link href="<?php echo $path; ?>css/bootstrap-additions.css" rel="stylesheet">
		<link href="<?php echo $path; ?>css/theme.css" rel="stylesheet" media="screen">
	
		<script src="<?php echo $path; ?>js/jquery-1.11.0.min.js"></script>
		<script src="<?php echo $path; ?>js/bootstrap.min.js"></script>
		<script src="<?php echo $path; ?>js/bootstrap-switch.min.js"></script>
		<script>
			var JS_MAND_CHECKBOX = '<?php echo $text['JS_MAND_CHECKBOX']; ?>',
				JS_ENTER_VALID_EMAIL = '<?php echo $text['JS_ENTER_VALID_EMAIL']; ?>',
				JS_SELECT_ITEM = '<?php echo $text['JS_SELECT_ITEM']; ?>',
				JS_INVALID_CHARACTER = '<?php echo $text['JS_INVALID_CHARACTER']; ?>',
				JS_MAND_FIELD = '<?php echo $text['JS_MAND_FIELD']; ?>',
				JS_NOT_SET = '<?php echo $text['NOT_SET']; ?>',
				lang = '<?php echo $lang; ?>',
				root_path = '<?php echo $root_path; ?>';
		</script>
	</head>



	<body>


	<div class="dashboard-section">
		<div class="left-column collapse" id="sidebar">
			<div class="logo-row text-center">
				<a href="#"><img src="images/admin-logo.png"></a>
			</div>
			<nav class="left-menu" id="left-menu-bar">
				<ul class="list-unstyled">
					<li class="analyzer-icon ">
						<a href="/"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
					</li>
					<?php if(strpos($actual_link, 'profile')){ ?>
					<li class="contacts-icon select-item">
						<a href="/users/profile.php"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
					</li>
					<?php }else{?>
					<li class="contacts-icon">
						<a href="/users/profile.php"><i class="fa fa-user" aria-hidden="true"></i> Profile</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'orders')){ ?>
					<li class="entities-icon select-item">
						<a href="/users/orders.php"><i class="fa fa-briefcase" aria-hidden="true"></i> Orders</a>
					</li>
					<?php }else{?>
					<li class="entities-icon ">
						<a href="/users/orders.php"><i class="fa fa-briefcase" aria-hidden="true"></i> Orders</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'Payments')){ ?>
					<li class="document-icon select-item">
						<a href="/users/Payments.php"><i class="fa fa-money" aria-hidden="true"></i> Payments</a>
					</li>
					<?php }else{?>
					<li class="document-icon">
						<a href="/users/Payments.php"><i class="fa fa-money" aria-hidden="true"></i> Payments</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'Invoices')){ ?>
					<li class="reports-icon select-item">
						<a href="/users/Invoices.php"><i class="fa fa-fax" aria-hidden="true"></i> Invoices</a>
					</li>
					<?php }else{?>
					<li class="reports-icon">
						<a href="/users/Invoices.php"><i class="fa fa-fax" aria-hidden="true"></i> Invoices</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'Statements')){ ?>
					<li class="reports-icon select-item">
						<a href="/users/Statements.php"><i class="fa fa-university" aria-hidden="true"></i> Statements</a>
					</li>
					<?php }else{?>
					<li class="reports-icon">
						<a href="/users/Statements.php"><i class="fa fa-university" aria-hidden="true"></i> Statements</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'BillPay')){ ?>
					<li class="dashboard-icon select-item">
						<a href="/users/BillPay.php"><i class="fa fa-credit-card" aria-hidden="true"></i> Bill Pay</a>
					</li>
					<?php }else{?>
					<li class="dashboard-icon">
						<a href="/users/BillPay.php"><i class="fa fa-credit-card" aria-hidden="true"></i> Bill Pay</a>
					</li>
					<?php } ?>
					<?php if(strpos($actual_link, 'connect')){ ?>
					<li class="dashboard-icon select-item">
						<a href="/users/connect/beta/"><i class="fa fa-credit-card" aria-hidden="true"></i> Packages</a>
					</li>
					<?php }else{?>
					<li class="dashboard-icon">
						<a href="/users/connect/beta/"><i class="fa fa-credit-card" aria-hidden="true"></i> Packages</a>
					</li>
					<?php } ?>
					<li class="contacts-icon">
						<a href="/users/action/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
					</li>


				</ul>
			</nav>
		</div>

		<div id="content" class="right-column">
			<div class="admin-top-header">
				<div class="container-fluid text-center">
					<p>Welcome Back <strong><?php echo $_SESSION["display_name"];  ?>!</strong> </p>
				</div>
			</div>