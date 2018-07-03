<?php

function pobierz_kategorie(){
	global $smarty;
	$q = mysql_query('select * from kategorie where kategoria_glowna=0 order by nazwa');
	while($dane = mysql_fetch_array($q)){
		$q2 = mysql_query('select * from kategorie where kategoria_glowna='.$dane['id'].' order by nazwa');
		while($dane2 = mysql_fetch_array($q2)){
			$q3 = mysql_query('select * from kategorie where kategoria_glowna='.$dane2['id'].' order by nazwa');
			while($dane3 = mysql_fetch_array($q3)){
				$dane3['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where kategoria="'.$dane3['id'].'"'));
				$dane2['podkategorie'][]=$dane3;
			}
			$dane2['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where kategoria="'.$dane2['id'].'"'));
			$dane['podkategorie'][]=$dane2;
		}
		$dane['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where kategoria="'.$dane['id'].'"'));
		$kategorie[] = $dane;
	}
	if(isset($kategorie)){$smarty->assign("kategorie", $kategorie);	}
}
	
function policz_strony($limit='10', $tabela, $warunek='true'){
	global $smarty;
	if (isset($_GET['strona']) and is_numeric($_GET['strona']) and $_GET['strona']>0)  { 
		$limit_start = ($_GET['strona']-1)*$limit;
		$smarty->assign("ktora_strona", $_GET['strona']);
	}else{
		$limit_start = 0;
		$smarty->assign("ktora_strona", 1);
	}
	$smarty->assign("ile_stron", ceil(mysql_num_rows(mysql_query('select * from '.$tabela.' where '.$warunek.''))/$limit));
	$url_strony = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; 
	if (strpos($url_strony,'&strona') !== false) {
		$url_strony = substr($url_strony,0,strpos($url_strony,"&strona"));
	}elseif (strpos($url_strony,'?strona') !== false) {
		$url_strony = substr($url_strony,0,strpos($url_strony,"?strona"));
	}
	$smarty->assign("url_strony", $url_strony);
	$smarty->assign("iteration", $limit_start);
	return $limit_start;
}

function sortuj($domyslne='data'){
	if(isset($_GET['sortuj'])){
		$sortuj = $_GET['sortuj'];
		if(isset($_GET['desc'])){$sortuj .= ' desc';}
	}else{
		$sortuj = $domyslne;
	}
	return $sortuj;
}

function thubmnailCreator($sciezka, $miniaturka){
	if(!empty($_FILES)){
		$img_dir=$sciezka;
		$img = explode('.', $_FILES['img']['name']);
		$nazwa = $img[0].'_'.time().'.'.$img[1];
		$originalImage=$img_dir.$nazwa;
		$image_filePath=$_FILES['img']['tmp_name'];
		$nazwa_miniaturki=$img[0].'_'.time().'_thumb.'.$img[1];
		$img_thumb = $img_dir . $nazwa_miniaturki;
		$extension = strtolower($img[1]);
		if(in_array($extension , array('jpg','jpeg', 'gif', 'png', 'bmp', 'JPG', 'JPEG', 'GIF', 'PNG', 'BMP'))){
			if($miniaturka){
				list($gotwidth, $gotheight, $gottype, $gotattr)= getimagesize($image_filePath); 	
				if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
					$src = imagecreatefromjpeg($_FILES['img']['tmp_name']);
				}else if($extension=="png" || $extension=="PNG" ){
					$src = imagecreatefrompng($_FILES['img']['tmp_name']);
				}else{
					$src = imagecreatefromgif($_FILES['img']['tmp_name']);
				}
				list($width,$height)=getimagesize($_FILES['img']['tmp_name']);
				if($gotwidth>=124){
					$newwidth=124;
				}else{
					$newwidth=$gotwidth;
				}
				$newheight=round(($gotheight*$newwidth)/$gotwidth);
				$tmp=imagecreatetruecolor($newwidth,$newheight);
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
				$createImageSave=imagejpeg($tmp,$img_thumb,100);
				if($createImageSave){
					$uploadOrginal=move_uploaded_file($_FILES['img']['tmp_name'],$originalImage);	
					return array($nazwa, $nazwa_miniaturki);
				}
			}else{
				$uploadOrginal=move_uploaded_file($_FILES['img']['tmp_name'],$originalImage);	
				return array($nazwa, $nazwa);
			}
		}
	}
}
