<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($_GET['search_users'])){
	$login = filtruj($_GET['login']);
	$imie = filtruj($_GET['name']);
	$adres = filtruj($_GET['address']);
	$miasta = filtruj($_GET['city']);
	$warunek='login like "%'.$login.'%" and imie like "%'.$imie.'%" and adres like "%'.$adres.'%" and miasta like "%'.$miasta.'%"';
	$smarty->assign("login", $login);
	$smarty->assign("imie", $imie);
	$smarty->assign("adres", $adres);
	$smarty->assign("miasta", $miasta);
}else{
	$warunek="true";
}

$ile_na_strone = 25;
$limit_start = policz_strony($ile_na_strone, 'uzytkownicy', $warunek.' and aktywny=1');

$q = mysql_query('select id, login, data, imie, adres, miasta from uzytkownicy where '.$warunek.' and aktywny=1 limit '.$limit_start.','.$ile_na_strone.'');
while($dane = mysql_fetch_array($q)){
	$dane['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$dane['id'].'"'));
	$dane['ile_obrazkow_glowna'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$dane['id'].'" and glowna=1'));
	$uzytkownicy[] = $dane;
}
if(isset($uzytkownicy)){$smarty->assign("uzytkownicy", $uzytkownicy);}

pobierz_losowe_obrazki();
pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();
