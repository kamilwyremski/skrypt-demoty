<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$limit_start = policz_strony($ustawienia['ile_na_strone'], 'stworzone', true);

$q = mysql_query('select url from stworzone order by data desc limit '.$limit_start.','.$ustawienia['ile_na_strone'].'');
while($dane = mysql_fetch_array($q)){$stworzone[] = $dane;}
if(isset($stworzone)){
	$smarty->assign("stworzone", $stworzone);
}else{
	pobierz_losowe_obrazki();
}

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

