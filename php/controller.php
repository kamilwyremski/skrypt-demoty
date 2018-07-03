<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$menu = false;

$title = $ustawienia['tytul'];
$keywords = $ustawienia['keywords'];
$description = $ustawienia['description'];

if(isset($_GET['akcja'])){
	
	$akcja = array_search($_GET['akcja'], $tlumaczenia_linki);
	if($akcja==''){
		if($_GET['akcja']=='obrazek'){
			$akcja='obrazek';
		}elseif($_GET['akcja']=='activate'){
			$akcja='aktywacja';
		}else{
			$akcja='404';
		}
	}
	if($akcja=='logowanie'){
		$title = $tlumaczenia_teksty['logowanie'].' - '.$title;
		$strona = 'logowanie';
		include('model/logowanie.php');
	}elseif($akcja=='aktywacja'){
		$title = $tlumaczenia_teksty['logowanie'].' - '.$title;
		$strona = 'logowanie';
		include('model/logowanie.php');
	}elseif($akcja=='rejestracja'){
		$title = $tlumaczenia_teksty['rejestracja'].' - '.$title;
		$strona = 'rejestracja';
		include('model/rejestracja.php');
	}elseif($akcja=='dodaj'){
		$title = $tlumaczenia_teksty['dodaj_nowy'].' - '.$title;
		$menu = true;
		$strona = 'dodaj';
		include('model/dodaj.php');
	}elseif($akcja=='stworz' and $ustawienia['tworzenie']==1){
		$title = $tlumaczenia_teksty['stworz'].' - '.$title;
		$menu = true;
		$strona = 'stworz';
		include('model/stworz.php');
	}elseif($akcja=='konto'){
		$title = $tlumaczenia_teksty['konto'].' - '.$title;
		$menu = true;
		$strona = 'konto';
		include('model/konto.php');
	}elseif($akcja=='edycja'){
		$title = $tlumaczenia_teksty['edytuj'].' - '.$title;
		$menu = true;
		$strona = 'edycja';
		include('model/edycja.php');
	}elseif($akcja=='obrazek' and isset($_GET['id'])){
		$menu = true;
		$strona = 'obrazek';
		include('model/obrazek.php');	
	}elseif($akcja=='onas'){
		$title = $tlumaczenia_teksty['onas'].' - '.$title;
		$menu = true;
		$strona = 'onas';
		include('model/onas.php');
	}elseif($akcja=='regulamin'){
		$title = $tlumaczenia_teksty['regulamin'].' - '.$title;
		$menu = true;
		$strona = 'regulamin';
		include('model/regulamin.php');
	}elseif($akcja=='polityka_prywatnosci'){
		$title = $tlumaczenia_teksty['polityka_prywatnosci'].' - '.$title;
		$menu = true;
		$strona = 'polityka_prywatnosci';
		include('model/polityka_prywatnosci.php');
	}elseif($akcja=='profil' and isset($_GET['id'])){
		$menu = true;
		$strona = 'profil';
		include('model/profil.php');		
	}elseif($akcja=='reset_hasla'){
		$title = $tlumaczenia_teksty['reset_hasla'].' - '.$title;
		$strona = 'reset_hasla';
		include('model/reset_hasla.php');
	}elseif($akcja=='mapa' and $ustawienia['mapa']==1){
		$title = $tlumaczenia_teksty['mapa_obiektow'].' - '.$title;
		$menu = true;
		$strona = 'mapa';
		include('model/mapa.php');
	}elseif($akcja=='mapa_uzytkownikow' and $ustawienia['mapa_uzytkownikow']==1){
		$title = $tlumaczenia_teksty['mapa_uzytkownikow'].' - '.$title;
		$menu = true;
		$strona = 'mapa_uzytkownikow';
		include('model/mapa_uzytkownikow.php');
	}elseif($akcja=='uzytkownicy'){
		$title = $tlumaczenia_teksty['uzytkownicy'].' - '.$title;
		$menu = true;
		$strona = 'uzytkownicy';
		include('model/uzytkownicy.php');
	}elseif($akcja=='stworzone' and $ustawienia['tworzenie']==1){
		$title = $tlumaczenia_teksty['stworzone'].' - '.$title;
		$menu = true;
		$strona = 'stworzone';
		include('model/stworzone.php');
	}elseif($akcja=='konkursy' and $ustawienia['konkursy']==1){
		$title = $tlumaczenia_teksty['konkursy'].' - '.$title;
		$menu = true;
		$strona = 'konkursy';
		include('model/konkursy.php');
	}elseif($akcja=='konkurs' and $ustawienia['konkursy']==1 and isset($_GET['id'])){
		$title = $tlumaczenia_teksty['konkurs'].' - '.$title;
		$menu = true;
		$strona = 'konkurs';
		include('model/konkurs.php');
	}elseif($akcja=='404'){
		$title = 'Error 404 - '.$title;
		$menu = true;
		$strona = '404';
		include('model/404.php');
	}else{
		$menu = true;
		$strona = 'index';
		include('model/index.php');
	}
}else{
	$menu = true;
	$strona = 'index';
	include('model/index.php');
}


