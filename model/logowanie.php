<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($_POST['login_email']) and isset($_POST['haslo']) and isset($_POST['logowanie'])){

	$q = mysql_query('select login, id, email, aktywny, moderator, data, imie, adres, miasta from uzytkownicy where (email="'.filtruj($_POST['login_email']).'" or login="'.filtruj($_POST['login_email']).'") and haslo="'.md5(filtruj($_POST['haslo'])).'" limit 1');
	while($dane = mysql_fetch_array($q)){$wynik = $dane;}
	if(isset($wynik)){
		if($wynik['aktywny']=='1'){
			$_SESSION['uzytkownik'] = $wynik['login'];
			$_SESSION['uzytkownik_id'] = $wynik['id'];
			$uzytkownik = $wynik;
			$smarty->assign("uzytkownik", $wynik);	
			
			if(isset($_GET['redirect']) and $_GET['redirect']!=''){
				header("Location: ".$_GET['redirect']);
			}else{
				header("Location: ".$ustawienia['base_url']);
			}

		}else{
			$smarty->assign("blad", $tlumaczenia_teksty['konto_nie_aktywowane']);
		}
	}else{
		$smarty->assign("blad", $tlumaczenia_teksty['dane_nieprawidlowe']);
		$smarty->assign("login_email", $_POST['login_email']);
	}
	
}elseif(isset($_GET['kod'])){

	$q = mysql_query('select id from uzytkownicy where kod_aktywacyjny="'.filtruj($_GET['kod']).'" and aktywny="0" limit 1');
	while($dane = mysql_fetch_array($q)){$wynik = $dane;}
	if(isset($wynik)){
		mysql_query('update uzytkownicy set aktywny="1" where kod_aktywacyjny="'.filtruj($_GET['kod']).'" limit 1');
		$smarty->assign("info", $tlumaczenia_teksty['aktywacja_info']);
	}else{
		$smarty->assign("blad", $tlumaczenia_teksty['aktywacja_blad']);
	}
}elseif(isset($_GET['new'])){
	$smarty->assign("info", $tlumaczenia_teksty['potwierdz_link_aktywacyjny']);
}elseif(isset($_GET['reset'])){
	$smarty->assign("info", $tlumaczenia_teksty['nowe_haslo_wyslane']);
}

