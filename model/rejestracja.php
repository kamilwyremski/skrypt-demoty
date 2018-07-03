<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($_POST['rejestracja']) and isset($_POST['email']) and isset($_POST['login']) and isset($_POST['haslo']) and isset($_POST['powtorz_haslo']) and isset($_POST['captcha'])){

	global $smarty, $ustawienia, $tlumaczenia_teksty, $tlumaczenia_linki;
	$blad = false;

	if($_POST['captcha']!=$_SESSION['captcha']){
		$blad = true;
		$smarty->assign("blad_captcha", $tlumaczenia_teksty['blad_captcha']);
	}else{
		$q = mysql_query('select id from uzytkownicy where login="'.filtruj($_POST['login']).'" limit 1');
		while($dane = mysql_fetch_array($q)){$wynik= $dane;}
		if(isset($wynik) and $wynik!=''){
			$blad = true;
			$smarty->assign("blad_login", $tlumaczenia_teksty['blad_login_zajety']);
		}
		
		if ((strpos($_POST['login'],'@') !== false)  or strlen($_POST['login'])>32) {
			$blad = true;
			$smarty->assign("blad_login", $tlumaczenia_teksty['blad_login']);
		}
		
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) or strlen($_POST['email'])>32) {
			$blad = true;
			$smarty->assign("blad_email", $tlumaczenia_teksty['blad_email']);
		}
		
		$q = mysql_query('select id from uzytkownicy where email="'.filtruj($_POST['email']).'" limit 1');
		while($dane = mysql_fetch_array($q)){$wynik2= $dane;}
		if(isset($wynik2)){
			$blad = true;
			$smarty->assign("blad_email", $tlumaczenia_teksty['blad_email_zajety']);
		}
		
		if($_POST['haslo']!=$_POST['powtorz_haslo']){
			$blad = true;
			$smarty->assign("blad_haslo", $tlumaczenia_teksty['blad_hasla_rozne']);
		}
		
		if(strlen($_POST['haslo'])>32){
			$blad = true;
			$smarty->assign("blad_haslo", $tlumaczenia_teksty['blad_dlugie_haslo']);
		}
		
		if(!isset($_POST['regulamin'])){
			$blad = true;
			$smarty->assign("blad_regulamin", $tlumaczenia_teksty['pole_obowiazkowe']);
		}
	}
	
	if(!$blad){
		$kod_aktywacyjny = md5(uniqid(rand(), true));
		
		if(isset($_POST['dodatkowe_informacje'])){
			$imie = filtruj($_POST['imie']);
			$adres = filtruj($_POST['adres']);
			$miasta = filtruj($_POST['miasta']);
		}else{
			$imie = $adres = $miasta = '';
		}
		if(isset($_POST['mapa']) and $_POST['mapa']!=''){$mapa=filtruj($_POST['mapa']);}else{$mapa='';}
		
		mysql_query('INSERT INTO `uzytkownicy`(`login`, `email`, `haslo`, `kod_aktywacyjny`, `data`, `imie`, `adres`, `miasta`, `mapa`) values("'.filtruj($_POST['login']).'", "'.filtruj($_POST['email']).'", "'.md5($_POST['haslo']).'", "'.$kod_aktywacyjny.'", "'.date("Y-m-d H:i:s").'", "'.$imie.'", "'.$adres.'", "'.$miasta.'", "'.$mapa.'")');
		
		$address = $_POST['email'];
		
		$subject = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_rejestracja_temat']);
		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';
		
		$link_aktywacyjny = '<a href="'.$ustawienia['base_url'].'?akcja=activate&kod='.$kod_aktywacyjny.'">'.$ustawienia['base_url'].'?akcja=activate&kod='.$kod_aktywacyjny.'</a>';
		$ustawienia["email_rejestracja_tresc"] = htmlspecialchars_decode($ustawienia["email_rejestracja_tresc"]);
		$ustawienia['email_rejestracja_tresc'] = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_rejestracja_tresc']);
		$ustawienia['email_rejestracja_tresc'] = str_replace("{link_aktywacyjny}",$link_aktywacyjny,$ustawienia['email_rejestracja_tresc']);
		
		$message = '<!doctype html><html lang="pl"><head><meta charset="utf-8"></head><body>'.$ustawienia['email_rejestracja_tresc'].'</body></html>';
		
		if($ustawienia['smtp']){
			include_once(realpath(dirname(__FILE__)).'/../config/smtp.php');
			
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->AddAddress($address);
			$mail->Send();
		
		}else{
			$header = 'Reply-To: <'.$ustawienia['email']."> \r\n"; 
			$header .= 'From: '.$ustawienia['email'].' <'.$ustawienia['email'].">\r\n"; 
			$header .= "MIME-Version: 1.0 \r\n"; 
			$header .= "Content-Type: text/html; charset=UTF-8"; 

			mail($address, $subject, $message, $header);
		}
		
		header("Location: ".$tlumaczenia_linki['logowanie']."?new");
	}else{
		$smarty->assign("email", $_POST['email']);
		$smarty->assign("login", $_POST['login']);
		$smarty->assign("haslo", $_POST['haslo']);
		if(isset($_POST['dodatkowe_informacje'])){
			$smarty->assign("imie", $_POST['imie']);
			$smarty->assign("adres", $_POST['adres']);
			$smarty->assign("miasta", $_POST['miasta']);
		}
		if(isset($_POST['mapa'])){
			$smarty->assign("mapa", $_POST['mapa']);
		}
	}
	
}

function rejestracja_facebook($object){
	global $ustawienia;
	$email = $object->getProperty('email');
	$q = mysql_query('select id, login from uzytkownicy where email="'.$email.'" and email!="" limit 1');
	while($dane = mysql_fetch_array($q)){$wynik = $dane;}
	if($email==''){
		return array("", "");
	}elseif(isset($wynik)){
		return array($wynik['id'], $wynik['login']);
	}else{
		$first_name = $object->getProperty('first_name');
		$last_name = $object->getProperty('last_name');
		$login = $first_name.$last_name;
		
		$tymczasowy_login = $login;
		for($i=2;mysql_num_rows(mysql_query('select id from uzytkownicy where login="'.$tymczasowy_login.'" limit 1'))>0;$i++){
			$tymczasowy_login = $login.'-'.$i;
		}
		$login = $tymczasowy_login;

		$haslo = randomPassword();
	
		$address = $email;
		
		$subject = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_rejestracja_fb_temat']);
		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

		$ustawienia["email_rejestracja_fb_tresc"] = htmlspecialchars_decode($ustawienia["email_rejestracja_fb_tresc"]);
		$ustawienia['email_rejestracja_fb_tresc'] = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_rejestracja_fb_tresc']);
		$ustawienia['email_rejestracja_fb_tresc'] = str_replace("{login}",$login,$ustawienia['email_rejestracja_fb_tresc']);
		$ustawienia['email_rejestracja_fb_tresc'] = str_replace("{haslo}",$haslo,$ustawienia['email_rejestracja_fb_tresc']);
		
		$message = '<!doctype html><html lang="pl"><head><meta charset="utf-8"></head><body>'.$ustawienia['email_rejestracja_fb_tresc'].'</body></html>';
		
		if($ustawienia['smtp']){
			include_once(realpath(dirname(__FILE__)).'/../config/smtp.php');
			
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->AddAddress($address);
			$mail->Send();
		
		}else{
			$header = 'Reply-To: <'.$ustawienia['email']."> \r\n"; 
			$header .= 'From: '.$ustawienia['email'].' <'.$ustawienia['email'].">\r\n"; 
			$header .= "MIME-Version: 1.0 \r\n"; 
			$header .= "Content-Type: text/html; charset=UTF-8"; 

			mail($address, $subject, $message, $header);
		}
		
		$kod_aktywacyjny = md5(uniqid(rand(), true));
		mysql_query('INSERT INTO `uzytkownicy`(`id`, `login`, `email`, `haslo`, `aktywny`, `kod_aktywacyjny`, `moderator`, `data`, `rejestracja_facebook`, `imie`, `adres`, `miasta`) values(null, "'.$login.'", "'.$email.'", "'.md5($haslo).'", "1", "'.$kod_aktywacyjny.'", "0", "'.date("Y-m-d H:i:s").'", "1", "'.$first_name.' '.$last_name.'", "", "")');
		return array(mysql_insert_id(), $login);
	}
}
