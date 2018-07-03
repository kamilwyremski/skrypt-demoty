<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_GET['id'])){
	$qk = mysql_query('select nazwa from kategorie where id="'.$_GET['id'].'"');
	while($dane = mysql_fetch_array($qk)){$smarty->assign("nazwa_kategorii", 'w kategorii '.$dane['nazwa']);};
	$smarty->assign("url_sortowania", '?akcja=komentarze&id='.$_GET['id'].'&nazwa='.$_GET['nazwa']);
	$warunek='obrazki.kategoria='.$_GET['id'].'';
}else{
	$smarty->assign("nazwa_kategorii", '- wszystkie');
	$smarty->assign("url_sortowania", '?akcja=komentarze');
	$warunek="true";
}

$sortuj = sortuj('komentarze.data');
$ile_na_strone = 25;
$limit_start = policz_strony($ile_na_strone, 'komentarze, obrazki', 'komentarze.obrazek_id = obrazki.id and '.$warunek);

$q = mysql_query('select komentarze.id, komentarze.tresc, komentarze.data, uzytkownicy.login, uzytkownicy.email, obrazki.tytul, obrazki.id as obrazek_id, obrazki.prosty_tytul from komentarze, uzytkownicy, obrazki where '.$warunek.' and komentarze.autor_id=uzytkownicy.id and komentarze.obrazek_id=obrazki.id order by '.$sortuj.' limit '.$limit_start.','.$ile_na_strone.'');
while($dane = mysql_fetch_array($q)){
	$dane['tresc'] = htmlspecialchars_decode($dane['tresc']);
	$komentarze[] = $dane;
}
if(isset($komentarze)){$smarty->assign("komentarze", $komentarze);}
	
