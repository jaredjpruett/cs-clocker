function switchLogin(view)
{
    // Determine login interface
	if(view == 1) {
		view = 2;

		var header = 'Employee Login';
		var form = 'Administrative Control Panel';
		var type = 'employees';
	} else {
		view = 1;

		var header = 'Administrator Control Panel';
		var form = 'Employee Login';
		var type = 'administrators';
	}

	var body =
        "<div class='page-header'><h2 style='text-align:center'>" + header + "<br>" +
		    "<small>Texas State University Department of Computer Science</small></h2>
        </div>" +
		"<form class='form-signin' id='login' method='post' action='php/control/login.php'>" +
		    "<input type='text' name='type' value='" + type + "' hidden readonly>" +
		    "<input type='text' class='form-control' id='netid' name='netid' placeholder='Net ID' autocomplete='off' autofocus>" +
		    "<input type='password' class='form-control' name='password' placeholder='Password'>" +
		    "<button class='btn btn-lg btn-primary btn-block' type='submit'>Sign In</button>"
        "</form>" +
		"<div style='margin: 0 auto; text-align: center'>" +
            "<a class='switch' href='#' onclick='return switchLogin(" + view + ");'>" + form + "</a>" +
        "</div>";

    // Update html contents of 'container'
	$('.container').html(body);

	$('#netid').focus();

	return false;
}
