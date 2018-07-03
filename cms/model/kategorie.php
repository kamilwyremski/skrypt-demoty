<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja'])){
	if($_POST['akcja']=='dodaj_kategorie' and isset($_POST['nazwa']) and isset($_POST['kategoria_glowna'])){
		$tymczasowa_nazwa = $prosta_nazwa = prosta_nazwa($_POST['nazwa']);
		for($i=2;mysql_num_rows(mysql_query('select id from kategorie where prosta_nazwa="'.$tymczasowa_nazwa.'" limit 1'))>0;$i++){
			$tymczasowa_nazwa = $prosta_nazwa.'-'.$i;
		}
		$prosta_nazwa = $tymczasowa_nazwa;
		mysql_query('insert into kategorie (`id`, `nazwa`, `prosta_nazwa`, `kategoria_glowna`) values(null, "'.$_POST['nazwa'].'", "'.$prosta_nazwa.'", "'.$_POST['kategoria_glowna'].'")');
	}elseif($_POST['akcja']=='edytuj_kategorie' and isset($_POST['id']) and isset($_POST['nazwa']) and isset($_POST['kategoria_glowna'])){
		$tymczasowa_nazwa = $prosta_nazwa = prosta_nazwa($_POST['nazwa']);
		for($i=2;mysql_num_rows(mysql_query('select id from kategorie where prosta_nazwa="'.$tymczasowa_nazwa.'" and id!="'.$_POST['id'].'" limit 1'))>0;$i++){
			$tymczasowa_nazwa = $prosta_nazwa.'-'.$i;
		}
		$prosta_nazwa = $tymczasowa_nazwa;
		if($_POST['id']==$_POST['kategoria_glowna']){$kategoria_glowna=0;}else{$kategoria_glowna=$_POST['kategoria_glowna'];}
		mysql_query('update kategorie set nazwa="'.$_POST['nazwa'].'", prosta_nazwa="'.$prosta_nazwa.'", kategoria_glowna="'.$kategoria_glowna.'" where id="'.$_POST['id'].'" limit 1');
	}elseif($_POST['akcja']=='usun_kategorie' and isset($_POST['id'])){
		$id = filtruj($_POST['id']);
		$qk = mysql_query('select kategoria_glowna from kategorie where id="'.$id.'" limit 1');
		while($dane = mysql_fetch_array($qk)){$kategoria_glowna = $dane['kategoria_glowna'];}
		if(isset($kategoria_glowna)){
			mysql_query('update kategorie set kategoria_glowna = "'.$kategoria_glowna.'" where kategoria_glowna = "'.$id.'"');
		}
		if(isset($_POST['usun_obrazki'])){
			$q = mysql_query('select id, wybor_obrazka, url from obrazki where kategoria="'.$id.'"');
			while($dane = mysql_fetch_array($q)){
				usun_obrazek($dane['id']);
			}
		}else{
			mysql_query('update obrazki set kategoria="0" where kategoria="'.$id.'"');
		}
		mysql_query('delete from kategorie where id="'.$id.'" limit 1');
	}
}
	
?>