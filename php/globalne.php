<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

function usun_komentarz($id){
	mysql_query('delete from komentarze_glosy where komentarz_id="'.$id.'"');
	mysql_query('delete from komentarze where id="'.$id.'" limit 1');
}

function usun_obrazek($id){
	$uploadDir = realpath(dirname(__FILE__)).'/../upload/';	
	$dane_obrazka = mysql_fetch_assoc(mysql_query('select wybor_obrazka, url from obrazki where id="'.$id.'" limit 1'));
	if($dane_obrazka['wybor_obrazka']=='z_dysku'){
		unlink($uploadDir.$dane_obrazka['url']);
	}elseif($dane_obrazka['wybor_obrazka']=='stworzony'){
		unlink($uploadDir.$dane_obrazka['url']);
		mysql_query('delete from obrazki where wybor_obrazka="stworzony" and url="'.$dane_obrazka['url'].'"');
		mysql_query('delete from stworzone where url="'.$dane_obrazka['url'].'" limit 1');	
	}
	mysql_query('delete from glosy where obrazek_id="'.$id.'"');
	$q = mysql_query('select id from komentarze where obrazek_id="'.$id.'"');
	while($dane = mysql_fetch_array($q)){
		usun_komentarz($dane['id']);
	}
	mysql_query('delete from obrazki where id="'.$id.'" limit 1');
}

function dodaj_znak_wodny($obrazek){
	$uploadDir = realpath(dirname(__FILE__)).'/../upload/';
	$stamp = imagecreatefrompng($uploadDir.'/../obrazy/watermark.png');
	$ext = substr(strrchr($uploadDir.$obrazek, "."), 1); 
	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG" ){
		$im = imagecreatefromjpeg($uploadDir.$obrazek);
	}else if($ext=="png" || $ext=="PNG" ){
		$im = imagecreatefrompng($uploadDir.$obrazek);
	}else{
		$im = imagecreatefromgif($uploadDir.$obrazek);
	}
	imagecopy($im,$stamp,imagesx($im)-imagesx($stamp) - 5, imagesy($im) - imagesy($stamp) - 5, 0, 0, imagesx($stamp), imagesy($stamp));
	if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG" ){
		imagejpeg($im,$uploadDir.$obrazek);
	}else if($ext=="png" || $ext=="PNG" ){
		imagepng($im,$uploadDir.$obrazek);
	}else{
		imagegif($im,$uploadDir.$obrazek);
	}
	imagedestroy($im);
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function adres_www($adres){
	if(substr($adres, 0, 7) != "http://" and substr($adres, 0, 8) != "https://" and $adres !='') {
		$adres = 'http://'.$adres;
	}
	if(substr($adres, -1)=='/'){
		$adres = substr($adres,0,-1);
	}
	return $adres;
}

function filtruj($zmienna){
    if(get_magic_quotes_gpc()){
        $zmienna = stripslashes($zmienna);
	}
    return mysql_real_escape_string(htmlspecialchars(trim(strip_tags($zmienna)))); 
}

function prosta_nazwa($text){
	$text = strtolower(str_replace(array(' ','%','$',':','–',',','/','=','?','Ę','Ó','Ą','Ś','Ł','Ż','Ź','Ć','Ń','ę','ó','ą','ś','ł','ż','ź','ć','ń'), array('-','-','','','','','','','','E','O','A','S','L','Z','Z','C','N','e','o','a','s','l','z','z','c','n'), $text));
	$text = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $text);
	$text = strtolower(str_replace(array(' ','$',':',',','/','=','?'), array('-','','','','','',''), $text));
	$text = preg_replace("/[^a-zA-Z0-9-]+/", "", $text);
	return $text;
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 
}

function purify($text=''){
	global $ustawienia, $purifier;
	require_once realpath(dirname(__FILE__)).'/../config/htmlpurifier.php';
	return $purifier->purify($text);
}
