<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($_POST['captcha']) and isset($_POST['temat']) and isset($_POST['tresc']) and isset($_POST['email']) and isset($_POST['imie'])){
	if($_POST['captcha']!=$_SESSION['captcha']){
		$smarty->assign("email_imie", $_POST['imie']);
		$smarty->assign("email_email", $_POST['email']);
		$smarty->assign("email_temat", $_POST['temat']);
		$smarty->assign("email_tresc", $_POST['tresc']);
		$smarty->assign("email_info", $tlumaczenia_teksty['blad_captcha']);
	}else{
		
		$address = $ustawienia['email'];
		
		$subject = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_kontakt_temat']);
		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

		$ustawienia["email_kontakt_tresc"] = htmlspecialchars_decode($ustawienia["email_kontakt_tresc"]);
		$ustawienia['email_kontakt_tresc'] = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_kontakt_tresc']);
		$ustawienia['email_kontakt_tresc'] = str_replace("{host}",$_SERVER['HTTP_HOST'],$ustawienia['email_kontakt_tresc']);
		$ustawienia['email_kontakt_tresc'] = str_replace("{imie}",$_POST['imie'],$ustawienia['email_kontakt_tresc']);
		$ustawienia['email_kontakt_tresc'] = str_replace("{email}",$_POST['email'],$ustawienia['email_kontakt_tresc']);
		$ustawienia['email_kontakt_tresc'] = str_replace("{temat}",$_POST['temat'],$ustawienia['email_kontakt_tresc']);
		$ustawienia['email_kontakt_tresc'] = str_replace("{tresc}",$_POST['tresc'],$ustawienia['email_kontakt_tresc']);
		
		$message = '<!doctype html><html lang="pl"><head><meta charset="utf-8"></head><body>'.$ustawienia['email_kontakt_tresc'].'</body></html>';
		
		if($ustawienia['smtp']){
			include_once(realpath(dirname(__FILE__)).'/../config/smtp.php');
			
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->AddAddress($address);
			$mail->Send();
		
		}else{
			$header = 'Reply-To: <'.$_POST['email']."> \r\n"; 
			$header .= 'From: '.$_POST['email'].' <'.$_POST['email'].">\r\n"; 
			$header .= "MIME-Version: 1.0 \r\n"; 
			$header .= "Content-Type: text/html; charset=UTF-8"; 

			mail($address, $subject, $message, $header);
		}
			
		$smarty->assign("email_info", $tlumaczenia_teksty['wiadomosc_wyslana']);
	}
}

$ustawienia["onas"] = htmlspecialchars_decode($ustawienia["onas"]);
$description = substr(strip_tags($ustawienia['onas']), 0, 300);

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();
pobierz_losowe_obrazki();

