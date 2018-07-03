<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zapisz'])){
	if($_POST['zapisz']=='onas' and isset($_POST['onas'])){
		mysql_query('update ustawienia set onas="'.htmlspecialchars($_POST['onas']).'" limit 1');
	}
	pobierz_ustawienia();
}
$ustawienia["onas"] = htmlspecialchars_decode($ustawienia["onas"]);

