<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$q = mysql_query('select id, login, moderator, data, imie, adres, miasta, mapa from uzytkownicy where login = "'.filtruj($_GET['id']).'" and aktywny=1 limit 1');
while($dane = mysql_fetch_array($q)){$profil = $dane;}
if(isset($profil)){	
	$profil['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$profil['id'].'"'));
	$profil['ile_obrazkow_glowna'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$profil['id'].'" and glowna=1'));
	$profil['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where autor_id="'.$profil['id'].'"'));
	if($ustawienia['tworzenie']==1){
		$profil['ile_stworzono'] = mysql_num_rows(mysql_query('select id from stworzone where autor_id="'.$profil['id'].'"'));
	}
	$smarty->assign("profil", $profil);

	$title = $profil['login'].' - profil użytkownika - '.$ustawienia['tytul'];
	
	$limit_start = policz_strony($ustawienia['konto_ile_na_strone'], 'obrazki', 'autor_id="'.$profil['id'].'"');

	$q = mysql_query('select id, tytul, glowna, prosty_tytul, wybor_obrazka, url, miniaturka, glosy, data, kategoria from obrazki where autor_id="'.$profil['id'].'" order by data desc limit '.$limit_start.','.$ustawienia['konto_ile_na_strone'].'');
	while($dane = mysql_fetch_array($q)){
		$q2 = mysql_query('select nazwa, prosta_nazwa from kategorie where id="'.$dane['kategoria'].'" limit 1');
		while($dane2 = mysql_fetch_array($q2)){	
			$dane['nazwa'] = $dane2['nazwa'];
			$dane['prosta_nazwa'] = $dane2['prosta_nazwa'];
		}
		$dane['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where obrazek_id="'.$dane['id'].'"'));
		$profil_obrazki[] = $dane;
	}
	
	if(isset($profil_obrazki)){$smarty->assign("profil_obrazki", $profil_obrazki);}
	
	pobierz_kategorie();
	pobierz_boksy();
	pobierz_dane_do_boksow();
}else{
	include('model/404.php');
}

if(isset($_POST['captcha']) and isset($_POST['temat']) and isset($_POST['tresc']) and isset($_POST['email']) and isset($_POST['imie']) and isset($_POST['id'])){
	if($_POST['captcha']!=$_SESSION['captcha']){
		$smarty->assign("email_imie", $_POST['imie']);
		$smarty->assign("email_email", $_POST['email']);
		$smarty->assign("email_temat", $_POST['temat']);
		$smarty->assign("email_tresc", $_POST['tresc']);
		$smarty->assign("email_info", $tlumaczenia_teksty['blad_captcha']);
	}else{
		$q = mysql_query('select email from uzytkownicy where id = "'.$_POST['id'].'" and aktywny=1 limit 1');
		while($dane = mysql_fetch_array($q)){$email = $dane['email'];}
		if(isset($email)){
			
			$address = $email;
			
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
		}else{
			$smarty->assign("email_imie", $_POST['imie']);
			$smarty->assign("email_email", $_POST['email']);
			$smarty->assign("email_temat", $_POST['temat']);
			$smarty->assign("email_tresc", $_POST['tresc']);
			$smarty->assign("email_info", $tlumaczenia_teksty['blad_inny']);
		}
	}
}

