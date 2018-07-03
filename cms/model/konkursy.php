<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja'])){
	if($_POST['akcja']=='dodaj_konkurs' and isset($_POST['tytul']) and isset($_POST['opis']) and isset($_POST['start']) and isset($_POST['koniec'])){
		if(isset($_POST['wlaczony'])){$wlaczony=1;}else{$wlaczony=0;}
		mysql_query('INSERT INTO `konkursy`(`id`, `wlaczony`, `tytul`, `prosty_tytul`, `opis`, `zwyciezca`, `start`, `koniec`, `data`) VALUES (null, "'.$wlaczony.'", "'.$_POST['tytul'].'", "'.prosta_nazwa($_POST['tytul']).'", "'.htmlspecialchars($_POST['opis']).'", "", "'.$_POST['start'].'", "'.$_POST['koniec'].'", "'.date("Y-m-d H:i:s").'")');
	}elseif($_POST['akcja']=='edytuj_konkurs' and isset($_POST['id']) and isset($_POST['tytul']) and isset($_POST['opis']) and isset($_POST['start']) and isset($_POST['koniec'])){
		if(isset($_POST['wlaczony'])){$wlaczony=1;}else{$wlaczony=0;}
		if(isset($_POST['zwyciezca']) and $_POST['zwyciezca']!=0){$zwyciezca=$_POST['zwyciezca'];}else{$zwyciezca=0;}
		mysql_query('update konkursy set wlaczony="'.$wlaczony.'", tytul="'.$_POST['tytul'].'", prosty_tytul="'.prosta_nazwa($_POST['tytul']).'", opis="'.htmlspecialchars($_POST['opis']).'", zwyciezca="'.$zwyciezca.'", start="'.$_POST['start'].'", koniec="'.$_POST['koniec'].'" where id="'.$_POST['id'].'" limit 1');;
	}elseif($_POST['akcja']=='usun_konkurs' and isset($_POST['id'])){
		mysql_query('delete from konkursy where id="'.$_POST['id'].'"');
	}
}

$q = mysql_query('select * from konkursy order by id desc');
while($dane = mysql_fetch_array($q)){
	if($dane['zwyciezca']!=0){
		$q2 = mysql_query('select login from uzytkownicy where id='.$dane['zwyciezca'].' limit 1');
		while($dane2 = mysql_fetch_array($q2)){
			$dane['wygral'] = $dane2['login'];
		}
	}
	$dane['opis'] = htmlspecialchars_decode($dane['opis']);
	$konkursy[] = $dane;
}
if(isset($konkursy)){
	$smarty->assign("konkursy", $konkursy);
	$q = mysql_query('select id, login from uzytkownicy where aktywny=1 order by login');
	while($dane = mysql_fetch_array($q)){$konkursy_uzytkownicy[]=$dane;}
	if (isset($konkursy_uzytkownicy)){$smarty->assign("konkursy_uzytkownicy", $konkursy_uzytkownicy);}
}

?>
	