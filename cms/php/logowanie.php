<?php

if(isset($_GET['wyloguj'])){
	$_SESSION['cms_zalogowany'] = false;
	session_destroy();
	header('Location: '.$ustawienia['base_url'].'/cms');
}else if(isset($_POST['login']) and isset($_POST['haslo'])){
	$login = filtruj($_POST['login']);
	if(mysql_num_rows(mysql_query('SELECT id FROM cms_logi WHERE zalogowal=0 AND data > DATE_ADD(NOW(), INTERVAL -30 MINUTE) AND ip="'.filtruj(get_client_ip()).'";')) > 4){
		$_SESSION['cms_zalogowany'] = false;
		$smarty->assign("komunikat", 'Przekroczono limit prób logowania.');
	}else{
		if (mysql_num_rows(mysql_query('SELECT login, haslo FROM cms WHERE login = "'.$login.'" AND haslo = "'.md5(filtruj($_POST['haslo'])).'";')) > 0){
			mysql_query('insert into cms_logi values(null, "'.$login.'", "1", "'.date("Y-m-d H:i:s").'", "'.filtruj(get_client_ip()).'")');
			$_SESSION['cms_zalogowany'] = true;
			$_SESSION['cms_login'] = $login;
		}else{
			mysql_query('insert into cms_logi values(null, "'.$login.'", "0", "'.date("Y-m-d H:i:s").'", "'.filtruj(get_client_ip()).'")');
			$_SESSION['cms_zalogowany'] = false;
			$smarty->assign("komunikat", 'Niepoprawny login lub hasło.');
		}
	}
}

if(isset($_SESSION['cms_login']) and $_SESSION['cms_login']!='' and isset($_SESSION['cms_zalogowany'])){
	global $cms_login;
	$cms_login = $_SESSION['cms_login'];
	if(isset($smarty)){
		$smarty->assign("cms_login", $cms_login);	
	}
}

