<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['dodaj_z_dysku']) and isset($_POST['dodaj_z_dysku_cel']) and isset($_POST['dodaj_z_dysku_uzytkownik']) and isset($_FILES['obrazy'])){
	
	if(isset($_POST['dodaj_z_dysku_kategoria']) and $_POST['dodaj_z_dysku_kategoria']>0){
		$kategoria = filtruj($_POST['dodaj_z_dysku_kategoria']);
	}else{
		$kategoria = 0;
	}
	$uploadDir = "../upload/";
	$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
	$dodane = array();
	for ($i = 0; $i < count($_FILES['obrazy']['name']); $i++) {
		if($_FILES['obrazy']['name'][$i]!=''){
			$ext = substr(strrchr($_FILES['obrazy']['name'][$i], "."), 1); 
			if (in_array($ext, $allowedExts)){		
				$img = explode('.', $_FILES['obrazy']['name'][$i]);
				
				$img = preg_replace('/\s+/', '', $img);
				
				if(file_exists($uploadDir.$_FILES['obrazy']['name'][$i])) {   
					$fPath = $img[0].'_'.time().'.'.$ext;
					move_uploaded_file($_FILES['obrazy']['tmp_name'][$i], $uploadDir . $fPath);
					$url = $fPath;
				}else{
					move_uploaded_file($_FILES['obrazy']['tmp_name'][$i], $uploadDir . $_FILES['obrazy']['name'][$i]);
					$url = $_FILES['obrazy']['name'][$i];
				}
				if($ustawienia['dodawaj_znak_wodny']=='1'){
					dodaj_znak_wodny($url);
				}
				if($_POST['dodaj_z_dysku_cel']){
					$data_glowna = date("Y-m-d H:i:s");
				}else{
					$data_glowna = '';
				}
				mysql_query('INSERT INTO `obrazki`(`kategoria`, `glowna`, `tytul`, `prosty_tytul`,`wybor_obrazka`, `url`, `miniaturka`, `autor_id`, `data_glowna`, `data`) VALUES ("'.$kategoria.'", "'.filtruj($_POST['dodaj_z_dysku_cel']).'", "'.filtruj($_FILES['obrazy']['name'][$i]).'", "'.prosta_nazwa(filtruj($_FILES['obrazy']['name'][$i])).'", "z_dysku", "'.$url.'", "'.$url.'", "'.filtruj($_POST['dodaj_z_dysku_uzytkownik']).'", "'.$data_glowna.'", NOW())');
				$obrazy_temp['id'] = mysql_insert_id();
				$obrazy_temp['url'] = $url;
				$obrazy_temp['tytul'] = $img[0];
				$dodane[] = $obrazy_temp;
			}
		}
	}
	if($dodane[0]){
		$smarty->assign("dodane", $dodane);
	}
}elseif(isset($_POST['dodaj_z_dysku_zatwierdz']) and isset($_POST['id'])){
	foreach ($_POST['id'] as $id=>$tytul) {
		mysql_query('update obrazki set tytul="'.$tytul.'", prosty_tytul="'.prosta_nazwa($tytul).'" where id="'.$id.'" limit 1');
	}
	$smarty->assign("wykonano", true);
}

$q = mysql_query('select id, login from uzytkownicy where aktywny=1 order by login');
while($dane = mysql_fetch_array($q)){$dodaj_z_dysku_uzytkownicy[]=$dane;}
if (isset($dodaj_z_dysku_uzytkownicy)){	$smarty->assign("dodaj_z_dysku_uzytkownicy", $dodaj_z_dysku_uzytkownicy);}
	
?>