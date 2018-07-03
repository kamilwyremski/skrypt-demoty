<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zapisz']) and $_POST['zapisz']=='maile'){
	mysql_query('UPDATE `ustawienia` SET `email_rejestracja_temat`="'.filtruj($_POST['email_rejestracja_temat']).'", `email_rejestracja_tresc`="'.htmlspecialchars($_POST['email_rejestracja_tresc']).'", `email_rejestracja_fb_temat`="'.filtruj($_POST['email_rejestracja_fb_temat']).'", `email_rejestracja_fb_tresc`="'.htmlspecialchars($_POST['email_rejestracja_fb_tresc']).'", `email_reset_temat`="'.filtruj($_POST['email_reset_temat']).'", `email_reset_tresc`="'.htmlspecialchars($_POST['email_reset_tresc']).'", `email_kontakt_temat`="'.filtruj($_POST['email_kontakt_temat']).'", `email_kontakt_tresc`="'.htmlspecialchars($_POST['email_kontakt_tresc']).'" limit 1');
	pobierz_ustawienia();
}
$ustawienia["email_rejestracja_tresc"] = htmlspecialchars_decode($ustawienia["email_rejestracja_tresc"]);
$ustawienia["email_rejestracja_fb_tresc"] = htmlspecialchars_decode($ustawienia["email_rejestracja_fb_tresc"]);
$ustawienia["email_reset_tresc"] = htmlspecialchars_decode($ustawienia["email_reset_tresc"]);
$ustawienia["email_kontakt_tresc"] = htmlspecialchars_decode($ustawienia["email_kontakt_tresc"]);

