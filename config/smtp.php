<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

require_once 'libs/PHPMailer-master/PHPMailerAutoload.php';

$mail = new PHPMailer();
 
$mail->IsSMTP();        
$mail->CharSet = "utf-8";
$mail->From = $ustawienia['smtp_email']; 
$mail->FromName = $_SERVER['HTTP_HOST'];
$mail->Host = $ustawienia['smtp_host'];  
$mail->Mailer = "smtp";
$mail->Username = $ustawienia['smtp_uzytkownik'];
$mail->Password = $ustawienia['smtp_haslo'];
$mail->SMTPAuth = true;
$mail->Port = 587; 
$mail->SMTPSecure = 'tls'; 
$mail->IsHTML(true);

$mail->smtpConnect(
    array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
            "allow_self_signed" => true
        )
    )
);
 