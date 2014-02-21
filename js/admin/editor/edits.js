var root = location.protocol + "//" + location.host;
var ac = "/~jaredp/Clocker/php/control/admin/";

var update = root + ac + "update.php";
var remove = root + ac + "remove.php";

// Warn user upon form exit attempt; unsubmitted changes will be lost.
$(window).bind('beforeunload', function() {
	return true;
});

/***************************************************************************************************
* Functions for posting data to be added, updated, or removed.									   *
***************************************************************************************************/

//
function commit_changes(rows, add)
{
	disable(1);

	var anchors = document.getElementsByTagName('a');
	var adds = anchors.length - rows;
	var employee = $('#employees').val();

	//if(verify_input(rows + adds) == false) return;

	var aid = new Array();
	var time = new Array();
	var action = new Array();
	var addition = new Array();

	for(var i = 0; i < rows; i++)
	{
		aid.push(anchors[i].text);
		time.push($('#t' + i).val());
		action.push($('#a' + i).val());
	}

	for(var i = 0; i < adds; i++)
		addition.push($('.nt' + i).val());

	$.ajax({
		type: "POST",
		url: update,
		data: { 'aid' : aid, 'name' : employee, 'time' : time, 'action' : action, 'adds' : adds, 'addition' : addition }
	})
	.done(function(result) {
		$("#content").html(result);
	});
}

function remove_selected(rows)
{
	if(confirm("Are you sure?") == false)
		return;
	
	disable(1);

	var anchors = document.getElementsByTagName('a');
	var employee = $('#employees').val();
	var data = new Array();

	for(var i = 0; i < rows; i += 2)
	{
		if($('#c' + i).prop("checked") == true)
		{
			data.push(anchors[i].text);

			if(i < rows - 1)
				data.push(anchors[i + 1].text);
		}
	}

	$.ajax({
		type: "POST",
		url: remove,
		data: { 'num' : rows, 'data' : data, 'employee' : employee }
	})
	.done(function(result) {
		$("#content").html(result);
	});
}

/***************************************************************************************************
* Functions for asserting the validity of data present on the form.								   *
* This is for the benefit of the user. Posted data will be validated by server.					   *
***************************************************************************************************/

function verify_input(rows)
{
	var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;

	for(var i = 0; i < rows; i++)
	{
		// Assert that only values for the current user are submitted
		// Looks like trash
		if($('#n' + i).val() != $('#employees').val())
		{
			alert("Fatal error or something with " + $('#n' + i).val());
			disable(0);

			return false;
		}

		// Assert that every submitted timestamp is valid
		if(!$('#t' + i).val().match(regex))
		{
			alert("Incorrectly formatted time detected: " + $('#t' + i).val());
			disable(0);

			return false;
		}
	}

	return true;
}
