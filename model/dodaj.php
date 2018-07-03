<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($uzytkownik)){
	
	if(isset($_GET['created']) and !is_nan($_GET['created'])){
		$q = mysql_query('select tytul, opis, url from stworzone where id="'.filtruj($_GET['created']).'" limit 1');
		while($dane = mysql_fetch_array($q)){
			$dane['opis'] = htmlspecialchars_decode($dane['opis']);
			$stworzony = $dane;	
		}
		if(isset($stworzony)){
			$smarty->assign("dodaj_tytul", $stworzony['tytul']);
			$smarty->assign("dodaj_opis", $stworzony['opis']);
			$smarty->assign("wybor_obrazka", 'stworzony');
			$smarty->assign("dodaj_stworzony", $stworzony['url']);
		}
	}

	if(isset($_POST['tytul']) and isset($_POST['dodaj']) and isset($_POST['wybor_obrazka'])){
		
		$blad = false;

		if(isset($_POST['kategoria']) and $_POST['kategoria']>0){
			$kategoria = filtruj($_POST['kategoria']);
		}else{
			$kategoria = 0;
		}
		$qs = mysql_query('select id, tytul, prosty_tytul from obrazki where tytul="'.filtruj($_POST['tytul']).'" and wybor_obrazka="'.filtruj($_POST['wybor_obrazka']).'" and kategoria="'.$kategoria.'" and opis="'.htmlspecialchars(purify($_POST['opis'])).'" and autor_id="'.$uzytkownik['id'].'"');
		if(mysql_num_rows($qs)==0){
			if($kategoria and !mysql_num_rows(mysql_query('select id from kategorie where id="'.$kategoria.'"'))){
				$blad =  $tlumaczenia_teksty['blad_kategoria'];
			}elseif(strlen($_POST['opis'])>3000){
				$blad =  $tlumaczenia_teksty['blad_opis'];			
			}else{
				if($_POST['wybor_obrazka']=='z_dysku'){
					$uploadDir = "upload/";
					if($_FILES['z_dysku']['name']!=''){
						$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
						$ext = substr(strrchr($_FILES['z_dysku']['name'], "."), 1); 
						if ((($_FILES["z_dysku"]["type"] == "image/gif") || ($_FILES["z_dysku"]["type"] == "image/jpeg") || ($_FILES["z_dysku"]["type"] == "image/jpg") || ($_FILES["z_dysku"]["type"] == "image/pjpeg") || ($_FILES["z_dysku"]["type"] == "image/x-png") || ($_FILES["z_dysku"]["type"] == "image/png"))	&& ($_FILES["z_dysku"]["size"] <= $ustawienia['rozmiar_upload']*1024) && in_array($ext, $allowedExts)){
							
							$nazwa_pliku = preg_replace('/\s+/', '', $_FILES['z_dysku']['name']);
							
							$img = explode('.', $nazwa_pliku);
							$nazwa_pliku = substr($img[0],0,100);
							
							if(file_exists($uploadDir.$nazwa_pliku.'.'.$ext)) {   
								$nazwa_pliku = $nazwa_pliku.'_'.time().'.'.$ext;
							}else{
								$nazwa_pliku = $nazwa_pliku.'.'.$ext;
							}
							
							move_uploaded_file($_FILES['z_dysku']['tmp_name'], $uploadDir . $nazwa_pliku);
							$miniaturka = $url = $nazwa_pliku;
							
							if($ustawienia['dodawaj_znak_wodny']=='1'){
								dodaj_znak_wodny($miniaturka);
							}
						}else{
							$blad = $tlumaczenia_teksty['blad_typ_pliku'];
						}
					}else{
						$blad = $tlumaczenia_teksty['blad_plik_z_dysku'];
					}
				}elseif($_POST['wybor_obrazka']=='z_internetu'){
					$miniaturka = $url = adres_www(filtruj($_POST['z_internetu']));
					if (!@getimagesize($miniaturka)) {
						$blad = $tlumaczenia_teksty['blad_url_nie_istnieje'];
					}
				}elseif($_POST['wybor_obrazka']=='z_youtube'){
					function getYoutubeIdFromUrl($url) {
						$parts = parse_url($url);
						if(isset($parts['query'])){
							parse_str($parts['query'], $qs);
							if(isset($qs['v'])){
								return $qs['v'];
							}else if(isset($qs['vi'])){
								return $qs['vi'];
							}
						}
						if(isset($parts['path'])){
							$path = explode('/', trim($parts['path'], '/'));
							return $path[count($path)-1];
						}
						return false;
					}
					$youtubeURL = getYoutubeIdFromUrl($_POST['z_youtube']);
					if($youtubeURL){
						$url = '//www.youtube.com/embed/'.$youtubeURL;
						$miniaturka = 'http://img.youtube.com/vi/'.$youtubeURL.'/0.jpg';
					}else{
						$blad = $tlumaczenia_teksty['blad_youtube'];
					}
				}elseif($_POST['wybor_obrazka']=='z_vimeo'){
					preg_match('/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/',$_POST['z_vimeo'],$matches);
					$id = $matches[2];	
					if($id==''){
						preg_match('/\/\/(www\.)?vimeo.com\/channels\/staffpicks\/(\d+)($|\/)/',$_POST['z_vimeo'],$matches);
						$id = $matches[2];	
					}
					if($id!=''){
						$url = 'http://player.vimeo.com/video/'.$id;
						$xml = simplexml_load_file("http://vimeo.com/api/v2/video/".$id.".xml");
						$xml = $xml->video;
						$miniaturka = $xml->thumbnail_large;
					}else{
						$blad = $tlumaczenia_teksty['blad_vimeo'];
					}
				}elseif($_POST['wybor_obrazka']=='z_dailymotion'){
					if(strrpos($_POST['z_dailymotion'], 'dailymotion.com')>0){
						$poz = strrpos($_POST['z_dailymotion'], '/');  
						$url = 'http://www.dailymotion.com/embed/video/'.substr($_POST['z_dailymotion'], $poz+1);	
						$miniaturka = 'http://www.dailymotion.com/thumbnail/video/'.substr($_POST['z_dailymotion'], $poz+1).'/0.jpg';	
					}else{
						$blad = $tlumaczenia_teksty['blad_dailymotion'];
					}
				}elseif($_POST['wybor_obrazka']=='stworzony' and $_POST['stworzony']!=''){
					$url = $miniaturka = $_POST['stworzony'];
				}else{
					$blad = $tlumaczenia_teksty['blad_inny'];
				}
			}
		}else{
			while($dane = mysql_fetch_array($qs)){$wynik = $dane;}
			$blad = $tlumaczenia_teksty['blad_dodales_juz'].'<br><a href="'.$ustawienia['base_url'].'/'.$wynik['id'].','.$wynik['prosty_tytul'].'" title="'.$wynik['tytul'].'">'.$ustawienia['base_url'].'/'.$wynik['id'].','.$wynik['prosty_tytul'].'</a><br>';
		}
		if($blad){
			$smarty->assign("blad", $blad);
			$smarty->assign("dodaj_tytul", filtruj($_POST['tytul']));
			$smarty->assign("dodaj_opis", purify($_POST['opis']));
			$smarty->assign("wybor_obrazka", filtruj($_POST['wybor_obrazka']));
			$smarty->assign("dodaj_z_youtube", filtruj($_POST['z_youtube']));
			$smarty->assign("dodaj_z_vimeo", filtruj($_POST['z_vimeo']));
			$smarty->assign("dodaj_z_dailymotion", filtruj($_POST['z_dailymotion']));
			$smarty->assign("dodaj_z_internetu", filtruj($_POST['z_internetu']));
			if(isset($_POST['kategoria'])){$smarty->assign("dodaj_kategoria", filtruj($_POST['kategoria']));}
			$smarty->assign("dodaj_tagi", filtruj($_POST['tagi']));
		}else{
			$tagi_output = '';
			if($_POST['tagi'] != ''){
				$tagi = str_replace(", ",",",filtruj($_POST['tagi']));
				$tagi = explode(',', $tagi);
				for($i=0; $i <= count($tagi) - 1; $i++){
					$tagi_prosta_nazwa = prosta_nazwa($tagi[$i]);
					if($tagi_prosta_nazwa!=''){
						$tag_juz_jest = false;
						$q = mysql_query('select id from tagi where prosta_nazwa = "'.$tagi_prosta_nazwa.'" limit 1');
						while($dane = mysql_fetch_array($q)){
							$tag_id = $dane['id'];
							$tag_juz_jest = true;
						}
						if($tag_juz_jest){
							$tagi_output.='-'.$tag_id.'-';
						}else{
							mysql_query('insert into tagi values(null, "'.$tagi[$i].'", "'.$tagi_prosta_nazwa.'")');
							$tagi_output.='-'.mysql_insert_id().'-';
						}
					}
				}
			}
			if(isset($_POST['mapa']) and $_POST['mapa']!=''){$mapa=$_POST['mapa'];}else{$mapa='';}
			$prosty_tytul = prosta_nazwa(filtruj($_POST['tytul']));
			if($uzytkownik['moderator']){
				$glowna = 1; $data_glowna = date("Y-m-d H:i:s");
			}else{
				$glowna = $data_glowna = 0;
			}
			mysql_query('insert into obrazki (`kategoria`, `tagi`, `glowna`, `tytul`, `prosty_tytul`, `opis`, `wybor_obrazka`, `url`, `miniaturka`, `mapa`, `autor_id`, `data_glowna`, `data`) values("'.$kategoria.'", "'.$tagi_output.'", "'.$glowna.'", "'.filtruj($_POST['tytul']).'", "'.$prosty_tytul.'", "'.htmlspecialchars(purify($_POST['opis'])).'", "'.filtruj($_POST['wybor_obrazka']).'", "'.$url.'", "'.$miniaturka.'", "'.$mapa.'", "'.$uzytkownik['id'].'", "'.$data_glowna.'", "'.date("Y-m-d H:i:s").'")');
			$smarty->assign("ok_link", $ustawienia['base_url'].'/'.mysql_insert_id().','.$prosty_tytul);
		}
	}
	
	pobierz_kategorie();
	pobierz_boksy();
	pobierz_dane_do_boksow();
	pobierz_losowe_obrazki();
	
}else{
	header('Location: '.$tlumaczenia_linki['logowanie'].'?redirect='.$tlumaczenia_linki['dodaj']);
}

