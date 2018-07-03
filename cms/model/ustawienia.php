<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

function pobierz_szablony(){
	global $smarty;
	$path = '../views/';
	$results = scandir($path);
	$szablony = array();
	foreach ($results as $result) {
		if ($result === '.' or $result === '..') continue;
		if (is_dir($path . '/' . $result)) {
		   $szablony[] .= $result;
		}
	}
	$smarty->assign("szablony", $szablony);
}
pobierz_szablony();

if(isset($_POST['zapisz'])){
	if($_POST['zapisz']=='ustawienia' and isset($_POST['base_url']) and isset($_POST['tytul']) and isset($_POST['szablon'])){
		if(isset($_POST['mapa'])){$mapa=1;}else{$mapa=0;}
		if(isset($_POST['logo_obrazek'])){$logo_obrazek=1;}else{$logo_obrazek=0;}
		if(isset($_POST['logowanie_facebook'])){$logowanie_facebook=1;}else{$logowanie_facebook=0;}
		if(isset($_POST['wylacz_niezalogowani'])){$wylacz_niezalogowani=1;}else{$wylacz_niezalogowani=0;}
		if(isset($_POST['tworzenie'])){$tworzenie=1;}else{$tworzenie=0;}
		if(isset($_POST['dodawaj_znak_wodny'])){$dodawaj_znak_wodny=1;}else{$dodawaj_znak_wodny=0;}
		if(isset($_POST['glowna_pelne_zdjecia'])){$glowna_pelne_zdjecia=1;}else{$glowna_pelne_zdjecia=0;}
		if(isset($_POST['logo_w_ramce'])){$logo_w_ramce=1;}else{$logo_w_ramce=0;}
		if(isset($_POST['wybor_kolor_ramki'])){$wybor_kolor_ramki=1;}else{$wybor_kolor_ramki=0;}
		if(isset($_POST['memy'])){$memy=1;}else{$memy=0;}
		if(isset($_POST['domyslnie_mem'])){$domyslnie_mem=1;}else{$domyslnie_mem=0;}
		if(isset($_POST['konkursy'])){$konkursy=1;}else{$konkursy=0;}
		if(isset($_POST['udostepnij_fb'])){$udostepnij_fb=1;}else{$udostepnij_fb=0;}
		if(isset($_POST['udostepnij_twitter'])){$udostepnij_twitter=1;}else{$udostepnij_twitter=0;}
		if(isset($_POST['udostepnij_wykop'])){$udostepnij_wykop=1;}else{$udostepnij_wykop=0;}
		if(isset($_POST['udostepnij_google'])){$udostepnij_google=1;}else{$udostepnij_google=0;}
		if(isset($_POST['udostepnij_pinterest'])){$udostepnij_pinterest=1;}else{$udostepnij_pinterest=0;}
		if(isset($_POST['smtp'])){$smtp=1;}else{$smtp=0;}
		if(isset($_POST['mapa_uzytkownikow'])){$mapa_uzytkownikow=1;}else{$mapa_uzytkownikow=0;}
		
		mysql_query('update ustawienia set base_url="'.adres_www($_POST['base_url']).'", tytul="'.$_POST['tytul'].'", keywords="'.$_POST['keywords'].'", description="'.$_POST['description'].'", stopka_url="'.adres_www($_POST['stopka_url']).'", stopka_nazwa="'.$_POST['stopka_nazwa'].'", email="'.$_POST['email'].'", szablon="'.$_POST['szablon'].'", ile_na_strone="'.$_POST['ile_na_strone'].'", konto_ile_na_strone="'.$_POST['konto_ile_na_strone'].'", facebook="'.$_POST['facebook'].'", analytics="'.htmlspecialchars($_POST['analytics']).'", adsense="'.htmlspecialchars($_POST['adsense']).'", mapa="'.$mapa.'", limit_mapa="'.$_POST['limit_mapa'].'", google_maps_api="'.$_POST['google_maps_api'].'", rozmiar_upload="'.$_POST['rozmiar_upload'].'", logo_obrazek="'.$logo_obrazek.'", logowanie_facebook="'.$logowanie_facebook.'", facebook_api="'.$_POST['facebook_api'].'", facebook_secret="'.$_POST['facebook_secret'].'", wylacz_niezalogowani="'.$wylacz_niezalogowani.'", tworzenie="'.$tworzenie.'", dodawaj_znak_wodny="'.$dodawaj_znak_wodny.'", glowna_pelne_zdjecia="'.$glowna_pelne_zdjecia.'", reklama_co_obrazkow="'.$_POST['reklama_co_obrazkow'].'", logo_w_ramce="'.$logo_w_ramce.'", wybor_kolor_ramki="'.$wybor_kolor_ramki.'", logo_w_ramce_tekst="'.$_POST['logo_w_ramce_tekst'].'", logo_w_ramce_kolor="'.$_POST['logo_w_ramce_kolor'].'", memy="'.$memy.'", domyslnie_mem="'.$domyslnie_mem.'", konkursy="'.$konkursy.'", udostepnij_fb="'.$udostepnij_fb.'", udostepnij_twitter="'.$udostepnij_twitter.'", udostepnij_wykop="'.$udostepnij_wykop.'", udostepnij_google="'.$udostepnij_google.'", udostepnij_pinterest="'.$udostepnij_pinterest.'", smtp="'.$smtp.'", smtp_email="'.filtruj($_POST['smtp_email']).'", smtp_host="'.filtruj($_POST['smtp_host']).'", smtp_uzytkownik="'.filtruj($_POST['smtp_uzytkownik']).'", smtp_haslo="'.filtruj($_POST['smtp_haslo']).'", mapa_uzytkownikow="'.$mapa_uzytkownikow.'", mapa_center="'.filtruj($_POST['mapa_center']).'", komentarze_glosy="'.isset($_POST['komentarze_glosy']).'", komentarze_pokaz_najlepszy="'.isset($_POST['komentarze_pokaz_najlepszy']).'", komentarze_facebook="'.isset($_POST['komentarze_facebook']).'" limit 1');
		
		array_map('unlink', glob("../tmp/*"));
	}
	pobierz_ustawienia();
}

