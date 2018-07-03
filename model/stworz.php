<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($uzytkownik)){
	
	function hex2rgb($hex,$default_color='#000000') {
		$hex = str_replace("#", "", $hex);
		if(!preg_match('/^[a-f0-9]{6}$/i', $hex)){
			$hex = str_replace("#", "", $default_color);
		}
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		}else{
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb;
	}

	function stworz_obrazek($zapisz=false){
		global $smarty, $uzytkownik, $ustawienia, $tlumaczenia_teksty;

		$realpath = realpath(dirname(__FILE__)).'/../';
		if($zapisz){
			$uploadDir = 'upload/';
		}else{
			$uploadDir = 'tmp/';
		}

		if((isset($_FILES['obraz']) and $_FILES['obraz']['name']!='') or (isset($_POST['typ']) and $_POST['typ']=='mem_obrazek')){
			$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
			if(!isset($_POST['typ']) or $_POST['typ']!='mem_obrazek'){
				$extension = substr(strrchr($_FILES['obraz']['name'], "."), 1);
			}else{
				$adres_obrazka = mysql_fetch_assoc(mysql_query('select url from memy_obrazki where id="'.$_POST['mem_obrazek'].'" limit 1'))['url'];
				$extension = substr(strrchr($adres_obrazka, "."), 1);
			}
			if (in_array($extension, $allowedExts)){
				if(isset($_POST['typ']) and $_POST['typ']=='mem_obrazek'){
					if($zapisz){
						if(file_exists($realpath.$uploadDir.$adres_obrazka)) {
							$img = explode('.', $adres_obrazka);
							$url = $fPath = $img[0].'_'.time().'.'.$extension;
							copy($realpath.'upload/'.$adres_obrazka, $realpath.$uploadDir.$fPath);
						}else{
							copy($realpath.'upload/'.$adres_obrazka, $realpath.$uploadDir.$adres_obrazka);
							$url = $adres_obrazka;
						}
					}else{
						copy($realpath.'upload/'.$adres_obrazka, $realpath.$uploadDir.$uzytkownik['id'].'.'.$extension);
						$url = $uzytkownik['id'].'.'.$extension;
					}
				}else{
					if($zapisz){
						if(file_exists($realpath.$uploadDir.$_FILES['obraz']['name'])) {
							$img = explode('.', $_FILES['obraz']['name']);
							$url = $fPath = $img[0].'_'.time().'.'.$extension;
							move_uploaded_file($_FILES['obraz']['tmp_name'], $realpath.$uploadDir.$fPath);
						}else{
							move_uploaded_file($_FILES['obraz']['tmp_name'],$realpath.$uploadDir.$_FILES['obraz']['name']);
							$url = $_FILES['obraz']['name'];
						}
					}else{
						move_uploaded_file($_FILES['obraz']['tmp_name'],$realpath.$uploadDir.$uzytkownik['id'].'.'.$extension);
						$url = $uzytkownik['id'].'.'.$extension;
					}
				}
				if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
					$src = imagecreatefromjpeg($realpath.$uploadDir.$url);
				}else if($extension=="png" || $extension=="PNG" ){
					$src = imagecreatefrompng($realpath.$uploadDir.$url);
				}else if($extension=="gif" || $extension=="GIF" ){
					$src = imagecreatefromgif($realpath.$uploadDir.$url);
				}
				list($width,$height)=getimagesize($ustawienia['base_url'].'/'.$uploadDir.$url);
				if($ustawienia['memy']=='1' and ($_POST['typ']=='mem' or $_POST['typ']=='mem_obrazek')){
					$newheight=round(($height*580)/$width);
					$tmp=imagecreatetruecolor(580,$newheight);
					imagecopyresampled($tmp,$src,0,0,0,0,580,$newheight, $width,$height);
					$y = 0;
				}else{
					$newheight=round(($height*506)/$width);
					$tmp=imagecreatetruecolor(510,$newheight+4);
					if ($ustawienia['wybor_kolor_ramki'] == '1' and isset($_POST['kolor_ramki'])){
						$kolor_ramka = hex2rgb($_POST['kolor_ramki'],'#FFFFFF');
					}else{
						$kolor_ramka = hex2rgb('#FFFFFF','#FFFFFF');
					}
					$kolor_ramka_wyjscie = imagecolorallocate($tmp, $kolor_ramka[0], $kolor_ramka[1], $kolor_ramka[2]);
					imagefill($tmp, 0, 0, $kolor_ramka_wyjscie);
					imagecopyresampled($tmp,$src,2,2,0,0,506,$newheight-4, $width,$height);
					if($ustawienia['logo_w_ramce']=='1' and $ustawienia['logo_w_ramce_tekst']!=''){
						$font = $realpath.'fonts/arialbd.ttf';
						$logo=imagecreatetruecolor(580,14);
						imagefill($logo, 0, 0, $kolor_ramka_wyjscie);
						$kolor_logo_w_ramce = hex2rgb($ustawienia['logo_w_ramce_kolor'],'#000000');
						$kolor_logo_w_ramce_wyjscie = imagecolorallocate($logo, $kolor_logo_w_ramce[0], $kolor_logo_w_ramce[1], $kolor_logo_w_ramce[2]);
						$font_size = 9;
						$text_box = imagettfbbox($font_size,0,$font,$ustawienia['logo_w_ramce_tekst']);
						imagettftext($logo, $font_size, 0, 4, $font_size+2, $kolor_logo_w_ramce_wyjscie, $font, $ustawienia['logo_w_ramce_tekst']);
						$logo_wyjscie = imagecreatetruecolor($text_box[2]+8, 14);
						imagecopyresized ($logo_wyjscie, $logo , 0, 0, 0, 0, $text_box[2]+8, 14, $text_box[2]+8, 14);
					}
					$y = $newheight+35;
				}
			}
		}
		if(!isset($url)){
			if($zapisz){
				$nazwa = prosta_nazwa(strip_tags(filtruj($_POST['tytul'])));
				if(file_exists($realpath.$uploadDir.$nazwa)) {
					$url = $nazwa.'_'.time().'.png';
				}else{
					$url = $nazwa.'.png';
				}
			}else{
				$url = $uzytkownik['id'].'.png';
			}
			$y = $newheight = 0;
		}

		if(($ustawienia['memy']=='1' and (isset($_POST['typ']) and $_POST['typ']=='mem' and isset($src)) or (isset($_POST['typ']) and $_POST['typ']=='mem_obrazek')) or !isset($_POST['typ']) or $_POST['typ']=='obrazek'){
			$obraz = imagecreatetruecolor(580, $newheight+3000);
			$kolor_tla = hex2rgb($_POST['kolor_tla'],'#000000');
			$kolor_tla_wyjscie = imagecolorallocate($obraz, $kolor_tla[0], $kolor_tla[1], $kolor_tla[2]);
			imagefill($obraz, 0, 0, $kolor_tla_wyjscie);
			if(isset($tmp)){
				if($ustawienia['memy']=='1' and ($_POST['typ']=='mem' or $_POST['typ']=='mem_obrazek')){
					imagecopymerge($obraz,$tmp,0,0,0,0, 580, $newheight,100);
				}else{
					imagecopymerge($obraz,$tmp,35,35,0,0, 510, $newheight,100);
				}
			}
			if(isset($logo_wyjscie)){
				imagecopyresampled($obraz,$logo_wyjscie,290-($text_box[2]+8)/2,$newheight+27,0,0,$text_box[2]+8,14, $text_box[2]+8,14);
			}

			$font = $realpath.'fonts/arial.ttf';
			$font_size = $_POST['tytul_font'];
			$kolor_font = hex2rgb($_POST['kolor_tytul'],'#FFFFFF');
			$font_color = imagecolorallocate($obraz, $kolor_font[0], $kolor_font[1], $kolor_font[2]);
			$tytul = preg_replace("/\r\n|\r|\n/",'|',$_POST['tytul']);
			$lines = explode('|', wordwrap($tytul, 62 - $font_size, '|'));
			foreach ($lines as $line){
				$y += $font_size*1.4;
				$text_box = imagettfbbox($font_size,0,$font,$line);
				$x = 290 - (($text_box[2]-$text_box[0])/2);
				imagettftext($obraz, $font_size, 0, $x, $y, $font_color, $font, $line);
			}
			$y +=10;

			$font_size = $_POST['opis_font'];
			$kolor_font = hex2rgb($_POST['kolor_opis'],'#FFFFFF');
			$font_color = imagecolorallocate($obraz, $kolor_font[0], $kolor_font[1], $kolor_font[2]);
			$opis = preg_replace("/\r\n|\r|\n/",'|',$_POST['opis']);
			if($opis!=''){
				if($ustawienia['memy']=='1' and ($_POST['typ']=='mem' or $_POST['typ']=='mem_obrazek')){
					$lines = explode('|', wordwrap($opis, 62 - $font_size, '|'));
					$y = $newheight - ($font_size*1.25)*(count($lines)+1)-10;
				}else{
					$lines = explode('|', wordwrap($opis, 55 - $font_size, '|'));
				}
				foreach ($lines as $line){
					$y += $font_size*1.4;
					$text_box = imagettfbbox($font_size,0,$font,$line);
					$x = 290 - (($text_box[2]-$text_box[0])/2);
					imagettftext($obraz, $font_size, 0, $x, $y, $font_color, $font, $line);
				}
			}
			$y += $font_size;

			if($ustawienia['memy']=='1' and ($_POST['typ']=='mem' or $_POST['typ']=='mem_obrazek')){
				$obraz_wyjsciowy = imagecreatetruecolor(580, $newheight);  //wymiary
				imagecopyresized ($obraz_wyjsciowy, $obraz , 0, 0, 0, 0, 580, $newheight, 580, $newheight);
			}else{
				$obraz_wyjsciowy = imagecreatetruecolor(580, $y);  //wymiary
				imagecopyresized ($obraz_wyjsciowy, $obraz , 0, 0, 0, 0, 580, $y, 580, $y);
			}

			if($ustawienia['dodawaj_znak_wodny']=='1'){
				$stamp = imagecreatefrompng($realpath.'obrazy/watermark.png');
				imagecopy($obraz_wyjsciowy, $stamp, imagesx($obraz_wyjsciowy) - imagesx($stamp) - 2, imagesy($obraz_wyjsciowy) - imagesy($stamp) - 2, 0, 0, imagesx($stamp), imagesy($stamp));
			}

			imagepng($obraz_wyjsciowy, $realpath.$uploadDir.$url);

			if($zapisz){
				mysql_query('INSERT INTO `stworzone`(`id`, `tytul`, `prosty_tytul`, `opis`, `url`, `autor_id`, `data`) VALUES (null, "'.$tytul.'", "'.prosta_nazwa($tytul).'", "'.htmlspecialchars(purify($opis)).'", "'.$url.'", "'.$uzytkownik['id'].'", "'.date("Y-m-d H:i:s").'")');
				$smarty->assign("ok_link", $url);
				$smarty->assign("ok_tytul", $tytul);
				$smarty->assign("ok_id", mysql_insert_id());
			}else{
				echo($ustawienia['base_url'].'/'.$uploadDir.$url);
			}

			imagedestroy($obraz_wyjsciowy);
			imagedestroy($obraz);
			if(isset($tmp)){
				imagedestroy($tmp);
			}
			return false;
		}elseif($zapisz){
			return $tlumaczenia_teksty['blad_plik_z_dysku'];
		}else{
			return false;
		}
	}


	function stworz_obrazek_php(){
		global $smarty, $uzytkownik, $ustawienia;

		if(isset($uzytkownik) and $ustawienia['tworzenie']==1 and isset($_POST['tytul']) and $_POST['tytul']!='' and isset($_POST['stworz']) and isset($_POST['tytul_font']) and isset($_POST['opis_font'])){

			$blad = false;

			if(strlen($_POST['tytul'])>128){
				$blad = 'Błąd! Za długi tytuł!';
			}elseif(strlen($_POST['opis'])>1024){
				$blad = 'Błąd! Za długi opis!';
			}else{
				$blad = stworz_obrazek(true);
			}

			if($blad){
				$smarty->assign("blad", $blad);
				$smarty->assign("tytul", $_POST['tytul']);
				$smarty->assign("opis", $_POST['opis']);
			}
		}
	}
	if(isset($smarty)){
		$q = mysql_query('select * from memy_obrazki');
		while($dane = mysql_fetch_array($q)){$memy_obrazki[] = $dane;}
		if(isset($memy_obrazki)){
			$smarty->assign("memy_obrazki", $memy_obrazki);
		}
		
		pobierz_kategorie();
		pobierz_boksy();
		pobierz_dane_do_boksow();
		stworz_obrazek_php();
		pobierz_losowe_obrazki();
	}
	
	
}else{
	header('Location: '.$tlumaczenia_linki['logowanie'].'?redirect='.$tlumaczenia_linki['stworz']);
}
