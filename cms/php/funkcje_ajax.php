<?php

session_start(); 

include('../../config/db.php');
include('../../php/globalne.php');
include('globalne.php');
include('logowanie.php');

if(isset($cms_login) and isset($_POST['akcja'])){
	if($_POST['akcja']=='aktywuj_obrazek' and isset($_POST['id'])){
		mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where id="'.$_POST['id'].'" limit 1');
	}elseif($_POST['akcja']=='aktywuj_uzytkownika' and isset($_POST['id'])){
		mysql_query('update uzytkownicy set aktywny=1 where id="'.$_POST['id'].'" limit 1');
	}elseif($_POST['akcja']=='usun_komentarz' and isset($_POST['id'])){
		usun_komentarz(filtruj($_POST['id']));
	}elseif($_POST['akcja']=='ustaw_moderator' and isset($_POST['id']) and isset($_POST['moderator'])){
		mysql_query('update uzytkownicy set moderator="'.$_POST['moderator'].'" where id="'.$_POST['id'].'"');
	}elseif($_POST['akcja']=='boks_pozycja' and isset($_POST['id']) and isset($_POST['pozycja']) and isset($_POST['dzialanie'])){
		if($_POST['dzialanie']==0){$warunek = '<'; $sortowanie = 'desc';}else{$warunek = '>'; $sortowanie = 'asc';}
		$q = mysql_query('select id, pozycja from boksy where pozycja '.$warunek.' '.$_POST['pozycja'].' order by pozycja '.$sortowanie.' limit 1');
		while($dane = mysql_fetch_array($q)){$wynik = $dane;}
		if(isset($wynik)){
			mysql_query('update boksy set pozycja="'.$wynik['pozycja'].'" where id="'.$_POST['id'].'" limit 1');
			mysql_query('update boksy set pozycja="'.$_POST['pozycja'].'" where id="'.$wynik['id'].'" limit 1');
		}
	}elseif($_POST['akcja']=='usun_tag' and isset($_POST['id'])){
		$q = mysql_query('select id, tagi from obrazki where tagi like "%-'.$_POST['id'].'-%"');
		while($dane = mysql_fetch_array($q)){
			mysql_query('update obrazki set tagi="'.str_replace("-".$_POST['id']."-","",$dane['tagi']).'" where id="'.$dane['id'].'"');
		}
		mysql_query('delete from tagi where id="'.$_POST['id'].'" limit 1');
	}
}
