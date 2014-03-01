$(document).ready(function(){
	
	var code = $('#gen-code').text();

	$('#first-save').click(function(){
		mmNotification("Working","<p>Saving your details.</p>",true,false);
		$('#first-save').attr('disabled',true);
		dataString = 'ac=' + code;
		$.ajax({
			url: 'load/map/standard.first.php',
			data:dataString,
			success: function(data){
				dataRes = data.charAt(0);
				dataText = "<p>"+data.substring(1)+"</p>";
				if(dataRes == 0){
					window.location.reload();
				} else if(dataRes == 1) {
					$('#first-save').attr('disabled',true);
					mmNotification("Error",dataText,false,true);
				} else if(dataRes == 2){
					mmNotification("Success",dataText,false,false);
				}
			},
			error: function(data){
				$('#first-save').attr('disabled',false);
				mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
			}
		});
	});

});