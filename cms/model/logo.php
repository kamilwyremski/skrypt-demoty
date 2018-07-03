<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['zmien_logo'])){
	function laduj_obraz($nazwa, $roz1, $roz2){
		$ext = substr(strrchr($_FILES[$nazwa]['name'], "."), 1); 
		if($ext == $roz1 or $ext == $roz2){
			move_uploaded_file($_FILES[$nazwa]['tmp_name'], "../obrazy/".$nazwa.'.'.$roz1);
		}
	}
	if($_FILES['logo']['name']!=''){
		laduj_obraz('logo', 'png', 'PNG');
	}
	if($_FILES['logo_facebook']['name']!=''){
		laduj_obraz('logo_facebook', 'png', 'PNG');
	}
	if($_FILES['watermark']['name']!=''){
		laduj_obraz('watermark', 'png', 'PNG');
	}
	if($_FILES['favicon']['name']!=''){
		laduj_obraz('favicon', 'ico', 'ICO');
	}
	pobierz_ustawienia();
}

