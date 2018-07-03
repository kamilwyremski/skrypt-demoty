<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja']) and $_POST['akcja']=='usun_uzytkownika' and isset($_POST['id'])){
	$q = mysql_query('select id, wybor_obrazka, url from obrazki where autor_id="'.$_POST['id'].'"');
	while($dane = mysql_fetch_array($q)){
		usun_obrazek($dane['id']);
	}
	mysql_query('delete from uzytkownicy where id="'.$_POST['id'].'" limit 1');
}
if(isset($_GET['nieaktywni'])){
	$smarty->assign("nazwa", 'Nieaktywni użytkownicy');
	$smarty->assign("url_sortowania", '?akcja=uzytkownicy&nieaktywni');
	$warunek="aktywny=0";
}elseif(isset($_GET['aktywni'])){
	$smarty->assign("nazwa", 'Aktywni użytkownicy');
	$smarty->assign("url_sortowania", '?akcja=uzytkownicy&aktywni');
	$warunek="aktywny=1";
}elseif(isset($_GET['moderatorzy'])){
	$smarty->assign("nazwa", 'Moderatorzy serwisu');
	$smarty->assign("url_sortowania", '?akcja=uzytkownicy&moderatorzy');
	$warunek="moderator=1";
}else{
	$smarty->assign("url_sortowania", '?akcja=uzytkownicy');
	$warunek="true";
}

$sortuj = sortuj();
$ile_na_strone = 50;
$limit_start = policz_strony($ile_na_strone, 'uzytkownicy', $warunek);

$q = mysql_query('select id, login, email, aktywny, moderator, data, rejestracja_facebook from uzytkownicy where '.$warunek.' order by '.$sortuj.' limit '.$limit_start.','.$ile_na_strone.'');
while($dane = mysql_fetch_array($q)){
	$dane['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where autor_id="'.$dane['id'].'"'));
	$dane['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$dane['id'].'"'));
	$dane['ile_obrazkow_glowna'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$dane['id'].'" and glowna=1'));
	$uzytkownicy[] = $dane;
}
if(isset($uzytkownicy)){
	$smarty->assign("uzytkownicy", $uzytkownicy);
}
