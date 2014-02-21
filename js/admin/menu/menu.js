var root = location.protocol + "//" + location.host;
var av = root + "/~jaredp/Clocker/php/view/admin/";

// Script URLs
var stat = av + "status.php";
var hours = av + "hours.php";
var editor = av + "editor.php";

// Form used by subviews
var form = "<form class='form-signin' method='post' action='../../control/logout.php'>" +
	"<button class='btn btn-lg btn-primary btn-block' type='button' onclick='show_dash()'>Go Back</button>" +
	"<button class='btn btn-lg btn-primary btn-block' type='submit'>Log Out</button>" +
	"</form>";

// Call PHP script to display employees currently clocked in
function get_working()
{
	$.ajax({
		type: "POST",
		url: stat
	})
	.done(function(result) {
		$("#content").html(result + form);
	});
}

// Call PHP script to display hours worked this period by each employee
function show_hours()
{
	$.ajax({
		type: "POST",
		url: hours
	})
	.done(function(result) {
		$("#content").html(result + form);
	});
}

// Reset view
function show_dash()
{
	var content = "<form class='form-signin' method='post' action='../../control/logout.php'>" +
		"<button class='btn btn-lg btn-primary btn-block' type='button' onclick='edit_mode()'>Go to Edit Mode</button>" +
		"<button class='btn btn-lg btn-primary btn-block' type='button' onclick='show_hours()'>Show Hours Worked This Period</button>" +
		"<button class='btn btn-lg btn-primary btn-block' type='button' onclick='get_working()'>Show Employees Clocked In</button>" +
		"<button class='btn btn-lg btn-primary btn-block' type='submit'>Log Out</button></form>";
	
	$("#content").html(content);
}

// Enter edit mode
function edit_mode()
{
	window.location = editor;
}

