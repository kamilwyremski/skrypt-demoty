$(document).ready(function(){

	$('#menu_kategorie').click(function(){
		if($(window).width()<=768){
			$(this).parents('.categories').trigger("mouseover");
			return false;
		}
	})
	
	jQuery("#facebook2_2").hover(function(){jQuery(this).stop(true,false).animate({right: "0px"}, 500 );},
		function(){jQuery("#facebook2_2").stop(true,false).animate({right: "-304px"}, 500 );});
	$('.fb-like-box').attr('data-height',$('#facebook2_2').height());
	$( window ).resize(function() {
		$('.fb-like-box').attr('data-height',$('#facebook2_2').height());
	})
	
	menu_scroll_height = $(window).height()-100;
	
	if($('#menu_scroll').height()>menu_scroll_height){
		$('#menu_scroll').slimScroll({
			height: menu_scroll_height, color: 'white',	wheelStep: '1',	size: 5, distance: 2,disableFadeOut : true
		});
	}

	$('#ul_wybor_obrazka a').click(function(){
		$('#ul_wybor_obrazka a.aktywny').removeClass('aktywny');
		$(this).addClass('aktywny');
		$('.div_wybor_obrazka').fadeOut(200).find('input').prop('required',false);
		href = $(this).attr('href');
		$(href).delay(200).fadeIn(200).find('input').prop('required',true);
		$('#input_wybor_obrazka').val(href.substring(1,href.length));
		return false;
	})
	input_value = $('#input_wybor_obrazka').val();
	if(input_value!=''){
		$('#ul_wybor_obrazka a[href=#'+input_value+']').click();
		if(input_value=='stworzony'){
			$('#ul_wybor_obrazka').css('display','none');
		}
	}else{
		$('#ul_wybor_obrazka a:first').click();
	}
	
	$('.glos').click(function(){
		$this = $(this);
		$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
			'akcja' : 'glos',
			'glos' : $this.data('glos'),
			'id' : $this.data('id'),
			'send': 'ok'}, 
			function(data) {
				$('.glos.aktywny').removeClass('aktywny');
				$this.addClass('aktywny');
				$this.parent().find('.glos_p').html(data);
		});
		return false;
	})
	
	$('.komentarz_glos').click(function(){
		$this = $(this);
		if($this.hasClass('niezalogowany')){
			alert(tlumaczenia_teksty['komentarz_niezalogowany']);
		}else{
			if($this.data('glos')=='1'){
				glos = 1;
			}else{
				glos = -1;
			}
			$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
				'akcja' : 'komentarz_glos',
				'glos' : glos,
				'id' : $this.data('id'),
				'send': 'ok'}, 
				function(data) {
					$this.parents('.komentarz_glosy').find('.komentarz_licznik_glosow').html(data);
			});
		}
		return false;
	})
	
	$('.link_stron.nieaktywny').click(function(){
		return false;
	})
	
	$('.konto_usun_obrazek').click(function(){
		var is_confirmed = confirm(tlumaczenia_teksty['na_pewno_usunac']);
		if (is_confirmed) {
			$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
				'akcja' : 'usun_obrazek',
				'id' : $(this).data('id'),
				'send': 'ok'},function(data){
					if(data){
						window.location.href = window.location;
					}
				}
			);
		}
		return false;
	})	
	
	$('.obrazek_usun_obrazek').click(function(){
		var is_confirmed = confirm(tlumaczenia_teksty['na_pewno_usunac']);
		if (is_confirmed) {
			$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
				'akcja' : 'usun_obrazek',
				'id' : $(this).data('id'),
				'send': 'ok'},function(data){
					if(data){
						window.location.href =  $('base').attr('href');
					}
				}
			);
		}
		return false;
	})	
	
	$('#pokaz_mape').change(function(){
		$box_mapa = $('#box_mapa');
		if(this.checked){
			if($box_mapa.hasClass('ukryta_mapa')){
				$box_mapa.css('display','none').removeClass('ukryta_mapa').slideDown();
			}else{
				$box_mapa.slideDown();
			}
		}else{
			$box_mapa.slideUp();
		}
	});
	if(!$('#pokaz_mape').attr('checked')) {
		$('#box_mapa').addClass('ukryta_mapa');
	};
	
	$('#pokaz_dodatkowe_informacje').change(function(){
		if(this.checked){
			$('#dodatkowe_informacje').slideDown();
		}else{
			$('#dodatkowe_informacje').slideUp();
		}
	});
	
	function pobierz_podglad(){
		if($('#form_stworz textarea[name=tytul]').val()!=''){
			var formData = new FormData(document.getElementById("form_stworz"));
			$('#podglad_laduje').css('display','block');
			$.ajax({
				url: $('base').attr('href')+'/php/funkcje_ajax.php',
				type: 'POST',
				data:  formData,
				mimeType:"multipart/form-data",
				contentType: false,
				cache: false,
				processData:false,
				success: function(data){
					if(data && data.length>1){
						d = new Date();
						$('#stworz_podglad').slideDown(700).find('img').attr('src',data+"?"+d.getTime());
					}else{
						$('#stworz_podglad').slideUp(700);
					}
					$('#podglad_laduje').hide();
				}          
			}); 
		}
	}
	$('#form_stworz textarea').blur(function(){
		pobierz_podglad();
	});
	$('#form_stworz input[type=file], #form_stworz input[type=color], #form_stworz select').change(function(){
		pobierz_podglad();
	})
	$('#form_stworz select[name=typ]').change(function(){
		typ_obrazka = $(this).val();
		if(typ_obrazka=='obrazek'){
			$('#typ_obrazek').slideDown();
			$('#form_stworz input[type=file]').attr('required', true);
		}else{
			$('#typ_obrazek').slideUp();
			$('#form_stworz input[type=file]').removeAttr('required');
		}
		if(typ_obrazka=='mem_obrazek'){
			$('#typ_mem_obrazek').slideDown();
			$('#typ_nie_mem_obrazek').slideUp();
		}else{
			$('#typ_mem_obrazek').slideUp();
			$('#typ_nie_mem_obrazek').slideDown();
		}
	})
	
	$('#typ_mem_obrazek label').click(function(){
		$('#typ_mem_obrazek label.active').removeClass('active');
		$(this).addClass('active');
		pobierz_podglad();
	})
	
	$('.usun_komentarz').click(function(){
		var $this = $(this);
		var is_confirmed = confirm(tlumaczenia_teksty['usunac_komentarz']);
		if (is_confirmed) {
			$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
				'akcja' : 'usun_komentarz',
				'id' : $(this).data('id'),
				'send': 'ok'},function(data){
					if(data){
						$this.parents('.komentarz').slideUp();
					}
				}
			);
		}
		return false;
	})	
	
	$('.na_glowna').click(function(){
		$.post($('base').attr('href')+'/php/funkcje_ajax.php', {
			'akcja' : 'na_glowna',
			'id' : $(this).data('id'),
			'send': 'ok'},function(data){
				if(data){
					window.location.href = window.location;
				}
			}
		);
		return false;
	})	
	
});

$(window).load(function(){
	if($('#kontakt').length>0 && $('#kontakt span').html()!=''){
		pozycja = $('#kontakt').offset().top+$('#kontakt').height()-$(window).height()+50;
		$('html, body').stop().animate({ scrollTop: pozycja}, 300);
	}else if($('#strona_komentarze .blad').length>0){
		pozycja = $('#strona_komentarze').offset().top+$('#strona_komentarze').height()-$(window).height()+50;
		$('html, body').stop().animate({ scrollTop: pozycja}, 300);
	}else if($('#form_dane_osobowe .ok').length>0){
		pozycja = $('#form_dane_osobowe').offset().top+$('#form_dane_osobowe').height()-$(window).height()+50;
		$('html, body').stop().animate({ scrollTop: pozycja}, 300);
	}else if($('#form_zmiana_hasla .blad, form_zmiana_hasla .ok').length>0){
		pozycja = $('#form_zmiana_hasla').offset().top+$('#form_zmiana_hasla').height()-$(window).height()+50;
		$('html, body').stop().animate({ scrollTop: pozycja}, 300);
	}
})

