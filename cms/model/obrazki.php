<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja'])){
	if($_POST['akcja']=='usun_obrazek' and isset($_POST['id'])){
		usun_obrazek(filtruj($_POST['id']));
	}elseif($_POST['akcja']=='usun_obrazki' and isset($_POST['id'])){
		foreach($_POST['id'] as $id=>$value){
			usun_obrazek(filtruj($id));
		}
	}elseif($_POST['akcja']=='usun_poczekalnie'){
		$q = mysql_query('select id, url, wybor_obrazka from obrazki where glowna="0"');
		while($dane = mysql_fetch_array($q)){
			usun_obrazek($dane['id']);
		}
		
	}elseif($_POST['akcja']=='aktywuj_poczekalnie'){
		mysql_query('update obrazki set glowna="1", data_glowna="'.date("Y-m-d H:i:s").'" where glowna="0"');
	}
}
if(isset($_GET['poczekalnia'])){
	$smarty->assign("nazwa_kategorii", 'w Poczekalni');
	$smarty->assign("url_sortowania", '?akcja=obrazki&poczekalnia');
	$warunek="obrazki.glowna=0";
}elseif(isset($_GET['id'])){
	$qk = mysql_query('select nazwa from kategorie where id="'.$_GET['id'].'"');
	while($dane = mysql_fetch_array($qk)){$smarty->assign("nazwa_kategorii", 'w kategorii '.$dane['nazwa']);};
	$smarty->assign("url_sortowania", '?akcja=obrazki&id='.$_GET['id'].'&nazwa='.$_GET['nazwa']);
	$warunek='obrazki.kategoria='.$_GET['id'].'';
}else{
	$smarty->assign("nazwa_kategorii", '- wszystkie');
	$smarty->assign("url_sortowania", '?akcja=obrazki');
	$warunek="true";
}

$sortuj = sortuj('obrazki.data desc');
$ile_na_strone = 50;
$limit_start = policz_strony($ile_na_strone, 'obrazki', $warunek);

$q = mysql_query('select obrazki.id, obrazki.glowna, obrazki.kategoria, obrazki.tytul, obrazki.prosty_tytul, obrazki.wybor_obrazka, obrazki.url, obrazki.miniaturka, obrazki.glosy, obrazki.data_glowna, obrazki.data, uzytkownicy.login from obrazki, uzytkownicy where '.$warunek.' and obrazki.autor_id=uzytkownicy.id order by '.$sortuj.' limit '.$limit_start.','.$ile_na_strone.'');
while($dane = mysql_fetch_array($q)){
	$q2 = mysql_query('select nazwa, prosta_nazwa from kategorie where id='.$dane['kategoria'].' limit 1');
	while($dane2 = mysql_fetch_array($q2)){
		$dane['nazwa'] = $dane2['nazwa'];
		$dane['prosta_nazwa'] = $dane2['prosta_nazwa'];					
	}
	$dane['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where obrazek_id="'.$dane['id'].'"'));
	$obrazki[] = $dane;
}
if(isset($obrazki)){$smarty->assign("obrazki", $obrazki);}
	
