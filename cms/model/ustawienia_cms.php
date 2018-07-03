<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zmien_ustawienia_cms']) and isset($_POST['nowy_login']) and isset($_POST['nowe_haslo']) and isset($_POST['powtorz_nowe_haslo'])){
	global $cms_login;
	if($_POST['nowe_haslo']!==$_POST['powtorz_nowe_haslo']){
		$smarty->assign("komunikat", 'Podane hasła są różne!');
	}else{
		$login = filtruj($_POST['nowy_login']);
		mysql_query('update cms set login="'.$login.'", haslo=md5("'.filtruj($_POST['nowe_haslo']).'") where login="'.$cms_login.'" limit 1');
		$_SESSION['cms_zalogowany'] = true;
		$_SESSION['cms_login'] = $login;
		$smarty->assign("komunikat", 'Ustawienia zostały zmienione poprawnie.');
	}
}

if(isset($_POST['usun_logi_cms'])){
	mysql_query('delete from cms_logi');
}else{
	$q = mysql_query('select * from cms_logi');
	while($dane = mysql_fetch_array($q)){$cms_logi[] = $dane;}
	if(isset($cms_logi)){
		$smarty->assign("cms_logi", $cms_logi);
	}
}

