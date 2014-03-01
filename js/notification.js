function mmNotification(title,data,isloading,closeable){
	$('#mm-n-title').html(title);
	$('#mm-n-text').html(data);
	if(isloading == true){
		if($('#mm-n-loading-rs').hasClass('open')){} else {
			$('#mm-n-loading-rs').addClass('open');
			$('#mm-n-rs').addClass('load');
		}
	} else {
		$('#mm-n-loading-rs').removeClass('open');
		$('#mm-n-rs').removeClass('load');
	}
	if(closeable == true){
		if($('#mm-n-close').hasClass('open')){} else {
			$('#mm-n-close').addClass('open');
		}
	} else {
		$('#mm-n-close').removeClass('open');
	}
	if($('#mm-notification').hasClass('open')){} else {
		$('#mm-notification').addClass('open');
	}
}

function mmNClose(){
	$('#mm-notification').removeClass('open');
}

$(document).ready(function(){
	$('#mm-n-close').click(function(){
		$('#mm-notification').removeClass('open');
	});
});

$.ajaxSetup({
	type:'POST',
	timeout:30000
});