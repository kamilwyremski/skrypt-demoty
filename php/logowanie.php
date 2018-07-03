<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

function logowanie(){
	global $uzytkownik, $smarty;
	$q = mysql_query('select login, email, id, moderator, data, imie, adres, miasta, mapa from uzytkownicy where login="'.filtruj($_SESSION['uzytkownik']).'" and id="'.filtruj($_SESSION['uzytkownik_id']).'" limit 1');
	while($dane = mysql_fetch_array($q)){$wynik = $dane;}
	if(isset($wynik)){
		$uzytkownik = $wynik;
		if(isset($smarty)){
			$smarty->assign("uzytkownik", $wynik);	
		}
	}else{
		unset($_SESSION['uzytkownik']);
		session_destroy();
	}
}
	
if(isset($_GET['log_out'])){
	unset($_SESSION['uzytkownik']);
	session_destroy();
	header("Location: ".$ustawienia['base_url']);
}elseif(isset($smarty) and $ustawienia['logowanie_facebook']==1 and $ustawienia['facebook_api']!='' and $ustawienia['facebook_secret']!=''){
	require_once(realpath(dirname(__FILE__)).'/facebook.php');
}

if(isset($_SESSION['uzytkownik']) and isset($_SESSION['uzytkownik_id'])){
	logowanie();
}

