document.getElementById('update').disabled = true;
document.getElementById('addper').disabled = true;
document.getElementById('addact').disabled = true;
document.getElementById('remove').disabled = true;

$('#employees').change(function() {
	var employee = $('#employees').val()

	$.ajax({
		type: 'POST',
		url: '../../view/admin/redraw.php',
		data: { 'employee' : employee }
	})
	.done(function(result) {
		$('#content').html(result);
		
		if(!$('#employees').val())
		{
			document.getElementById('update').disabled = true;
			document.getElementById('addper').disabled = true;
			document.getElementById('addact').disabled = true;
			document.getElementById('remove').disabled = true;
		}
	});
});
