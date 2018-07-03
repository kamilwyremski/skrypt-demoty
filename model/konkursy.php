<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$q = mysql_query('select id, wlaczony, tytul, prosty_tytul, zwyciezca, koniec from konkursy where start < CURRENT_DATE() order by wlaczony desc');
while($dane = mysql_fetch_array($q)){
	if($dane['zwyciezca']!=0){
		$q2 = mysql_query('select login from uzytkownicy where id='.$dane['zwyciezca'].' limit 1');
		while($dane2 = mysql_fetch_array($q2)){
			$dane['wygral'] = $dane2['login'];
		}
	}
	if( strtotime($dane['koniec']) < strtotime('now') ) {
		$dane['wlaczony']=0;
	}
	$konkursy[] = $dane;
}
if(isset($konkursy)){
	$smarty->assign("konkursy", $konkursy);
}else{
	pobierz_losowe_obrazki();
}

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

