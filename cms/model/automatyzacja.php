<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zapisz']) and $_POST['zapisz']=='automatyzacja'){
	if(isset($_POST['wlacz'])){$wlacz=1;}else{$wlacz=0;}
	if(isset($_POST['wlacz_wszystkie'])){$wlacz_wszystkie=1;}else{$wlacz_wszystkie=0;}
	if(isset($_POST['wlacz_dni_wiecej'])){$wlacz_dni_wiecej=1;}else{$wlacz_dni_wiecej=0;}
	if(isset($_POST['wlacz_dni_mniej'])){$wlacz_dni_mniej=1;}else{$wlacz_dni_mniej=0;}
	if(isset($_POST['wlacz_glosy'])){$wlacz_glosy=1;}else{$wlacz_glosy=0;}
	if(isset($_POST['wlacz_komentarze'])){$wlacz_komentarze=1;}else{$wlacz_komentarze=0;}
	if(isset($_POST['wlacz_obrazki_inne_strony'])){$wlacz_obrazki_inne_strony=1;}else{$wlacz_obrazki_inne_strony=0;}
	if(isset($_POST['inne_strony_cel'])){$inne_strony_cel=1;}else{$inne_strony_cel=0;}
	
	$lista_stron_wejscie = explode("\n", $_POST['lista_stron']);
	$lista_stron = '';

	foreach ($lista_stron_wejscie as $strona_automatyzacja) {
		$lista_stron .= filtruj(adres_www(preg_replace('/\s+/', '', $strona_automatyzacja)))."\n";
	}

	if(isset($_POST['inne_strony_kategoria']) and $_POST['inne_strony_kategoria']>0){
		$inne_strony_kategoria = filtruj($_POST['inne_strony_kategoria']);
	}else{
		$inne_strony_kategoria = 0;
	}
	
	mysql_query('update automatyzacja set wlacz="'.$wlacz.'", wlacz_wszystkie="'.$wlacz_wszystkie.'", wlacz_dni_wiecej="'.$wlacz_dni_wiecej.'", dni_wiecej="'.filtruj($_POST['dni_wiecej']).'", wlacz_dni_mniej="'.$wlacz_dni_mniej.'", dni_mniej="'.filtruj($_POST['dni_mniej']).'", wlacz_glosy="'.$wlacz_glosy.'", glosy="'.filtruj($_POST['glosy']).'", wlacz_komentarze="'.$wlacz_komentarze.'", komentarze="'.filtruj($_POST['komentarze']).'", wlacz_obrazki_inne_strony="'.$wlacz_obrazki_inne_strony.'", min_szerokosc="'.filtruj($_POST['min_szerokosc']).'", min_wysokosc="'.filtruj($_POST['min_wysokosc']).'", lista_stron="'.$lista_stron.'", inne_strony_cel="'.filtruj($_POST['inne_strony_cel']).'", inne_strony_uzytkownik="'.filtruj($_POST['inne_strony_uzytkownik']).'", inne_strony_kategoria="'.$inne_strony_kategoria.'", generuj_sitemap="'.isset($_POST['generuj_sitemap']).'", usun_bledne_obrazki="'.isset($_POST['usun_bledne_obrazki']).'" limit 1');
	
	if(isset($_POST['uruchom'])){
		include('../cron-daily.php');
	}
}
$q = mysql_query('select * from automatyzacja limit 1');
while($dane = mysql_fetch_array($q)){$automatyzacja=$dane;}
$smarty->assign("automatyzacja", $automatyzacja);

$q = mysql_query('select id, login from uzytkownicy where aktywny=1 order by login');
while($dane = mysql_fetch_array($q)){$automatyzacja_uzytkownicy[]=$dane;}
if (isset($automatyzacja_uzytkownicy)){	$smarty->assign("automatyzacja_uzytkownicy", $automatyzacja_uzytkownicy);}
	
?>