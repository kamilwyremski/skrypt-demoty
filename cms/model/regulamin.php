<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zapisz'])){
	if($_POST['zapisz']=='regulamin' and isset($_POST['regulamin']) and isset($_POST['polityka_prywatnosci'])){
		mysql_query('update ustawienia set regulamin="'.htmlspecialchars($_POST['regulamin']).'", polityka_prywatnosci="'.htmlspecialchars($_POST['polityka_prywatnosci']).'" limit 1');
	}
	pobierz_ustawienia();
}

$ustawienia["regulamin"] = htmlspecialchars_decode($ustawienia["regulamin"]);
$ustawienia["polityka_prywatnosci"] = htmlspecialchars_decode($ustawienia["polityka_prywatnosci"]);

