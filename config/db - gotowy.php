<?php
$mysql_server = ""; 	// serwer i port bazy danych
$mysql_user = "";  		// użytkownik bazy danych
$mysql_pass = ""; 		// hasło bazy danych
$mysql_database = '' ;	// nazwa bazy danych
@mysql_connect($mysql_server, $mysql_user, $mysql_pass) or die("Nie można połączyć się z bazą danych");
mysql_query("SET NAMES utf8");
@mysql_select_db($mysql_database) or die("Nieprawidłowa nazwa bazy danych");
mysql_query("SET GLOBAL time_zone = 'Europe/Warsaw'");

function pobierz_ustawienia(){
	global $ustawienia;
	$q = mysql_query("select * from ustawienia limit 1");
	while($dane = mysql_fetch_array($q)){
		$dane["analytics"] = htmlspecialchars_decode($dane["analytics"]);
		$dane["adsense"] = htmlspecialchars_decode($dane["adsense"]);
		$dane["stopka"] = htmlspecialchars_decode($dane["stopka"]);
		$ustawienia=$dane;
	}
}
pobierz_ustawienia();

function pobierz_tlumaczenia_linki(){
	global $tlumaczenia_linki, $ustawienia;
	$q = mysql_query("select * from tlumaczenia_linki where jezyk='".$ustawienia['jezyk']."' limit 1");
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_linki=$dane;}
}
pobierz_tlumaczenia_linki();

function pobierz_tlumaczenia_teksty(){
	global $tlumaczenia_teksty, $ustawienia;
	$q = mysql_query("select * from tlumaczenia_teksty where jezyk='".$ustawienia['jezyk']."' limit 1");
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_teksty=$dane;}
}
pobierz_tlumaczenia_teksty();

