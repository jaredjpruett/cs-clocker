var root = location.protocol + "//" + location.host;
var ec = root + "/~jaredp/Clocker/php/control/employee/";
var ev = root + "/~jaredp/Clocker/php/view/employee/";

var act = ec + "act.php";
var inf = ev + "info.php";
var menu = ev + "menu.php";

function clock()
{
	$.ajax({
		type: "POST",
		url: act
	})
	.done(function(result) {
		$("#outer").html(result);
	});
}

function info()
{
	$.ajax({
		type: "POST",
		url: inf
	})
	.done(function(result) {
		$("#outer").html(result);
	});
}

function dash()
{
	window.location.replace(menu);
}
