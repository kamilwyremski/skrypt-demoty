<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja']) and $_POST['akcja']=='usun_stworzony' and isset($_POST['id'])){
	$q = mysql_query('select url from stworzone where id="'.$_POST['id'].'" limit 1');
	while($dane = mysql_fetch_array($q)){
		$q2 = mysql_query('select id from obrazki where wybor_obrazka="stworzony" and url="'.$dane['url'].'"');
		while($dane2 = mysql_fetch_array($q2)){
			usun_obrazek($dane2['id']);
		}
	}
	mysql_query('delete from stworzone where id="'.$_POST['id'].'" limit 1');	
}

$smarty->assign("url_sortowania", '?akcja=stworzone');
$sortuj = sortuj('data');
$ile_na_strone = 25;
$limit_start = policz_strony($ile_na_strone, 'stworzone', 'true');

$q = mysql_query('select id, tytul, url, data, autor_id from stworzone order by '.$sortuj.' limit '.$limit_start.','.$ile_na_strone.'');
while($dane = mysql_fetch_array($q)){
	$q2 = mysql_query('select login from uzytkownicy where id="'.$dane['autor_id'].'"');
	while($dane2 = mysql_fetch_array($q2)){$dane['login'] = $dane2['login'];}
	$stworzone[] = $dane;
}
if(isset($stworzone)){
	$smarty->assign("stworzone", $stworzone);
}

