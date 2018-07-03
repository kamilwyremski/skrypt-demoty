
$(document).ready(function(){
	if($('input[name=url]').attr('value')==''){
		$('input[name=url]').attr('value',window.location.origin+'/');
	}
	if($('input[name=port]').attr('value')==''){
		$('input[name=port]').attr('value','3306');
	}
	if($('input[name=logincms]').attr('value')==''){
		$('input[name=logincms]').attr('value','administrator');
	}
	$("form").submit( function () {   
		$('.red').css({'display':'none'});
		if($('input[name=haslocms]').val()!=$('input[name=haslocms2]').val()){
			$('.red').css({'display':'block'});
			return false;
		}
    });  
});

