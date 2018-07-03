<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja'])){
	if($_POST['akcja']=='dodaj_zdjecie'){
		list($url, $miniaturka) = thubmnailCreator("../upload/", 1);
		mysql_query('INSERT INTO `memy_obrazki`(`url`, `miniaturka`) VALUES ("'.$url.'", "'.$miniaturka.'")');
	}elseif($_POST['akcja']=='usun_zdjecie' and isset($_POST['id'])){
		$q = mysql_query('select url, miniaturka from memy_obrazki where id="'.filtruj($_POST['id']).'" limit 1');
		while($dane = mysql_fetch_array($q)){
			unlink('../upload/'.$dane['url']);
			unlink('../upload/'.$dane['miniaturka']);
		}
		mysql_query('delete from memy_obrazki where id="'.filtruj($_POST['id']).'" limit 1');	
	}
}
$q = mysql_query('select * from memy_obrazki');
while($dane = mysql_fetch_array($q)){$memy_obrazki[] = $dane;}
if(isset($memy_obrazki)){$smarty->assign("memy_obrazki", $memy_obrazki);}
	
