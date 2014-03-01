isWorking = 0;
mmDLoad = 0;

$(document).ready(function(){

	var newCode,pwReg = /^(?=.*[\W|_])(?=.*[0-9])(?=.*[a-z]).{7,40}/;

	$('#mm-d-close').click(function(){
		mmDClose();
	});
	
	$('#set-home').click(function(){
		if(isWorking == 0){
			isWorking = 1;
			center = MappMe.getCenter();
			zoom = MappMe.getZoom();
			homeLat = center.lat();
			homeLong = center.lng();
			homeString = 'mmlat=' + homeLat + '&mmlong=' + homeLong + '&zoom=' + zoom;
			$('#set-home').attr('disabled',true);
			mmNotification("Working","<p>Saving home position.</p>",false,true);
			$.ajax({
				url: 'load/map/home.set.php',
				data:homeString,
				success: function(data){
					dataRes = data.charAt(0);
					dataText = "<p>"+data.substring(1)+"</p>";
					if(dataRes == 0){
						$('#set-home').attr('disabled',false);
						mmNotification("Success",dataText,false,true);
						$('#cur-home').html('<span>Current Home</span> '+homeLat+', '+homeLong+' (Zoom: '+zoom+')');
					} else {
						$('#set-home').attr('disabled',false);
						mmNotification("Error",dataText,false,true);
					}
					isWorking = 0;
				},
				error: function(data){
					$('#set-home').attr('disabled',false);
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking = 0;
				}
			});
		}
	});

	$('#new-code').click(function(){
		if(isWorking == 0){
			isWorking = 1;
			mmNotification('Loading','<p>Generating your new access code.</p>',true,false);
			$.ajax({
				url: 'load/map/accesscode.load.php',
				success: function(data){
					mmNClose();
					mmDialogue('New Access Code',data);
					getCode();
					isWorking =0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking=0;
				}
			});
		}
	});

	function getCode(){
		newCode = $('#new-gen-code').text();
	}

	$('#mm-d-content').on('click','#me-set-code',function(){
		if(isWorking == 0){
			isWorking=1;
			$('#me-set-code').attr('disabled',true);
			codeString = 'ac='+newCode+'';
			mmDClose();
			mmNotification("Working","<p>Saving your access code.</p>",true,false);
			$.ajax({
				url: 'load/map/accesscode.set.php',
				data:codeString,
				success: function(data){
					dataRes = data.charAt(0);
					dataText = "<p>"+data.substring(1)+"</p>";
					if(dataRes == 0){
						mmNotification("Success","<p>Your access code has been updated.</p>",false,true);
					} else {
						mmNotification("Error",dataText,false,true);
					}
					isWorking=0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking=0;
				}
			});
		}
	});

	$('#change-pw').click(function(){
		if(isWorking == 0){
			isWorking = 1;
			mmNotification('Loading','<p>Loading your password settings.</p>',true,false);
			$.ajax({
				url: 'load/map/password.load.php',
				success: function(data){
					mmNClose();
					mmDialogue('Change My Password',data);
					isWorking = 0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking = 0;
				}
			});
		}
	});

	$('#mm-d-content').on('click','#set-new-pass',function(){
		if(isWorking == 0){
			isWorking = 1;
			oldPsw = $('#cur-passw').val();
			newPsw = $('#new-passw').val();
			newPsw2 = $('#new-passw2').val();
			passErr = "";
			if(oldPsw == ""){
				passErr += "<p>Please enter your current password.</p>";
			}
			if(newPsw == ""){
				passErr += "<p>Please enter your new password.</p>";
			} else {
				if(pwReg.test(newPsw) == false){
					passErr += "<p>Please make sure your password meets the requirements.</p>";
				}
			}
			if(newPsw != newPsw2){
				passErr += "<p>Your new passwords do not match.</p>";
			}
			if(passErr == ""){
				pswString = 'ol='+oldPsw+'&npsw='+newPsw+'&npsw2='+newPsw2;
				$.ajax({
					url: 'load/map/password.set.php',
					data:pswString,
					success: function(data){
						dataRes = data.charAt(0);
						dataText = "<p>"+data.substring(1)+"</p>";
						if(dataRes == 0){
							mmDClose();
							mmNotification("Success",dataText,false,true);
						} else {
							mmNotification("Error",dataText,false,true);
						}
						isWorking = 0;
					},
					error: function(data){
						mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
						isWorking = 0;
					}
				});
			} else {
				mmNotification("Error",passErr,false,true);
				isWorking = 0;
			}
		}
	});

	$('#me-settings').click(function(){
		if(isWorking == 0){
			isWorking = 1;
			mmNotification('Loading','<p>Loading your settings.</p>',true,false);
			$.ajax({
				url: 'load/map/settings.load.php',
				success: function(data){
					mmNClose();
					mmDialogue('My Settings',data);
					isWorking = 0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking = 0;
				}
			});
		}
	});

	$('#mm-d-content').on('click','#me-set-save',function(){
		if(isWorking == 0){
			isWorking = 1;
			polyl = $('#s-polyl').is(':checked');
			nocom = $('#s-n-com').is(':checked');
			notime = $('#s-n-time').is(':checked');
			nolat = $('#s-n-lat').is(':checked');
			nolong = $('#s-n-long').is(':checked');
			timeLimit = $('#t-limit').val();
			markLimit = $('#n-limit').val();
			settingString = 'pll=' + polyl + '&tl=' + timeLimit + '&ml=' + markLimit + '&nc=' + nocom + '&nt=' + notime + '&nlo=' + nolong + '&nla=' + nolat;
			$.ajax({
				url: 'load/map/settings.set.php',
				data:settingString,
				success: function(data){
					dataRes = data.charAt(0);
					dataText = "<p>"+data.substring(1)+"</p>";
					if(dataRes == 0){
						window.location.reload();
					} else {
						mmNotification("Error",dataText,false,true);
					}
					isWorking = 0;
				},
				error: function(data){
					mmNotification("Error","<p>There was an error with the server or your request timed out.</p><p>Please try again.</p>",false,true);
					isWorking = 0;
				}
			});
		}
	});

});

function mmDialogue(title,data){
	$('#mm-d-title').html(title);
	$('#mm-d-content').html(data);
	if($('#mm-d-bg').hasClass('open')){} else {
		$('#mm-d-bg').addClass('open');
		$('#mm-d-bg').addClass('left');
	}	
}

function mmDClose(){
	$('#mm-d-bg').removeClass('open').delay(300).queue(function(){
		$('#mm-d-bg').removeClass('left');
		$('#mm-d-title').empty();
		$('#mm-d-content').empty();
		$(this).dequeue();
	});
}