@extends('layouts.app')
@section('content')
<style type="text/css">
	#wrapper {
	 z-index: 999999;
    font-family: Lato;
    font-size: 1.5rem;
    text-align: center;
    box-sizing: border-box;
    color: #333;
    position: absolute;
    width: 100%;
    background-color: #4a4a4a;
}
 #wrapper #dialog {
	 border: solid 1px #ccc;
	 margin: 10px auto;
	 padding: 20px 30px;
	 display: inline-block;
	 box-shadow: 0 0 4px #ccc;
	 background-color: #faf8f8;
	 overflow: hidden;
	 position: relative;
	 max-width: 450px;
}
 #wrapper #dialog h3 {
	 margin: 0 0 10px;
	 padding: 0;
	 line-height: 1.25;
}
 #wrapper #dialog span {
	 font-size: 90%;
}
 #wrapper #dialog #form {
	 max-width: 240px;
	 margin: 25px auto 0;
}
 #wrapper #dialog #form input {
	 margin: 0 5px;
	 text-align: center;
	 line-height: 80px;
	 font-size: 50px;
	 border: solid 1px #ccc;
	 box-shadow: 0 0 5px #ccc inset;
	 outline: none;
	 width: 20%;
	 transition: all 0.2s ease-in-out;
	 border-radius: 3px;
}
 #wrapper #dialog #form input:focus {
	 border-color: #3183d7;
	 box-shadow: 0 0 5px #3183d7 inset;
}
 #wrapper #dialog #form input::selection {
	 background: transparent;
}
 #wrapper #dialog #form button {
	 margin: 30px 0 50px;
	 width: 100%;
	 padding: 6px;
	 background-color: #3183d7;
	 border: none;
	 text-transform: uppercase;
}
 #wrapper #dialog .close {
	 border: solid 2px;
	 border-radius: 30px;
	 line-height: 19px;
	 font-size: 120%;
	 width: 22px;
	 position: absolute;
	 right: 5px;
	 top: 5px;
}
 #wrapper #dialog div {
	 position: relative;
	 z-index: 1;
}
 #wrapper #dialog img {
	 position: absolute;
	 bottom: -70px;
	 right: -63px;
}
 
</style>
<div>
	<div class="affiliate-member-container">
		<div id="wrapper">
		  	<div id="dialog">
			    <a class="close" href="/affiliate">×</a>
			    <h3>Please enter the 4-digit verification code we sent via Email:</h3>
			    <span>(we want to make sure it's you)</span>
			    <form action="{{url('affiliate/verify-code')}}" method="POST">
			    	{{csrf_field()}}
			    	<div id="form">
				      	<input type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}"  name="digit1" />
				      	<input type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" name="digit2" /><input type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" name="digit3" /><input type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" name="digit4" />
				      	<button class="btn btn-primary btn-embossed" type="submit" disabled>Verify</button>
				    </div>
				    <div>
				      Didn't receive the code?<br />
				      	<a href="#">Send code again</a><br />
				      	<a href="/affiliate">Change Email</a>
				    </div>
			    </form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
	$(function() {
  'use strict';

  var body = $('#form');

  function goToNextInput(e) {
    var key = e.which,
      t = $(e.target),
      sib = t.next('input');

    if (key != 9 && (key < 48 || key > 57)) {
      e.preventDefault();
      return false;
    }

    if (key === 9) {
      return true;
    }

    if (!sib || !sib.length) {
      sib = body.find('input').eq(0);
    }
    sib.select().focus();
  }

  function onKeyDown(e) {
    var key = e.which;

    if (key === 9 || (key >= 48 && key <= 57)) {
    	
      return true;
    }
   
    e.preventDefault();
    return false;
  }
  function onKeyUp(){
  	var inputs = body.find('input');
	var count = 0;
	$.each(inputs,function(e){
		if (inputs[e].value) {
			count++;
		}
	});
	if (count == 4) {
		$('.btn-embossed').removeAttr('disabled');
	}else{
		$('.btn-embossed').attr('disabled',true);
	}
  }
  function onFocus(e) {
    $(e.target).select();
  }

  body.on('keyup', 'input', goToNextInput);
  body.on('keydown', 'input', onKeyDown);
  body.on('keyup', 'input', onKeyUp);
  body.on('click', 'input', onFocus);
});
</script>
@stop