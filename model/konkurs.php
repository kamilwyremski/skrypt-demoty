<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$q = mysql_query('select * from konkursy where id="'.filtruj($_GET['id']).'" limit 1');
while($dane = mysql_fetch_array($q)){
	if($dane['zwyciezca']!=0){
		$q2 = mysql_query('select login from uzytkownicy where id='.$dane['zwyciezca'].' limit 1');
		while($dane2 = mysql_fetch_array($q2)){
			$dane['wygral'] = $dane2['login'];
		}
	}
	if( strtotime($dane['koniec']) < strtotime('now') ) {
		$dane['wlaczony']=0;
	}elseif( strtotime($dane['start']) > strtotime('now') ) {
		$dane['wlaczony']=0;
	}
	$dane['opis'] = htmlspecialchars_decode($dane['opis']);
	$konkurs = $dane;
}
if(isset($konkurs)){
	$smarty->assign("konkurs", $konkurs);
	$title = $konkurs['tytul'].' - '.$ustawienia['tytul'];
}else{
	pobierz_losowe_obrazki();
}

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

