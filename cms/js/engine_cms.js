$(document).ready(function(){
	
	var window_height, window_width;
	var scrollTop = $(window).scrollTop();
	rozmiar();
	
	/* boxy edycji */
	$('.submit, .submit_no_css').click(function(){
		var $this = $(this);
		$cel = $($this.attr('href'));
		var mydata = $this.data();
		for (var key in mydata) {
			var obj = mydata[key];
			$cel.find('input.cel_'+key).attr('value', obj);
			$cel.find('input[type=checkbox].cel_'+key).attr('checked', obj);
			$cel.find('a.cel_'+key).attr('href', obj);
			$cel.find('span.cel_'+key).text(obj);
			$cel.find('select.cel_'+key).val(obj);
			$cel.find('textarea.cel_'+key).html($this.parents('tr').find('.'+key).html());
		}
		$inside_page_box = $cel.find('.inside_page_box');
		$cel.fadeIn().find('input[type=text], input[type=email], input[type=number], textarea').first().focus();
		if($inside_page_box.height()<window_height){
			$inside_page_box.css({'margin-top':-$inside_page_box.height()/2});
		}else{
			$inside_page_box.css({'top':0});
		}
		if($inside_page_box.width()<window_width){
			$inside_page_box.css({'margin-left':-$inside_page_box.width()/2});
		}else{
			$inside_page_box.css({'left':0});
		}
		return false;
	})
	
	$('.nie').click(function(){
		$('.page_box').fadeOut();
		$('.red').html('');
		return false;
	})
	/* koniec: boxy edycji */
	
	$(window).resize(function() {
		rozmiar();
	})
	$(window).scroll(function(){
		scrollTop = $(window).scrollTop();
	})

	/* menu responsywne */
	function rozmiar(){
		window_height = $(window).height();
		window_width = $(window).width();
		function wysokosc_podmenu($this){
			$this.css({'width':'auto'});
			if($this.offset().top-scrollTop<0){
				$this.addClass('fixed').css({'top':5,'bottom':5});
				ul_width = $this.width();
				var i = 2;
				while($this.find('li').last().offset().top-scrollTop>window_height){
					$this.css({'width':ul_width*i}).find('li').addClass('inline').css({'width':ul_width}); 
					i++;
				}
			}
		}
		if(window_width>750){
			var $menu = $('#menu_ul');
			if(($menu.offset().top-scrollTop+$menu.height()>window_height-10) || $('.slimScroll #menu_ul').length>0){
				nowa_wysokosc = window_height+scrollTop-$menu.offset().top;
				$('#menu_ul').slimScroll({
					height: nowa_wysokosc-10, color: '#03d09d',	wheelStep: '1',	size: 5, distance: 2,disableFadeOut : true
				});
				$('.slimScrollDiv').css('overflow','visible');
				$('#menu li li').removeClass('inline-block');
				$('#menu li').mouseenter(function(){
					if(window_width>750){
						$this = $(this);
						$ul = $this.find('ul');
						if($ul.length>0){
							var ul_height = $ul.height();
							var top_menu_height = parseInt($('#top_menu').height());
							var top = $this.offset().top-scrollTop-top_menu_height-20;
							if(top+ul_height>nowa_wysokosc){
								$ul.css({'top':window_height-ul_height-top_menu_height-20,'left':200});
							}else{
								$ul.css({'top':top,'left':200});
							}
							wysokosc_podmenu($ul);
						}
					}
				}) 
			}else{
				$('#menu li').addClass('relative');
				$('#menu li ul').each(function(){
					$this = $(this);
					var top = $this.offset().top-scrollTop+parseInt($this.height());
					if(top>window_height-5){
						$this.css('top',-(top-window_height+5));
					}
					wysokosc_podmenu($this);
				})
			}
		}else{
			$('#menu li').addClass('relative');
			$('#menu li ul').removeClass('fixed').each(function(){
				$li = $(this).find('li');
				$li.removeClass('inline').css({'width':'auto'}); 
				if($li.length>20){
					$li.parent().css({'left':-parseInt($li.parent().parent().offset().left-25),'width':window_width-50,'top':33,'bottom':'auto'});
				}else if(parseInt($li.offset().left)+parseInt($li.width())>$('footer').width()){
					$li.parent().css({'left':-(parseInt($li.offset().left)+parseInt($li.width())-$('footer').width()),'top':33,'bottom':'auto'});
				}else{
					$li.parent().css({'left':0,'top':33,'bottom':'auto'});
				}
			})
		}
	}	
	/* koniec: menu responsywne */
	
	$(".aktywuj_obrazek").click(function(){
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'aktywuj_obrazek',
			'id' : $(this).data('id'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    }); 
	
	$('.link_strony.nieaktywny, .ikona.nieaktywna').click(function(){
		return false;
	})
	
	$(".usun_tag").click(function(){
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'usun_tag',
			'id' : $(this).data('id'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    });
	
	$(".aktywuj_uzytkownika").click(function(){
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'aktywuj_uzytkownika',
			'id' : $(this).data('id'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    }); 
	
	$('#pobierz_url').click(function(){
		$('input[name=base_url]').val(window.location.origin+'/');
		return false;
	})
	
	$(".usun_komentarz").click(function(){
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'usun_komentarz',
			'id' : $(this).data('id'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    });
	
	$(".ustaw_moderator").click(function(){
		$this = $(this);
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'ustaw_moderator',
			'id' : $this.data('id'),
			'moderator' : $this.data('moderator'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    });
	
	$(".boks_pozycja").click(function(){
		$this = $(this);
		$.post('php/funkcje_ajax.php', {
			'akcja' : 'boks_pozycja',
			'id' : $this.data('id'),
			'pozycja' : $this.data('pozycja'),
			'dzialanie' : $this.data('dzialanie'),
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    });
	
	function select_boks($element){
		var this_value = $element.find('.boks_rodzaj').val();
		if(this_value == 'tekst'){
			$element.find('.boks_tresc').css('display','block');
			$element.find('.boks_ilosc').css('display','none');
		}else if(this_value == 'top' || this_value == 'komentarze' || this_value == 'nowe' ){
			$element.find('.boks_tresc').css('display','none');
			$element.find('.boks_ilosc').css('display','block');
		}else{
			$element.find('.boks_tresc').css('display','none');
			$element.find('.boks_ilosc').css('display','none');
		}
	}
	$('.boks_rodzaj').change(function(){
		select_boks($(this).parents('.page_box'));
	});
	$(".boks_edytuj").click(function(){
		select_boks($('#edytuj_boks'));
	})
	
	$('input[name=wlacz_obrazki_inne_strony]').change(function(){
		if(this.checked){
			$('.inne_obrazki').attr('required',true);
		}else{
			$('.inne_obrazki').attr('required',false);
		}
	});
	
	$('#box_logo form').submit(function(){
		$('h5').slideUp();
		blad = false;
		$('#box_logo input[type=file]').each(function(){
			$this = $(this);
			var fileName = $this.val();
			if (fileName != "" && fileName.split(".")[1].toUpperCase() != $this.data('typ')){
				$this.next().slideDown();
				blad = true;
			}
		})
		if(blad){
			return false;
		}
    })
	
	$.datepicker.regional['pl'] = {
        closeText: 'Zamknij',
        prevText: '&#x3c;Poprzedni',
        nextText: 'Następny&#x3e;',
        currentText: 'Dziś',
        monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
        'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
        'Lip','Sie','Wrz','Pa','Lis','Gru'],
        dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
        dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
        weekHeader: 'Tydz',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['pl']);
	$('input[type=date]').datepicker();
})

$(window).load(function(){
	if($('#strona').height()>$(window).height()){
		$('footer').css({'position':'relative'});
	}
})

$(document).keyup(function(e) { 
    if (e.which == 27) { $('.nie').click();}
});