$(document).ready(function() {	
	
	$('.btn').click(function(event) {
	    event.preventDefault();
	    var target = $(this).data('target');
		// console.log('#'+target);
		$('#click-alert').html('data-target= ' + target).fadeIn(50).delay(3000).fadeOut(1000);
	});
// email add bulk
  var counter = 2;

  $("#addrow").on("click", function () {
      var newRow = $("<tr>");
      var cols = "";
      cols += '<td><input type="text" class="form-control" name="contactfirstName' + counter + '" required/></td>';
      cols += '<td><input type="text" class="form-control" name="contactlastName' + counter + '"/></td>';
      cols += '<td><input type="text" class="form-control" name="contactEmail' + counter + '" required/></td>';

      cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
      newRow.append(cols);
      $("table.order-list").append(newRow);
      counter++;
  });
  $("table.order-list").on("click", ".ibtnDel", function (event) {
    $(this).closest("tr").remove();       
    counter -= 1;
  });

//sms add bulk 
  var smsContactCount = 2;

  $("#addSMSContactRow").on("click", function () {
      var addRow = $("<tr>");
      var addCols = "";
      addCols += '<td><input type="text" class="form-control" name="contactfirstName' + smsContactCount + '" required/></td>';
      addCols += '<td><input type="text" class="form-control" name="contactlastName' + smsContactCount + '"/></td>';
      addCols += '<td><input type="text" class="form-control" name="contactNumber' + smsContactCount + '" placeholder="877 308 5803" required/></td>';
      addCols += '<td><input type="button" class="smsContactRowDelBtn btn btn-md btn-danger "  value="Delete"></td>';
      addRow.append(addCols);
      $("table.order-list").append(addRow);
      smsContactCount++;
  });
  $("table.order-list").on("click", ".smsContactRowDelBtn", function (event) {
    $(this).closest("tr").remove();       
    counter -= 1;
  });
});
function validateContacts(){
  if(form.contactfirstName1 !== "" && form.contactEmail1 == ""){
    alert('Email or First name cannot be empty.');
    return false;
  }
  else{
    return true;
  }
}
// Multi-Step Form
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab

// function showTab(n) {
//   // This function will display the specified tab of the form...
//   var x = document.getElementsByClassName("tab");
//   x[n].style.display = "block";
//   //... and fix the Previous/Next buttons:
//   if (n == 0) {
//     document.getElementById("prevBtn").style.display = "none";
//   } else {
//     document.getElementById("prevBtn").style.display = "inline";
//   }
//   if (n == (x.length - 1)) {
//     document.getElementById("nextBtn").innerHTML = "Submit";
//   } else {
//     document.getElementById("nextBtn").innerHTML = "Next";
//   }
//   //... and run a function that will display the correct step indicator:
//   fixStepIndicator(n)
// }

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y,z, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  z = x[currentTab].getElementsByTagName("textarea");

  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }

  }

  for (var k = 0; k < z.length; k++) {
  	if(z[k].value == ""){

    	z[k].className += " invalid";
    	valid = false;
    }
    
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}