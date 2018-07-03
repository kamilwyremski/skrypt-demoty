<?php
 
header('Content-Type: text/html; charset=utf-8');

if(!isset($_POST['uruchom'])){
	include(realpath(dirname(__FILE__)).'/config/db.php');
	include(realpath(dirname(__FILE__)).'/php/globalne.php');
	include(realpath(dirname(__FILE__)).'/php/funkcje.php');
}

function cron_daily(){
	global $ustawienia;
	
	$cron = mysql_fetch_assoc(mysql_query('select * from automatyzacja where wlacz=1 limit 1'));
	if($cron){
		if($cron['wlacz_wszystkie']==1){
			mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where glowna=0');
		}else{
			if($cron['wlacz_dni_wiecej']==1){
				mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where glowna=0 and data < DATE_ADD(CURDATE(), INTERVAL -'.$cron['dni_wiecej'].' DAY)');
			}
			if($cron['wlacz_dni_mniej']==1){
				mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where glowna=0 and data > DATE_ADD(CURDATE(), INTERVAL -'.$cron['dni_mniej'].' DAY)');
			}
			if($cron['wlacz_glosy']==1){
				mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where glowna=0 and glosy>='.$cron['glosy'].'');
			}
			if($cron['wlacz_komentarze']==1){
				$q = mysql_query('select id from obrazki where glowna=0');
				while($dane = mysql_fetch_array($q)){
					if(mysql_num_rows(mysql_query('select id from komentarze where obrazek_id="'.$dane['id'].'"'))>=$cron['komentarze']){
						mysql_query('update obrazki set glowna=1, data_glowna="'.date("Y-m-d H:i:s").'" where id='.$dane['id'].'');
					}
				}
			}
		}
		if($cron['wlacz_obrazki_inne_strony']==1 and $cron['lista_stron']!='' and mysql_num_rows(mysql_query('select id from uzytkownicy where id="'.$cron['inne_strony_uzytkownik'].'"'))>0 and (!$cron['inne_strony_kategoria'] or mysql_num_rows(mysql_query('select id from kategorie where id="'.$cron['inne_strony_kategoria'].'"'))>0)){
			$lista_stron = explode("\n", $cron['lista_stron']);
			$realpath = realpath(dirname(__FILE__));
			if($ustawienia['dodawaj_znak_wodny']=='1'){
				$stamp = imagecreatefrompng($realpath.'/obrazy/watermark.png');
				$stampsx = imagesx($stamp);
				$stampsy = imagesy($stamp);
			}
			foreach ($lista_stron as $strona) {
				if($strona!=''){
					$html = file_get_contents($strona);
					$doc = new DOMDocument();
					@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
					$tags = $doc->getElementsByTagName('img');
					
					foreach ($tags as $tag) {
						$src = $tag->getAttribute('src');
						$text = $alt = $tag->getAttribute('alt');
						
						$alt = filtruj(iconv(mb_detect_encoding($alt), "UTF-8", $alt));
						
						if(!mysql_num_rows(mysql_query('select 1 from obrazki where tytul="'.$alt.'" and wybor_obrazka="z_dysku" and autor_id="'.$cron['inne_strony_uzytkownik'].'" and kategoria="'.$cron['inne_strony_kategoria'].'" limit 1'))){
							
							$extension = substr(strrchr($src, "."), 1); 
							if(substr($src, 0,1)=='/'){
								$src = substr($src,1);
							}
							if(substr($src, 0, 7) != "http://" and substr($src, 0, 8) != "https://") {
								$src = $strona.'/'.$src;
							}

							if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
								$obraz = imagecreatefromjpeg($src);
							}else if($extension=="png" || $extension=="PNG" ){
								$obraz = imagecreatefrompng($src);
							}else if($extension=="gif" || $extension=="GIF" ){
								$obraz = imagecreatefromgif($src);
							}else{
								$obraz = false;
							}
							if($obraz){
								$imagesx = imagesx($obraz);
								$imagesy = imagesy($obraz);
								if($imagesx>=$cron['min_szerokosc'] and $imagesy>=$cron['min_wysokosc']){
									$nazwa = pathinfo($src);
									$nazwa['basename'] = substr($nazwa['basename'],0,100);
									if(file_exists($realpath.'/upload/'.$nazwa['basename'])) {   
										$url = $nazwa['filename'].'_'.time().'.'.$nazwa['extension'];
									}else{
										$url = $nazwa['basename'];
									}
									if(isset($stamp)){
										imagecopy($obraz,$stamp,$imagesx-$stampsx - 5, $imagesy - $stampsy - 5, 0, 0,$stampsx, $stampsy);
									}
									if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
										imagejpeg($obraz, $realpath.'/upload/'.$url);
									}else if($extension=="png" || $extension=="PNG" ){
										imagepng($obraz, $realpath.'/upload/'.$url);
									}else if($extension=="gif" || $extension=="GIF" ){
										imagegif($obraz, $realpath.'/upload/'.$url);
									}
									imagedestroy($obraz);
									
									if($ustawienia['dodawaj_znak_wodny']=='1'){
										dodaj_znak_wodny($url);
									}
						
									if($cron['inne_strony_cel']){
										$glowna = 1; $data_glowna = date("Y-m-d H:i:s");
									}else{
										$glowna = $data_glowna = 0;
									}
			
									mysql_query('INSERT INTO `obrazki`(`kategoria`, `glowna`, `tytul`, `prosty_tytul`, `wybor_obrazka`, `url`, `miniaturka`, `autor_id`, `data_glowna`, `data`) VALUES ("'.$cron['inne_strony_kategoria'].'", "'.$glowna.'", "'.$alt.'", "'.prosta_nazwa($alt).'", "z_dysku", "'.$url.'", "'.$url.'", "'.$cron['inne_strony_uzytkownik'].'", "'.$data_glowna.'", "'.date("Y-m-d H:i:s").'")');
								}
							}
						}
					}
				}
			}	
		}
		if($cron['generuj_sitemap']){
			include_once('php/sitemap_generator.php');
			sitemap_generator();
		}
		if($cron['usun_bledne_obrazki']){
			$q = mysql_query('select id, url from obrazki where wybor_obrazka="z_dysku" order by id desc');
			while($dane = mysql_fetch_array($q)){
				if(!file_exists(realpath(dirname(__FILE__)).'/upload/'.$dane['url'])){
					usun_obrazek($dane['id']);
				}
			}
		}
	}
	
}
cron_daily();
