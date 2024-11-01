jQuery(document).ready( function($) {
	var loc;
	$("input#locsearch").click( function() {
		$.ajax({
			url: "admin-ajax.php",
			type:"GET",
			data:"action=get_location&location="+loc,
			success: function(data){
				$("select#select-weather").append(data);
			},
			dataType:"html"
		});
	});
	
	$("input#fweather").keyup( function() {
		loc = $(this).val();
	});
});


function searchLocation() {
		alert('click');
		var loc = jQuery('input#find-weather').val();
		alert(loc);
		jQuery.ajax({
			url: "admin-ajax.php",
			type:"GET",
			data:"action=get_location&location="+loc,
			success: function(data){
				jQuery("#select-weather").append(data);
			},
			dataType:"html"
		});
}
	
