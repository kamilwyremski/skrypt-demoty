<?php
 
session_start();

include('../config/db.php');

include('globalne.php');
include('funkcje.php');
include('logowanie.php');

if(isset($_POST['akcja'])){
	global $uzytkownik;
	if($_POST['akcja']=='glos' and isset($_POST['glos']) and isset($_POST['id'])){
		$ip = get_client_ip();
		$id = filtruj($_POST['id']);
		$glos = filtruj($_POST['glos']);
		if(isset($uzytkownik)){$autor_id=$uzytkownik['id'];}else{$autor_id=0;}
		$q = mysql_query('select * from glosy where obrazek_id="'.$id.'" and ((autor_id="'.$autor_id.'" and autor_id!="0") or (ip="'.$ip.'" and autor_id="'.$autor_id.'")) limit 1');
		while($dane = mysql_fetch_array($q)){$wynik = $dane;}
		if(isset($wynik)){
			if($wynik['glos']!=$glos){
				mysql_query('update glosy set glos="'.$glos.'", data = NOW() where id="'.$wynik['id'].'" limit 1');
				mysql_query('update obrazki set glosy=glosy+2*'.$glos.' where id="'.$id.'" limit 1');				
			}
		}else{
			mysql_query('update obrazki set glosy=glosy+'.$glos.' where id="'.$id.'" limit 1');
			mysql_query('insert into glosy (`obrazek_id`, `glos`, `autor_id`, `ip`, `data`) values ("'.$id.'", "'.$glos.'", "'.$autor_id.'", "'.$ip.'", NOW())');
		}
		$glosy = mysql_fetch_array(mysql_query('select glosy from obrazki where id="'.$id.'" limit 1'))['glosy'];
		echo($glosy);
	}elseif($ustawienia['komentarze_glosy'] and $_POST['akcja']=='komentarz_glos' and isset($_POST['glos']) and isset($_POST['id'])){
		if(isset($uzytkownik)){
			$ip = get_client_ip();
			$id = filtruj($_POST['id']);
			$glos = filtruj($_POST['glos']);
			if(isset($uzytkownik)){$autor_id=$uzytkownik['id'];}else{$autor_id=0;}
			$q = mysql_query('select * from komentarze_glosy where komentarz_id="'.$id.'" and ((autor_id="'.$autor_id.'" and autor_id!="0") or (ip="'.$ip.'" and autor_id="'.$autor_id.'")) limit 1');
			while($dane = mysql_fetch_array($q)){$wynik = $dane;}
			if(isset($wynik)){
				if($wynik['glos']!=$glos){
					mysql_query('update komentarze_glosy set glos="'.$glos.'", data="'.date("Y-m-d H:i:s").'" where id="'.$wynik['id'].'" limit 1');			
				}
			}else{
				mysql_query('insert into komentarze_glosy (`komentarz_id`, `glos`, `autor_id`, `ip`, `data`) VALUES ("'.$id.'", "'.$glos.'", "'.$autor_id.'", "'.$ip.'", NOW())');
			}
			$glosy = mysql_num_rows(mysql_query('select 1 from komentarze_glosy where komentarz_id="'.$id.'" and glos="1"')) - mysql_num_rows(mysql_query('select 1 from komentarze_glosy where komentarz_id="'.$id.'" and glos="-1"'));
			echo($glosy);
		}
	}elseif($_POST['akcja']=='usun_obrazek' and isset($_POST['id']) and isset($uzytkownik)){
		$id = filtruj($_POST['id']);
		if($uzytkownik['moderator']==1){
			$warunek = mysql_num_rows(mysql_query('select wybor_obrazka, url from obrazki where id="'.$id.'" limit 1'));
		}else{
			$warunek = mysql_num_rows(mysql_query('select wybor_obrazka, url from obrazki where id="'.$id.'" and autor_id="'.$uzytkownik['id'].'" limit 1'));
		}
		if($warunek){
			usun_obrazek($id);
			echo true;
		}
	}elseif(isset($uzytkownik) and $ustawienia['tworzenie']==1 and $_POST['akcja']=='stworz_podglad' and isset($_POST['tytul']) and $_POST['tytul']!='' and isset($_POST['tytul_font']) and isset($_POST['opis_font'])){
		include('../model/stworz.php');
		stworz_obrazek(false);
	}elseif($_POST['akcja']=='usun_komentarz' and isset($_POST['id']) and isset($uzytkownik)){
		$id = filtruj($_POST['id']);
		if(mysql_num_rows(mysql_query('select id from komentarze where id="'.$id.'" and autor_id="'.$uzytkownik['id'].'" limit 1'))>0 or $uzytkownik['moderator']==1){
			usun_komentarz($id);
			echo true;
		}
	}elseif($_POST['akcja']=='na_glowna' and isset($_POST['id']) and isset($uzytkownik) and $uzytkownik['moderator']==1){
		mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where id="'.filtruj($_POST['id']).'" limit 1');
		echo true;
	}
}


