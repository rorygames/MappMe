$(document).ready(function(){

	var alphanumReg = /^[a-zA-Z0-9-_]+$/;
	
	$('#new-user').click(function(){
		if(isWorking == 0){
			isWorking = 1;
			mmNotification('Loading','<p>Loading new user settings.</p>',true,false);
			$.ajax({
				url: 'load/map/user.load.php',
				success: function(data){
					mmNClose();
					mmDialogue('Create a New User',data);
					isWorking = 0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking = 0;
				}
			});
		}
	});

	$('#mm-dialogue').on('click','#n-u-sub',function(){
		if(isWorking == 0){
			isWorking = 1;
			newUsn = $('#n-u-usn').val();
			newPsw = $('#n-u-psw').val();
			newError = "";
			if(newUsn == ""){
				newError+="<p>Please enter a username.</p>";
			} else if(newUsn.search(alphanumReg) == -1){
				newError+="<p>Please use alphanumeric, dash or underscore characters for the username.</p>";
			}
			if(newPsw == ""){
				newError+="<p>Please enter a password.</p>";
			} else if(newPsw.search(alphanumReg) == -1){
				newError+="<p>Please use alphanumeric, dash or underscore characters for the password.</p>";
			}
			if(newError == ""){
				mmNotification("Working","<p>Adding user "+newUsn+" to MappMe.</p>",true,false);
				$('#n-u-usn,#n-u-psw,#n-u-sub').attr('disabled',true);
				newUString = 'usn=' + newUsn + '&psw=' + newPsw;
				$.ajax({
					url: 'load/map/user.add.php',
					data:newUString,
					success:function(data){
						dataRes = data.charAt(0);
						if(dataRes == 0){
							dataText = "<p>"+data.substring(1)+"</p>";
							mmNotification("Success",dataText,false,true);
							$('#n-u-usn,#n-u-psw,#n-u-sub').attr('disabled',false);
							mmDClose();
						} else {
							dataText = "<p>"+data.substring(1)+"</p>";
							$('#n-u-usn,#n-u-psw,#n-u-sub').attr('disabled',false);
							mmNotification("Error",dataText,false,true);
						}
						isWorking = 0;
					},
					error:function(data){
						dataText = "<p>There was an error processing your data or the request timed out. Please try again.</p>";
						$('#n-u-usn,#n-u-psw,#n-u-sub').attr('disabled',false);
						mmNotification("Error",dataText,false,true);
						isWorking = 0;
					}
				});
			} else {
				mmNotification("Error",newError,false,true);
				isWorking = 0;
			}
		}		
	});

});