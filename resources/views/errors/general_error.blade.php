<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Error</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:700,900" rel="stylesheet">

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{ asset('css/errors/style.css') }}" />
    <style>
    	@media only screen and (max-width: 768px) {
    	    .notfound .notfound-404 h1 {
    	        left: 52%;
    	        font-size: 210px;
    	    }
    	    .notfound a {
    	        margin-top: 50px;
    	    }
    	        l
    	}

    	@media only screen and (max-width: 480px) {
    	  .notfound .notfound-404 h1 {
    	      font-size: 105px;
    	      left: 53%;
    	      letter-spacing: -2px;
    	  }
    	  .notfound a {
    	      margin-top: 0px;
    	  }
    	}
    </style>
</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>Oops!</h1>
				<h2>SOMETING WENT WRONG!</h2>
			</div>
			<a href="/">Homepage</a>
		</div>
	</div>

</body>

</html>
