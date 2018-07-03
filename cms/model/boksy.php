<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja'])){
	if($_POST['akcja']=='dodaj_boks' and isset($_POST['tresc']) and isset($_POST['rodzaj']) and isset($_POST['ilosc'])){
		$nowapozycja=0;
		$q = mysql_query('select pozycja from boksy order by pozycja desc limit 1');
		while($dane = mysql_fetch_array($q)){$nowapozycja = $dane['pozycja'];}
		$nowapozycja++;
		mysql_query('insert into boksy (`id`, `pozycja`, `rodzaj`, `ilosc`, `tresc`) values(null, "'.$nowapozycja.'", "'.$_POST['rodzaj'].'", "'.$_POST['ilosc'].'", "'.htmlspecialchars($_POST['tresc']).'")');
	}elseif($_POST['akcja']=='edytuj_boks' and isset($_POST['id']) and isset($_POST['tresc']) and isset($_POST['rodzaj']) and isset($_POST['ilosc'])){
		mysql_query('update boksy set rodzaj="'.$_POST['rodzaj'].'", ilosc="'.$_POST['ilosc'].'", tresc="'.htmlspecialchars($_POST['tresc']).'" where id="'.$_POST['id'].'" limit 1');
	}elseif($_POST['akcja']=='usun_boks' and isset($_POST['id'])){
		mysql_query('delete from boksy where id="'.$_POST['id'].'" limit 1');
	}
}
$q = mysql_query('select * from boksy order by pozycja');
while($dane = mysql_fetch_array($q)){
	$dane['tresc'] = htmlspecialchars_decode($dane['tresc']);
	$boksy[]=$dane;
}
if(isset($boksy)){
	$smarty->assign("boksy", $boksy);
}
	
