$(document).ready(function(){
	
	$('#ts-logo').addClass('open');

	$('#user-splash').submit(function(){
		mmUsn = $('#username').val();
		mmPsw = $('#password').val();
		errText = "";
		if(mmUsn == ""){
			errText += "<p>Please enter a username.</p>";
		}
		if(mmPsw == ""){
			errText += "<p>Please enter a password.</p>";
		}
		if(errText == ""){
			$('#username,#password,#submit').attr('disabled',true);
			userString = 'usn=' + mmUsn + '&psw=' + mmPsw;
			mmNotification("Working","<p>Logging you in to MappMe</p>",true,false);
			$.ajax({
				url: 'load/login.php',
				data:userString,
				success: function(data){
					dataRes = data.charAt(0);
					dataText = "<p>"+data.substring(1)+"</p>";
					if(dataRes == 0){
						window.location.href = data.substring(1);
					} else {
						$('#username,#password,#submit').attr('disabled',false);
						mmNotification("Error",dataText,false,true);
					}
				},
				error: function(data){
					$('#username,#password,#submit').attr('disabled',false);
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
				}
			});
		} else {
			mmNotification("Error",errText,false,true);
		}
		return false;
	});

});