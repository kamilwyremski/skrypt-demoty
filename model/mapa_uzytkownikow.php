<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$q = mysql_query('select login, mapa from uzytkownicy where aktywny = 1 and mapa!="" order by rand() limit '.$ustawienia['limit_mapa']);
while($dane = mysql_fetch_array($q)){
	$mapa_uzytkownikow[] = $dane;
} 
if(isset($mapa_uzytkownikow)){$smarty->assign("mapa_uzytkownikow", $mapa_uzytkownikow);}

pobierz_losowe_obrazki();
pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

