<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_GET['jezyk']) and $_GET['jezyk']!=''){
	$jezyk = filtruj($_GET['jezyk']);
	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_jezyk_linki' and isset($_POST["rejestracja"]) and $_POST["rejestracja"]!="" and isset($_POST["logowanie"]) and $_POST["logowanie"]!="" and isset($_POST["dodaj"]) and $_POST["dodaj"]!="" and isset($_POST["stworz"]) and $_POST["stworz"]!="" and isset($_POST["kategoria"]) and $_POST["kategoria"]!="" and isset($_POST["tag"]) and $_POST["tag"]!="" and isset($_POST["poczekalnia"]) and $_POST["poczekalnia"]!="" and isset($_POST["top"]) and $_POST["top"]!="" and isset($_POST["reset_hasla"]) and $_POST["reset_hasla"]!="" and isset($_POST["konto"]) and $_POST["konto"]!="" and isset($_POST["onas"]) and $_POST["onas"]!="" and isset($_POST["regulamin"]) and $_POST["regulamin"]!="" and isset($_POST["polityka_prywatnosci"]) and $_POST["polityka_prywatnosci"]!="" and isset($_POST["profil"]) and $_POST["profil"]!="" and isset($_POST["mapa"]) and $_POST["mapa"]!="" and isset($_POST["mapa_uzytkownikow"]) and $_POST["mapa_uzytkownikow"]!="" and isset($_POST["edycja"]) and $_POST["edycja"]!="" and isset($_POST["uzytkownicy"]) and $_POST["uzytkownicy"]!="" and isset($_POST["stworzone"]) and $_POST["stworzone"]!="" and isset($_POST["konkursy"]) and $_POST["konkursy"]!="" and isset($_POST["konkurs"]) and $_POST["konkurs"]!=""){
		mysql_query('update tlumaczenia_linki set rejestracja="'.filtruj($_POST["rejestracja"]).'", logowanie="'.filtruj($_POST["logowanie"]).'", dodaj="'.filtruj($_POST["dodaj"]).'", stworz="'.filtruj($_POST["stworz"]).'", kategoria="'.filtruj($_POST["kategoria"]).'", tag="'.filtruj($_POST["tag"]).'", poczekalnia="'.filtruj($_POST["poczekalnia"]).'", top="'.filtruj($_POST["top"]).'", reset_hasla="'.filtruj($_POST["reset_hasla"]).'", konto="'.filtruj($_POST["konto"]).'", onas="'.filtruj($_POST["onas"]).'", regulamin="'.filtruj($_POST["regulamin"]).'", polityka_prywatnosci="'.filtruj($_POST["polityka_prywatnosci"]).'", profil="'.filtruj($_POST["profil"]).'", mapa="'.filtruj($_POST["mapa"]).'", mapa_uzytkownikow="'.filtruj($_POST["mapa_uzytkownikow"]).'", edycja="'.filtruj($_POST["edycja"]).'", uzytkownicy="'.filtruj($_POST["uzytkownicy"]).'", stworzone="'.filtruj($_POST["stworzone"]).'", konkursy="'.filtruj($_POST["konkursy"]).'", konkurs="'.filtruj($_POST["konkurs"]).'" where jezyk="'.$jezyk.'" limit 1');
		pobierz_tlumaczenia_linki();
	}
	$q = mysql_query('select * from tlumaczenia_linki where jezyk="'.$ustawienia['jezyk'].'" limit 1');
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_linki_aktywny = $dane;}
	$smarty->assign("tlumaczenia_linki_aktywny", $tlumaczenia_linki_aktywny);
	$q = mysql_query('select * from tlumaczenia_linki where jezyk="'.$jezyk.'" limit 1');
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_linki = $dane;}
	if (isset($tlumaczenia_linki)){$smarty->assign("tlumaczenia_linki", $tlumaczenia_linki);}
}

