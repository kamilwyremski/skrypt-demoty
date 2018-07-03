<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$q = mysql_query('select id, tytul, prosty_tytul, mapa from obrazki where glowna=1 and mapa!="" order by rand() limit '.$ustawienia['limit_mapa']);
while($dane = mysql_fetch_array($q)){
	$mapa[] = $dane;
} 
if(isset($mapa)){$smarty->assign("mapa", $mapa);}

pobierz_losowe_obrazki();
pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

