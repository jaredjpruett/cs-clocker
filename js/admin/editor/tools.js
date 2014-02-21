/***************************************************************************************************
* Functions for either adding or removing a new action or period								   *
***************************************************************************************************/

function period_add(rows)
{
	disable(1);

	var employee = $('#employees').val();
	var next = rows + 1;
	var timeIn = get_time(0);
	var timeOut = get_time(1);

	var period = "<a value='-1' hidden>-1</a><a value='-1' hidden>-1</a>" +
		"<input id='n" + rows + "' type='text' value='" + employee + "' readonly disabled>" +
		"<input id='n" + next + "' type='text' value='" + employee + "' readonly hidden disabled>" +
		"<input id='t" + rows + "' class='nt0 nt' type='text' placeholder='Time in' value='" + timeIn + "'>" +
		"<input id='t" + next + "' class='nt1 nt' type='text' placeholder='Time out' value='" + timeOut + "'>" +
		"<input id='a" + rows + "' type='text' value='clock_in' disabled hidden readonly>" +
		"<input id='a" + next + "' type='text' value='clock_out' readonly hidden disabled>";
	var buttons = "<input type='button' value='Update' id='update' onclick='commit_changes(" + rows + ", " + 2 + ")'>" +
		"<input type='button' value='Cancel' id='addper' onclick='period_cancel(" + rows + ")'>" +
		"<input type='submit' value='Log out' id='logout'>";

	$("#insert").html(period);
	$("#buttons").html(buttons);
}

function action_add(rows)
{
	disable(1);

	var actions = document.getElementsByTagName('a');
	var employee = $('#employees').val();
	var time = get_time(0);

	if(actions.length % 2 == 0)
	{
		var action = "clock_in";
		var placeholder = "Time in";
	}
	else
	{
		var action = "clock_out";
		var placeholder = "Time out";
	}

	var period = "<a id='period' value='-1' hidden>-1</a>" + 
		"<input id='n" + rows + "' type='text' value='" + employee + "' readonly disabled>" +
		"<input id='t" + rows + "' class='nt0' type='text' placeholder='YYYY-MM-DD HH:MM:SS' value='" + time + "'>" +
		"<input id='a" + rows + "' type='text' value='" + action + "' readonly disabled>";
	var buttons = "<input type='button' value='Update' id='update' onclick='commit_changes(" + rows + ", " + 1 + ")'>" +
		"<input type='button' value='Cancel' id='addper' onclick='period_cancel(" + rows + ")'>" +
		"<input type='submit' value='Log out' id='logout'>";

	$("#insert").html(period);
	$("#buttons").html(buttons);
}

function period_cancel(rows)
{
	disable(1);

	var buttons = "<input type='button' value='Update' id='update' onclick='commit_changes(" + rows + ")'>" +
		"<input type='button' value='Remove selected' id='remove' onclick='remove_selected(" + rows + ")'>" +
		"<input type='button' value='Add period' id='addper' onclick='period_add(" + rows + ")'>" +
		"<input type='button' value='Add action' id='addact' onclick='action_add(" + rows + ")'>" +
		"<input type='submit' value='Log out' id='logout'>";

	$("#insert").html("");
	$("#buttons").html(buttons);
}

/***************************************************************************************************
* Auxiliary functions																			   *
***************************************************************************************************/

// Get the current time, format for use in editor.
// Called twice when creating period; second call increases seconds by one.
function get_time(act)
{
	var time = new Date();

	var year = time.getFullYear();
	var month = ("0" + (time.getMonth() + 1)).slice(-2);
	var day = ("0" + time.getDate()).slice(-2);
	var hours = ("0" + time.getHours()).slice(-2);
	var minutes = ("0" + time.getMinutes()).slice(-2);
	var seconds = ("0" + (time.getSeconds() + act)).slice(-2);

	// If adding a period would result in seconds being 59 and 60, instead make them 58 and 59.
	// Affects adding of a single action too, but whatever.
	seconds = act == 0 && seconds == 59 ? 58 : seconds;
	seconds = act == 1 && seconds == 60 ? 59 : seconds;

	var datetime = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;

	return datetime;
}

// Enable or disable buttons
// Called by functions posting with AJAX; done to ensure user cannot post twice in the event of latency
function disable(val)
{
	if($("#update").length) document.getElementById("update").disabled = val;
	if($("#remove").length) document.getElementById("remove").disabled = val;
	if($("#addper").length) document.getElementById("addper").disabled = val;
	if($("#addact").length) document.getElementById("addact").disabled = val;
	if($("#logout").length) document.getElementById("logout").disabled = val;
}
