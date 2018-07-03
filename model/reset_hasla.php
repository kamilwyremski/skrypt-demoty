<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($_POST['email']) and isset($_POST['captcha'])){
	if($_POST['captcha']!=$_SESSION['captcha']){
		$smarty->assign("reset_email", $_POST['email']);
		$smarty->assign("blad", $tlumaczenia_teksty['blad_captcha']);
	}else{
		$q = mysql_query('select login, kod_aktywacyjny from uzytkownicy where email="'.filtruj($_POST['email']).'" limit 1');
		while($dane = mysql_fetch_array($q)){$wynik = $dane;}
		if(isset($wynik)){
			$haslo = randomPassword();
			mysql_query('update uzytkownicy set haslo="'.md5($haslo).'" where email="'.filtruj($_POST['email']).'" limit 1');

			$address = $_POST['email'];
			
			$subject = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_reset_temat']);
			$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

			$link_aktywacyjny = '<a href="'.$ustawienia['base_url'].'?akcja=activate&kod='.$wynik['kod_aktywacyjny'].'">'.$ustawienia['base_url'].'?akcja=activate&kod='.$wynik['kod_aktywacyjny'].'</a>';
			$ustawienia["email_reset_tresc"] = htmlspecialchars_decode($ustawienia["email_reset_tresc"]);
			$ustawienia['email_reset_tresc'] = str_replace("{tytul}",$ustawienia['tytul'],$ustawienia['email_reset_tresc']);
			$ustawienia['email_reset_tresc'] = str_replace("{link_aktywacyjny}",$link_aktywacyjny,$ustawienia['email_reset_tresc']);
			$ustawienia['email_reset_tresc'] = str_replace("{login}",$wynik['login'],$ustawienia['email_reset_tresc']);
			$ustawienia['email_reset_tresc'] = str_replace("{haslo}",$haslo,$ustawienia['email_reset_tresc']);
			
			$message = '<!doctype html><html lang="pl"><head><meta charset="utf-8"></head><body>'.$ustawienia['email_reset_tresc'].'</body></html>';
			
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
			
			header("Location: ".$tlumaczenia_linki['logowanie']."?reset");
		}else{
			$smarty->assign("email", $_POST['email']);
			$smarty->assign("blad", $tlumaczenia_teksty['email_nie_zarejestrowany']);
		}
	}
}

