$(document).ready(function(){

	var stage = 0, anudregex = /^[a-zA-Z0-9-_]+$/, anregex = /^[a-zA-Z0-9]+$/, pswregex = /^(?=.*[\W|_])(?=.*[0-9])(?=.*[a-z]).{7,40}/, stage0Err="", dbname="", dbusn="", dbpsw="", dbpre="", adminusn="", adminpsw="", adminpswc="", siteurl="", baseurl="", stage2Err="";

	$('#first-setup').on('click','#db-check',function(){
		if(stage == 0){
			stage0();
		} else {
			alert("Skipping parts of the installation will cause errors. Please stop trying!");
		}
	});

	$('#first-setup').on('click','#db-start',function(){
		if(stage == 1){
			stage1();
		} else {
			alert("Skipping parts of the installation will cause errors. Please stop trying!");
		}
	});

	$('#second-setup').on('click','#ad-check',function(){
		if(stage == 2){
			stage2();
		} else {
			alert("Skipping parts of the installation will cause errors. Please stop trying!");
		}
	});

	$('#second-setup').on('click','#ad-start',function(){
		if(stage == 3){
			stage3();
		} else {
			alert("Skipping parts of the installation will cause errors. Please stop trying!");
		}
	});

	$('#finish-setup').click(function(){
		window.location.href="index";
	});

	function stage0(){
		stage0Err = "";
		dbname = $('#db-name').val();
		dbusn = $('#db-username').val();
		dbpsw = $('#db-password').val();
		dbpre = $('#db-prefix').val();
		if(dbname == ""){
			stage0Err += "<p>Please enter a database name.</p>";
		} else {
			if(dbname.search(anudregex) == -1){
				stage0Err += "<p>Please only use alphanumeric, dashes or underscores for the database name.</p>";
			}
		}
		if(dbusn == ""){
			stage0Err += "<p>Please enter a database username.</p>";
		} else {
			if(dbusn.search(anudregex) == -1){
				stage0Err += "<p>Please only use alphanumeric, dashes or underscores for the database username.</p>";
			}
		}
		if(dbpsw == ""){
			stage0Err += "<p>Please enter a database password.</p>";
		} else {
			if(dbpsw.search(anudregex) == -1){
				stage0Err += "<p>Please only use alphanumeric, dashes or underscores for the database password.</p>";
			}
		}
		if(dbpre == ""){
			stage0Err += "<p>Please enter a database prefix.</p>";
		} else {
			if(dbpre.search(anudregex) == -1){
				stage0Err += "<p>Please only use alphanumeric, dashes or underscores for the database prefix.</p>";
			}
		}
		if(stage0Err == ""){
			$('#db-name,#db-username,#db-password,#db-prefix,#db-check').attr('disabled',true);
			mmNotification("Working","<p>Checking your database settings.</p>",true,false);
			checkString = 'stage=' + stage + '&dbname=' + dbname + '&dbusn=' + dbusn + '&dbpsw=' + dbpsw;
			$.ajax({
				url: 'initial/setup.php',
				data:checkString,
				success: function(data){
					dataRes = data.charAt(0);
					if(dataRes == 0){
						dataText = "<p>"+data.substring(1)+"</p>";
						mmNotification("Success",dataText,false,true);
						$('#db-check').attr('disabled',false);
						$('#db-check').val('Continue');
						$('#db-check').attr('id','db-start');
						stage++;
					} else {
						dataText = "<p>"+data.substring(1)+"</p>";
						$('#db-name,#db-username,#db-password,#db-prefix,#db-check').attr('disabled',false);
						mmNotification("Error",dataText,false,true);
					}
				},
				error: function(data){
					stage0Err = "<p>There was an error processing your data or the request timed out. Please try again.</p>";
					$('#db-name,#db-username,#db-password,#db-prefix,#db-check').attr('disabled',false);
					mmNotification("Error",stage0Err,false,true);
				}
			});
		} else {
			mmNotification("Error",stage0Err,false,true);
		}
	}

	function stage1(){
		mmNotification("Working","<p>Building your database and tables.</p><p>Please be patient, this could take a short while depending on your server.</p>",true,false);
		buildString = 'stage=' + stage + '&dbname=' + dbname + '&dbusn=' + dbusn + '&dbpsw=' + dbpsw + '&dbpre=' + dbpre;
		$('#db-start').attr('disabled',true);
		$.ajax({
			url: 'initial/setup.php',
			data:buildString,
			success: function(data){
				dataRes = data.charAt(0);
				dataText = "<p>"+data.substring(1)+"</p>";
				if(dataRes == 0){
					mmNotification("Success",dataText,false,true);
					mmNClose();
					$('#first-setup').removeClass('open');
					$('#second-setup').addClass('open');
					stage++;
				} else {
					$('#db-name,#db-username,#db-password,#db-prefix,#db-start').attr('disabled',false);
					$('#db-start').val('Check Details');
					$('#db-start').attr('id','db-check');
					mmNotification("Error",dataText,false,true);
					stage--;
				}
			},
			error: function(data){
				stage0Err = "<p>There was an error processing your data or the request timed out. Please try again.</p>";
				$('#db-start').attr('disabled',false);
				mmNotification("Error",stage0Err,false,true);
			}
		});
	}

	function stage2(){
		stage2Err="";
		adminusn = $('#admin-name').val();
		adminpsw = $('#admin-password').val();
		adminpswc = $('#admin-password2').val();
		siteurl = $('#server-url').val();
		baseurl = $('#server-path').val();
		if(adminusn == ""){
			stage2Err += "<p>Please enter a username.</p>";
		} else {
			if(adminusn.length < 4){
				stage2Err += "<p>Please enter a username longer or equal to 4 characters.</p>";
			} else {
				if(adminusn.search(anregex) == -1){
					stage2Err += "<p>Please only use alphanumeric characters for your username.</p>";
				}
			}
		}
		if(adminpsw == ""){
			stage2Err += "<p>Please enter a password.</p>";
		} else {
			if(adminpsw.length < 7){
				stage2Err += "<p>Please enter a password longer or equal to 7 characters.</p>";
			} else {
				if(pswregex.test(adminpsw) == false){
					stage2Err += "<p>Please make sure to include at least one letter, number and non-alphanumeric character in your password.</p>";
				}
			}
		}
		if(adminpsw != adminpswc){
			stage2Err += "<p>Please make sure your passwords match.</p>";
		}
		if(stage2Err == ""){
			$('#admin-name,#admin-password,#admin-password2,#server-url,#server-path').attr('disabled',true);
			mmNotification("Success","<p>Perfect! Your administrator account details are correct.</p><p>Please press Continue to create your account.</p>",false,true);
			$('#ad-check').val('Continue');
			$('#ad-check').attr('id','ad-start');
			stage++;
		} else {
			mmNotification("Error",stage2Err,false,true);
		}
	}

	function stage3(){
		userString = 'stage=' + stage + '&adminusn=' + adminusn + '&adminpsw=' + adminpsw + '&adminpsw2=' + adminpswc + '&surl=' + siteurl + '&burl=' + baseurl;
		mmNotification("Working","<p>Creating your administrator account.</p>",true,false);
		$('#ad-start').attr('disabled',true);
		$.ajax({
			url: 'initial/setup.php',
			data:userString,
			success: function(data){
				dataRes = data.charAt(0);
				dataText = "<p>"+data.substring(1)+"</p>";
				if(dataRes == 0){
					mmNotification("Success",dataText,false,true);
					mmNClose();
					$('#second-setup').removeClass('open');
					$('#third-setup').addClass('open');
					stage++;
				} else {
					$('#admin-name,#admin-password,#admin-password2').attr('disabled',false);
					$('#ad-start').val('Check Details');
					$('#ad-start').attr('id','ad-check');
					mmNotification("Error",dataText,false,true);
					stage--;
				}
			},
			error: function(data){
				$('#admin-name,#admin-password,#admin-password2,#server-url,#server-path').attr('disabled',false);
				stage2Err = "<p>There was an error processing your data or the request timed out. Please try again.</p>";
				$('#ad-start').val('Check Details');
				$('#ad-start').attr('id','ad-check');
				stage--;
				mmNotification("Error",stage2Err,false,true);
			}
		});
	}

});