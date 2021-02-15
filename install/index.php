<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(0);

ob_start();

if(phpversion()<5.4 or phpversion()>=7){
	die('Nieprawidlowa wersja PHP na serwerze. Obslugiwane: 5.4 - 5.6');
}

$install = true;

include('../config/db.php');

if(isset($ustawienia['base_url'])){
	header_remove();
	header('location: '.$ustawienia['base_url'].'/cms');
}

if (isset($_POST['url']) and isset($_POST['serwer']) and isset($_POST['port']) and isset($_POST['uzytkownik']) and isset($_POST['nazwa']) and isset($_POST['logincms']) and isset($_POST['haslocms'])){

	$connect = mysql_connect($_POST['serwer'].':'.$_POST['port'], $_POST['uzytkownik'], $_POST['haslo']);
	if (!$connect) {
		$error = "Błąd! Nie można połączyć z wybranym serwerem.";
	}else{
		$db_selected = @mysql_select_db($_POST['nazwa']);
		if (!$db_selected) {
			$error = "Błąd! Niewłaściwa nazwa bazy danych.";
		}else{
			$dir = '../config/db.php';
			if (!file_exists($dir) ) {
				fwrite($dir,'');
			}else{
				chmod($dir, 0777);
			}
 
			file_put_contents($dir, '<?php
$mysql_server = "'.$_POST['serwer'].':'.$_POST['port'].'";
$mysql_user = "'.$_POST['uzytkownik'].'"; 
$mysql_pass = "'.$_POST['haslo'].'"; 
$mysql_database = "'.$_POST['nazwa'].'";
@mysql_connect($mysql_server, $mysql_user, $mysql_pass) or die("Nie można połączyć się z bazą danych");
mysql_query("SET NAMES utf8");
@mysql_select_db($mysql_database) or die("Nieprawidłowa nazwa bazy danych");
mysql_query("SET GLOBAL time_zone = \'Europe/Warsaw\'");

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
	$q = mysql_query("select * from tlumaczenia_linki where jezyk=\'".$ustawienia[\'jezyk\']."\' limit 1");
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_linki=$dane;}
}
pobierz_tlumaczenia_linki();

function pobierz_tlumaczenia_teksty(){
	global $tlumaczenia_teksty, $ustawienia;
	$q = mysql_query("select * from tlumaczenia_teksty where jezyk=\'".$ustawienia[\'jezyk\']."\' limit 1");
	while($dane = mysql_fetch_assoc($q)){$tlumaczenia_teksty=$dane;}
}
pobierz_tlumaczenia_teksty();
?>');		
						
			mysql_query("SET NAMES utf8");
			mysql_query("CREATE TABLE IF NOT EXISTS `automatyzacja` (`id` int(1) NOT NULL AUTO_INCREMENT,  `wlacz` int(1) NOT NULL,  `wlacz_wszystkie` int(1) NOT NULL,  `wlacz_dni_wiecej` int(1) NOT NULL,  `dni_wiecej` int(3) NOT NULL,  `wlacz_dni_mniej` int(1) NOT NULL,  `dni_mniej` int(3) NOT NULL,  `wlacz_glosy` int(1) NOT NULL,  `glosy` int(11) NOT NULL,  `wlacz_komentarze` int(1) NOT NULL,  `komentarze` int(11) NOT NULL,  `wlacz_obrazki_inne_strony` int(1) NOT NULL,  `min_szerokosc` int(3) NOT NULL,  `min_wysokosc` int(3) NOT NULL,  `lista_stron` varchar(2048) COLLATE utf8_polish_ci NOT NULL,  `inne_strony_cel` int(1) NOT NULL,  `inne_strony_uzytkownik` int(11) NOT NULL,  `inne_strony_kategoria` int(11) NOT NULL, `generuj_sitemap` int(1) NOT NULL, `usun_bledne_obrazki` int(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1");
			mysql_query("CREATE TABLE IF NOT EXISTS `boksy` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `pozycja` int(11) NOT NULL,  `rodzaj` varchar(16) COLLATE utf8_polish_ci NOT NULL,  `ilosc` int(1) NOT NULL,  `tresc` text COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `cms` ( `id` int(11) NOT NULL AUTO_INCREMENT, `login` varchar(256) COLLATE utf8_polish_ci DEFAULT NULL,  `haslo` varchar(256) COLLATE utf8_polish_ci DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `cms_logi` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(256) COLLATE utf8_polish_ci DEFAULT NULL,  `zalogowal` int(11) DEFAULT NULL,  `data` datetime DEFAULT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `glosy` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `obrazek_id` int(11) NOT NULL,  `glos` int(1) NOT NULL,  `autor_id` int(11) NOT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `kategorie` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `nazwa` varchar(45) COLLATE utf8_polish_ci NOT NULL,  `prosta_nazwa` varchar(48) COLLATE utf8_polish_ci NOT NULL,  `kategoria_glowna` int(11) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `komentarze` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `autor_id` int(11) NOT NULL,  `obrazek_id` int(11) NOT NULL,  `tresc` varchar(1300) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE `komentarze_glosy` ( `id` INT NOT NULL AUTO_INCREMENT , `komentarz_id` INT NOT NULL , `glos` INT(1) NOT NULL , `autor_id` INT NOT NULL , `ip` VARCHAR(40) NOT NULL , `data` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
			mysql_query("CREATE TABLE IF NOT EXISTS `konkursy` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `wlaczony` int(1) NOT NULL,  `tytul` varchar(256) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(256) CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,  `opis` text CHARACTER SET utf8 COLLATE utf8_polish_ci NOT NULL,  `zwyciezca` int(11) NOT NULL,  `start` date NOT NULL,  `koniec` date NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `memy_obrazki` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,  `miniaturka` varchar(256) COLLATE utf8_unicode_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `obrazki` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `kategoria` int(11) NOT NULL,  `tagi` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `glowna` int(1) NOT NULL,  `tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `opis` text COLLATE utf8_polish_ci NOT NULL,  `wybor_obrazka` varchar(16) COLLATE utf8_polish_ci NOT NULL,  `url` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `miniaturka` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `mapa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `autor_id` int(11) NOT NULL,  `glosy` int(11) NOT NULL,  `data_glowna` datetime NOT NULL, `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `stworzone` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `opis` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `url` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `autor_id` int(11) NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `tagi` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `prosta_nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
			mysql_query("CREATE TABLE IF NOT EXISTS `ustawienia` (
  `id` int(11) NOT NULL,
  `base_url` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `keywords` varchar(512) COLLATE utf8_polish_ci NOT NULL,
  `description` text COLLATE utf8_polish_ci NOT NULL,
  `stopka_url` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stopka_nazwa` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `szablon` varchar(16) COLLATE utf8_polish_ci NOT NULL,
  `onas` mediumtext COLLATE utf8_polish_ci NOT NULL,
  `ile_na_strone` int(2) NOT NULL,
  `konto_ile_na_strone` int(2) NOT NULL,
  `facebook` varchar(256) COLLATE utf8_polish_ci NOT NULL,
  `analytics` text COLLATE utf8_polish_ci NOT NULL,
  `adsense` text COLLATE utf8_polish_ci NOT NULL,
  `regulamin` mediumtext COLLATE utf8_polish_ci NOT NULL,
  `polityka_prywatnosci` mediumtext COLLATE utf8_polish_ci NOT NULL,
  `mapa` int(1) NOT NULL,
  `limit_mapa` int(3) NOT NULL,
  `rozmiar_upload` int(5) NOT NULL,
  `logo_obrazek` int(1) NOT NULL,
  `logowanie_facebook` int(1) NOT NULL,
  `facebook_api` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `facebook_secret` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wylacz_niezalogowani` int(1) NOT NULL,
  `tworzenie` int(1) NOT NULL,
  `dodawaj_znak_wodny` int(1) NOT NULL,
  `glowna_pelne_zdjecia` int(1) NOT NULL,
  `reklama_co_obrazkow` int(2) NOT NULL,
  `logo_w_ramce` int(1) NOT NULL,
  `wybor_kolor_ramki` int(1) NOT NULL,
  `logo_w_ramce_tekst` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `logo_w_ramce_kolor` varchar(7) COLLATE utf8_polish_ci NOT NULL,
  `memy` int(1) NOT NULL,
  `domyslnie_mem` int(1) NOT NULL,
  `konkursy` int(1) NOT NULL,
  `udostepnij_fb` int(1) NOT NULL,
  `udostepnij_twitter` int(1) NOT NULL,
  `udostepnij_wykop` int(1) NOT NULL,
  `udostepnij_pinterest` int(1) NOT NULL,
  `udostepnij_google` int(1) NOT NULL,
  `smtp` int(1) NOT NULL,
  `smtp_email` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `smtp_host` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `smtp_uzytkownik` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `smtp_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mapa_uzytkownikow` int(1) NOT NULL,
  `jezyk` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `mapa_center` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `email_rejestracja_temat` varchar(256) COLLATE utf8_polish_ci NOT NULL,
  `email_rejestracja_tresc` text COLLATE utf8_polish_ci NOT NULL,
  `email_rejestracja_fb_temat` varchar(256) COLLATE utf8_polish_ci NOT NULL,
  `email_rejestracja_fb_tresc` text COLLATE utf8_polish_ci NOT NULL,
  `email_reset_temat` varchar(256) COLLATE utf8_polish_ci NOT NULL,
  `email_reset_tresc` text COLLATE utf8_polish_ci NOT NULL,
  `email_kontakt_temat` varchar(256) COLLATE utf8_polish_ci NOT NULL,
  `email_kontakt_tresc` text COLLATE utf8_polish_ci NOT NULL,
  `komentarze_glosy` int(1) NOT NULL,
  `komentarze_pokaz_najlepszy` int(1) NOT NULL,
  `komentarze_facebook` int(1) NOT NULL,
  `google_maps_api` VARCHAR(128) NOT NULL,
  `stopka` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
			mysql_query("CREATE TABLE IF NOT EXISTS `uzytkownicy` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `email` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `haslo` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `aktywny` int(1) NOT NULL,  `kod_aktywacyjny` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `moderator` int(1) NOT NULL,  `data` datetime NOT NULL,  `rejestracja_facebook` int(1) NOT NULL,  `imie` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `adres` varchar(256), `mapa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `miasta` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
			mysql_query("CREATE TABLE `tlumaczenia_linki` (
  `jezyk` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `podstawowy` int(1) NOT NULL,
  `rejestracja` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `logowanie` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `dodaj` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `stworz` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `kategoria` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `tag` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `poczekalnia` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `top` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `reset_hasla` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `konto` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `onas` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `regulamin` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `polityka_prywatnosci` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `profil` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `mapa` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `mapa_uzytkownikow` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `edycja` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `uzytkownicy` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `stworzone` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `konkursy` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `konkurs` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`jezyk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
		mysql_query("INSERT INTO `tlumaczenia_linki` (`jezyk`, `podstawowy`, `rejestracja`, `logowanie`, `dodaj`, `stworz`, `kategoria`, `tag`, `poczekalnia`, `top`, `reset_hasla`, `konto`, `onas`, `regulamin`, `polityka_prywatnosci`, `profil`, `mapa`, `mapa_uzytkownikow`, `edycja`, `uzytkownicy`, `stworzone`, `konkursy`, `konkurs`) VALUES
('angielski', 0, 'register', 'login', 'add_new', 'create_new', 'category', 'tag', 'waiting_area', 'top', 'reset_password', 'account', 'about_us', 'terms_of_use', 'privacy_policy', 'profile', 'map', 'users_map', 'edition', 'users', 'created', 'competition', 'competitions'),
('francuski', 0, 'enregistrement', 'identifiant', 'ajouter', 'creer_un', 'categorie', 'etiquette', 'salle_dattente', 'haut', 'reinitialiser_le_mot_de_passe', 'compte', 'a_propos_de_nous', 'reglements', 'politique_de_confidentialite', 'profil', 'carte', 'carte_utilisateurs', 'edition', 'membres', 'etabli', 'competitions', 'concours'),
('hiszpański', 0, 'registro', 'login', 'anadir', 'crear', 'categoria', 'etiqueta', 'sala_de_espera', 'superior', 'restablecer_su_contrasena', 'cuenta', 'quienes_somos', 'reglamentos', 'politica_de_privacidad', 'perfil', 'mapa', 'mapa_usuarios', 'edicion', 'usuarios', 'creado', 'competiciones', 'competencia'),
('niemiecki', 0, 'anmeldung', 'login', 'hinzufugen', 'schaffen', 'kategorie', 'etikett', 'wartezimmer', 'top', 'passwort_zurucksetzen', 'konto', 'uber_uns', 'vorschriften', 'datenschutz', 'profil', 'karte', 'karte_benutzer', 'ausgabe', 'benutzer', 'erstellt', 'wettbewerbe', 'wettbewerb'),
('polski', 1, 'rejestracja', 'logowanie', 'dodaj', 'stworz', 'kategoria', 'tag', 'poczekalnia', 'top', 'reset_hasla', 'konto', 'onas', 'regulamin', 'polityka_prywatnosci', 'profil', 'mapa', 'mapa_uzytkownikow', 'edycja', 'uzytkownicy', 'stworzone', 'konkursy', 'konkurs'),
('rosyjski', 0, 'registraciya', 'vhod', 'dobavit', 'sozdat', 'kategoriya', 'teg', 'pesochnica', 'luchshie', 'sbros_parolya', 'akkaunt', 'onas', 'regulyamin', 'politika_konfidencialnosti', 'profil', 'karta', 'karta_polzovatelej', 'redaktirovanie', 'polzovateli', 'sozdannye', 'konkursy', 'konkurs'),
('włoski', 0, 'registrazione', 'accesso', 'aggiungere', 'creare_un', 'categoria', 'etichetta', 'sala_dattesa', 'top', 'reimpostare_la_password', 'conto', 'chi_siamo', 'normativa', 'tutela_della_privacy', 'profilo', 'mappa', 'gli_utenti_della_mappa', 'edizione', 'utenti', 'creato', 'concorsi', 'concorso');");
		mysql_query("CREATE TABLE `tlumaczenia_teksty` (
  `jezyk` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `dodaj_nowy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `udalo_sie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zobacz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tytul` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `opis` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `film_youtube` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `film_vimeo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `film_dailymotion` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazek_z_dysku` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazek_z_internetu` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kategoria` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tagi_po_przecinku` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodaj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zaznacz_lokalizacje` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `edytuj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zapisz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `poczekalnia` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `autor` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `data` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `komentarzy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `musisz_zalogowany` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `strona` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_dodano` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `aktywny` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zakonczony` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `data_start` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `data_koniec` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `konkurs_nie_znaleziono` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `konkursy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `konkurs` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zwyciezca` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `status` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `konto` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `login` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `statystyki` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `data_rejestracji` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodanych_obrazkow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazkow_na_glownej` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodanych_komentarzy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworzonych_obrazkow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `twoje_obrazki` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `glosow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `usun` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_znaleziono` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dane_osobowe` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `imie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `adres` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `miasta` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zmiana_hasla` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stare_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nowe_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `powtorz_nowe_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zmien_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zaloguj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `login_lub_email` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zaloguj_przez_fb` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `reset_hasla` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `rejestracja` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wroc_do_serwisu` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `onas` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_top` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_dodaj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_stworz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_poczekalnia` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_mapa_obiektow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_mapa_uzytkownikow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_konkursy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_konto` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_wyloguj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_rejestracja` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_logowanie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `losowo_wybrane` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stopka_opis` varchar(512) COLLATE utf8_polish_ci NOT NULL,
  `mapa_obiektow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mapa_obiektow_opis` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `nie_dodano_lokalizacji` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mapa_uzytkownikow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mapa_uzytkownikow_opis` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `na_glowna` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_mozna_zaladowac` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `komentarze` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `usun_komentarz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_dodano_komentarzy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `komentarz_zalogowany` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_znaleziono_obrazka` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tresc_komentarza` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `captcha` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kontakt` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kontakt_info` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `temat` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wiadomosc` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wyslij` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `polityka_prywatnosci` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `moderator` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kontakt_z_uzytkownikiem` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kontakt_z_uzytkownikiem_opis` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nie_znaleziono_uzytkownika` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `regulamin` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `powtorz_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `akceptuje` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `oraz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `polityke_prywatnosci` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodatkowe_informacje` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazkow_i_filmow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `w_poczekalni` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kategorii` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tagow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworzonych` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `uzytkownikow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `najlepsze` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `najnowsze` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `najnowsze_komentarze` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wyszukiwarka` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `szukaj` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wyszukiwarka_uzytkownikow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tag` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworz_obrazek` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zapisz_obrazek` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworz_nowy_obrazek` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kolor_w_formacie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodaj_obrazek_z_dysku` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `typ` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazek` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mem` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kolor_tla` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kolor_ramki` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `podglad` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `stworzone` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `obrazkow` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `na_glownej` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nic_nie_znaleziono` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mapa_strony` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_onas` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_kategorie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `tagi` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `top` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `logowanie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `uzytkownicy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `profil` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dodano` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `kategorie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `na_pewno_usunac` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `blad_typ_pliku` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `blad_plik_z_dysku` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_url_nie_istnieje` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_youtube` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_vimeo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_dailymotion` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_inny` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_dodales_juz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_kategoria` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_opis` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zaloguj_sie` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `zapisano` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dane_zaktualizowane` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_hasla_rozne` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_stare_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_dlugie_haslo` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_wszystkie_pola` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `konto_nie_aktywowane` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `dane_nieprawidlowe` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `potwierdz_link_aktywacyjny` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `nowe_haslo_wyslane` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `blad_captcha` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_dlugi_komentarz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `wiadomosc_wyslana` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_login` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_login_zajety` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_email` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_email_zajety` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `pole_obowiazkowe` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `aktywacja_info` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `aktywacja_blad` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `email_nie_zarejestrowany` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `cookies_tekst` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `cookies_zamknij` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `usunac_komentarz` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mocne` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `slabe` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_glowna` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `nastepna_strona` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_stworzone` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `menu_uzytkownicy` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `mem_obrazek` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `blad_404` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `komentarz_niezalogowany` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `komentarz_glos_tak` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `komentarz_glos_nie` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `najlepszy_komentarz` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`jezyk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
		mysql_query("INSERT INTO `tlumaczenia_teksty` (`jezyk`, `dodaj_nowy`, `udalo_sie`, `zobacz`, `tytul`, `opis`, `film_youtube`, `film_vimeo`, `film_dailymotion`, `obrazek_z_dysku`, `obrazek_z_internetu`, `kategoria`, `tagi_po_przecinku`, `dodaj`, `zaznacz_lokalizacje`, `edytuj`, `zapisz`, `blad`, `poczekalnia`, `autor`, `data`, `komentarzy`, `musisz_zalogowany`, `strona`, `nie_dodano`, `aktywny`, `zakonczony`, `data_start`, `data_koniec`, `konkurs_nie_znaleziono`, `konkursy`, `konkurs`, `zwyciezca`, `status`, `konto`, `login`, `email`, `statystyki`, `data_rejestracji`, `dodanych_obrazkow`, `obrazkow_na_glownej`, `dodanych_komentarzy`, `stworzonych_obrazkow`, `twoje_obrazki`, `glosow`, `usun`, `nie_znaleziono`, `dane_osobowe`, `imie`, `adres`, `miasta`, `zmiana_hasla`, `stare_haslo`, `nowe_haslo`, `powtorz_nowe_haslo`, `zmien_haslo`, `zaloguj`, `login_lub_email`, `haslo`, `zaloguj_przez_fb`, `reset_hasla`, `rejestracja`, `wroc_do_serwisu`, `onas`, `menu_top`, `menu_dodaj`, `menu_stworz`, `menu_poczekalnia`, `menu_mapa_obiektow`, `menu_mapa_uzytkownikow`, `menu_konkursy`, `menu_konto`, `menu_wyloguj`, `menu_rejestracja`, `menu_logowanie`, `losowo_wybrane`, `stopka_opis`, `mapa_obiektow`, `mapa_obiektow_opis`, `nie_dodano_lokalizacji`, `mapa_uzytkownikow`, `mapa_uzytkownikow_opis`, `na_glowna`, `nie_mozna_zaladowac`, `komentarze`, `usun_komentarz`, `nie_dodano_komentarzy`, `komentarz_zalogowany`, `nie_znaleziono_obrazka`, `tresc_komentarza`, `captcha`, `kontakt`, `kontakt_info`, `temat`, `wiadomosc`, `wyslij`, `polityka_prywatnosci`, `moderator`, `kontakt_z_uzytkownikiem`, `kontakt_z_uzytkownikiem_opis`, `nie_znaleziono_uzytkownika`, `regulamin`, `powtorz_haslo`, `akceptuje`, `oraz`, `polityke_prywatnosci`, `dodatkowe_informacje`, `obrazkow_i_filmow`, `w_poczekalni`, `kategorii`, `tagow`, `stworzonych`, `uzytkownikow`, `najlepsze`, `najnowsze`, `najnowsze_komentarze`, `wyszukiwarka`, `szukaj`, `wyszukiwarka_uzytkownikow`, `tag`, `stworz_obrazek`, `zapisz_obrazek`, `stworz_nowy_obrazek`, `kolor_w_formacie`, `dodaj_obrazek_z_dysku`, `typ`, `obrazek`, `mem`, `kolor_tla`, `kolor_ramki`, `podglad`, `stworz`, `stworzone`, `obrazkow`, `na_glownej`, `nic_nie_znaleziono`, `mapa_strony`, `menu_onas`, `menu_kategorie`, `tagi`, `top`, `logowanie`, `uzytkownicy`, `profil`, `dodano`, `kategorie`, `na_pewno_usunac`, `blad_typ_pliku`, `blad_plik_z_dysku`, `blad_url_nie_istnieje`, `blad_youtube`, `blad_vimeo`, `blad_dailymotion`, `blad_inny`, `blad_dodales_juz`, `blad_kategoria`, `blad_opis`, `zaloguj_sie`, `zapisano`, `dane_zaktualizowane`, `blad_hasla_rozne`, `blad_stare_haslo`, `blad_dlugie_haslo`, `blad_wszystkie_pola`, `konto_nie_aktywowane`, `dane_nieprawidlowe`, `potwierdz_link_aktywacyjny`, `nowe_haslo_wyslane`, `blad_captcha`, `blad_dlugi_komentarz`, `wiadomosc_wyslana`, `blad_login`, `blad_login_zajety`, `blad_email`, `blad_email_zajety`, `pole_obowiazkowe`, `aktywacja_info`, `aktywacja_blad`, `email_nie_zarejestrowany`, `cookies_tekst`, `cookies_zamknij`, `usunac_komentarz`, `mocne`, `slabe`, `menu_glowna`, `nastepna_strona`, `menu_stworzone`, `menu_uzytkownicy`, `mem_obrazek`, `blad_404`, `komentarz_glos_tak`, `komentarz_glos_nie`, `komentarz_niezalogowany`, `najlepszy_komentarz`) VALUES
('angielski', 'Add New', 'Success', 'SEE', 'Title', 'Description', 'Video from Youtube', 'Video from Vimeo', 'Video from Dailymotion', 'Image from computer', 'Image from Internet', 'Category', 'Tags (with decimals)', 'Add', 'You can mark location on the map', 'Edit image', 'Save', 'An error occurred!', 'Waiting Area', 'Author', 'Date', 'Comments', 'You must be logged in to see the picture!', 'Page', 'You have not added any images...', 'Active', 'Completed', 'Start date', 'Date of completion', 'Not Found Competition...', 'Competitions', 'Competition', 'Winner', 'Status', 'Account', 'Username', 'E-mail', 'Statistics', 'Date of registration', 'Added images', 'Images on the home page', 'Added comments', 'Created pictures', 'Your images and videos', 'Votes', 'Remove', 'Nothing found', 'Personal data', 'First name and last name', 'Address', 'Cities filming', 'Password change', 'Old password', 'New password', 'Repeat new password', 'Change password', 'Sign In', 'Username or e-mail', 'Password', 'Login with Facebook', 'Reset your password', 'Register', 'Return to the site', 'About us', 'TOP', 'ADD NEW', 'CREATE', 'WAITING AREA', 'OBJECTS MAP', 'USERS MAP', 'COMPETITIONS', 'ACCOUNT', 'LOG OUT', 'REGISTER', 'LOGIN', 'Random', 'All pictures and videos on the website are added by users of the service and its owner does not take any responsibility for them.', 'Objects map', 'Here you can find the objects by their location on the map!', 'You have not added any location', 'Users map', 'Here you can find users by their location on the map', 'On the home page', 'You can not load image.', 'Comments', 'Delete comment', 'No comments added yet. Add the first!', 'To post a comment you must be logged.', 'Not found picture...', 'Your comment', 'Captcha', 'Contact', 'If you have questions or concerns, please contact us -', 'Message subject', 'Message', 'Send', 'Privacy Policy', 'Moderator', 'Contact with user', 'Here you can send a message to the user.', 'User with the specified login name not found', 'Terms of Use', 'Repeat', 'I accept', 'and', 'Privacy Policy', 'Add additional information', 'Pictures and videos', 'In the waiting area', 'Categories', 'Tags', 'Created', 'Users', 'Top', 'New', 'Latest comments', 'Search', 'Search', 'Search users', 'Tag', 'Create your image', 'Save image', 'Create a new image', 'The color in hexadecimal format', 'Add an image from computer', 'Type', 'Image', 'Mem', 'Background color', 'Frame color', 'Preview', 'Create', 'Created', 'Images', 'On the home page', 'Nothing found', 'Site map', 'ABOUT US', 'CATEGORIES', 'Tags', 'Top', 'Login', 'Users', 'Profile', 'Added', 'Categories', 'Are you sure you delete an object? It will also remove all comments.', 'Error: invalid file type, or file for the large size.', 'Error: add the correct file from the computer.', 'Error: The URL does not exist.', 'Error: enter the address of a video from Youtube.', 'Error: enter the address of a video from Vimeo.', 'Error: enter the address of a video from Dailymotion.', 'Unidentified error.', 'You have already added similar picture or video.', 'Invalid number of category.', 'Too long description!', 'Sign In!', 'Saved!', 'The data has been correctly updated.', 'The passwords are different.', 'Enter the correct old password.', 'The password is too long!', 'Fill in all the fields.', 'Account has not been activated yet.', 'None Supplied.', 'Account has been created, confirm your e-mail address by clicking on the activation link.', 'The new password has been sent to your e-mail address.', 'Improperly prescribed code.', 'Comment is too long!', 'Message was sent', 'Incorrect username', 'Login is already in use.', 'Incorrect e-mail address.', 'E-mail address already exists in the database.', 'This field is mandatory.', 'Account has been activated, now you can login.', 'Invalid activation code or the account has already been activated.', 'E-mail address is not registered in the database.', 'We have placed cookies on your computer to help make this website better.', 'I agree', 'Are you sure you delete a comment?', 'Like it', 'Don\'t like it', 'HOME', 'Next page', 'CREATED', 'USERS', 'Mem from picture', 'Error 404. Page does not exist.', 'YES', 'NO', 'You must be logged in to be able to cast your vote!', 'Best comment:'),
('francuski', 'Ajouter un nouveau', 'Il a travaillé', 'VOIR', 'Titre', 'Description', 'Film Youtube', 'Film Vimeo', 'Film Dailymotion', 'Disque Image', 'Image de l\'Internet', 'Catégorie', 'Tags (décimal)', 'Ajouter', 'Vous pouvez marquer des emplacements sur la carte', 'Modifier l\'image', 'Sauvegarder', 'Une erreur est survenue!', 'Salle d\'attente', 'Auteur', 'Date', 'Commentaires', 'Vous devez être connecté pour voir la photo!', 'Page', 'Vous ne l\'avez pas ajouté d\'images ...', 'Actif', 'Terminé', 'Date de début', 'Date d\'achèvement', 'Pas trouvé la compétition ...', 'Compétitions', 'Concours', 'Gagnant', 'Statut', 'Compte', 'Login', 'Courriel', 'Statistiques', 'Date d\'inscription', 'Images ajoutées', 'Images sur la principale', 'Commentaires ajoutés', 'Images créées', 'Vos photos et vidéos', 'Votes', 'Effacer', 'Rien trouvé', 'Données personnelles', 'Nom', 'Adresse', 'Villes tournage', 'Changer votre mot de passe', 'Ancien mot de passe', 'Nouveau mot de passe', 'Répéter nouveau mot de passe', 'Changez votre mot de passe', 'Connectez-vous', 'Connexion ou e-mail', 'Mot de passe', 'Connectez-vous avec Facebook', 'Votre mot de passe', 'Enregistrement', 'Retour au site', 'A propos de nous', 'TOP', 'AJOUTER', 'CRÉER', 'SALLE D\'ATTENTE', 'CARTE DES OBJETS', 'CARTE UTILISATEURS', 'COMPETITIONS', 'COMPTE', 'LOG OUT', 'ENREGISTREMENT', 'LOGIN', 'Aléatoire', 'Toutes les photos et vidéos sur le site sont ajoutés par les utilisateurs et son propriétaire ne prend aucune responsabilité pour eux.', 'Objets carte', 'Ici vous pouvez trouver les objets en fonction de leur emplacement sur la carte!', 'Non ajouté emplacement', 'Carte des utilisateurs', 'Ici vous pouvez trouver des utilisateurs par leur emplacement sur la carte', 'La principale', 'Vous ne pouvez pas charger l\'image.', 'Commentaires', 'Effacer un commentaire', 'Aucun commentaire n\'a encore été ajouté. Ajouter le premier!', 'Pour ajouter un commentaire, vous devez être connecté.', 'Pas trouvé photo ...', 'Commentaire', 'Captcha', 'Contact', 'Si vous avez des questions ou des préoccupations, s\'il vous plaît contactez-nous -', 'Message objet', 'Message', 'Envoyer', 'Politique de confidentialité', 'Modérateur', 'Contacter le propriétaire', 'Ici, vous pouvez envoyer un message à l\'utilisateur.', 'Aucun utilisateur n\'a été trouvé avec le nom de connexion spécifié', 'Règlements', 'Répétition', 'Accepte', 'et', 'Politique de confidentialité', 'Fournir des informations supplémentaires', 'Vos photos et vidéos', 'Dans la salle d\'attente', 'Catégorie', 'Étiquettes', 'Établi', 'Membres', 'Meilleur', 'Récent', 'Commentaires récents', 'Recherche', 'Recherche', 'Les utilisateurs des moteurs de recherche', 'Étiquette', 'Créer votre propre image', 'Enregistrer l\'image', 'Créer une nouvelle image', 'La couleur au format hexadécimal', 'Ajouter une image à partir du disque', 'Type', 'Image', 'Mem', 'Couleur de fond', 'Couleur du cadre', 'Avant-première', 'Créer', 'Établi', 'Photos', 'Sur la route principale', 'Rien trouvé', 'Plan du site', 'QUI SOMMES-NOUS', 'CATEGORIE', 'Étiquettes', 'Haut', 'Connectez-vous', 'Membres', 'Profil', 'Ajouté', 'Catégories', 'Etes-vous sûr de supprimer un objet? Ils seront également supprimés tous les commentaires.', 'Erreur: mauvais type de fichier, ou le fichier pour la grande taille.', 'Erreur: ajouter le fichier approprié à partir du disque.', 'Erreur: n\'existe pas l\'URL spécifiée.', 'Erreur: entrez l\'adresse d\'un film à partir de Youtube.', 'Erreur: entrez l\'adresse du site vidéo Vimeo.', 'Erreur: entrez l\'adresse du service vidéo Dailymotion.', 'Erreur non identifiée.', 'Vous avez déjà ajouté une photo ou une vidéo similaire.', 'Nombre incorrect de catégories.', 'Pour une description longue!', 'S\'il vous plaît Identifiez-vous!', 'Saved!', 'Les données ont été mis à jour correctement.', 'Les mots de passe sont différents.', 'Fournir ancien mot de passe approprié.', 'Le mot de passe est trop long!', 'Remplissez tous les champs.', 'Le compte n\'a pas encore été activé.', 'Non saisi.', 'Le compte a été mis en place, confirmer votre adresse e-mail en cliquant sur le lien d\'activation.', 'Le nouveau mot de passe a été envoyé à votre adresse e-mail.', 'Improperly stockée code.', 'Le commentaire est trop long!', 'Le message a été envoyé', 'Connexion non valide', 'Connectez-vous est déjà pris.', 'Invalid adresse de messagerie électronique.', 'E-mail existe déjà dans la base de données.', 'Ce champ est obligatoire.', 'Le compte a été activé, vous pouvez maintenant vous connecter.', 'Code d\'activation incorrecte ou le compte a déjà été activé.', 'Adresse e-mail est pas enregistrée dans la base de données.', 'Ce site utilise des cookies (cookies), de sorte que notre service peut mieux fonctionner.', 'Je comprends', 'Voulez-vous vraiment supprimer votre commentaire?', 'Fort', 'Faiblesses', 'ACCUEIL', 'Page suivante', 'CRÉE', 'UTILISATEURS', 'Mem de la photo', '404 page d\'erreur n\'existe pas.', 'OUI', 'NON', 'Vous devez être connecté pour pouvoir voter!', 'Meilleur commentaire:'),
('hiszpański', 'Añadir nuevo', 'Funcionó', 'VER', 'Título', 'Descripción', 'Youtube Video', 'Vimeo Video', 'Dailymotion Video', 'Foto del disco', 'Imagen del Internet', 'Categoría', 'Etiquetas (decimal)', 'Añadir', 'Puede marcar ubicaciones en el mapa', 'Edición de imagen', 'Guardar', 'Se produjo un error!', 'Sala de espera', 'Autor', 'Fecha', 'Comentarios', 'Tienes que iniciar sesión para ver la imagen!', 'Página', 'Usted no ha agregado ninguna imagen ...', 'Vivo', 'Terminado', 'Fecha de inicio', 'Fecha de finalización', 'No se ha encontrado competencia...', 'Competiciones', 'Competencia', 'Ganador', 'Estado', 'Cuenta', 'Login', 'E-mail', 'Estadísticas', 'Fecha de registro', 'Añadido imágenes', 'Las imágenes en el inicio', 'Añadidos comentarios', 'Creado fotos', 'Sus fotos y videos', 'Votos', 'Eliminar', 'Encontrado nada', 'Datos personales', 'Nombre y apellidos', 'Dirección de la casa', 'Ciudades rodaje', 'Cambie su contraseña', 'Antigua contraseña', 'Nueva contraseña', 'Repita su nueva contraseña', 'Cambie su contraseña', 'Regístrate', 'Nombre de usuario o correo electrónico', 'Contraseña', 'Ingresar con Facebook', 'Restablecer su contraseña', 'Registro', 'Volver al sitio', 'Quiénes somos', 'TOP', 'AÑADIR', 'CREAR', 'SALA DE ESPERA', 'EDIFICIOS MAP', 'USUARIOS MAP', 'CONCURSOS', 'CUENTA', 'SALIR', 'REGISTRO', 'LOGIN', 'Random', 'Todas las fotos y videos en el sitio web son agregados por los usuarios del servicio y su dueño no toma ninguna responsabilidad por ellos.', 'Objetos mapa', 'Aquí podrá encontrar los objetos en función de su ubicación en el mapa!', 'No ha agregado cualquier ubicación', 'Mapa Usuario', 'Aquí usted puede encontrar los usuarios por su ubicación en el mapa', 'En la inicio', 'No se puede cargar la imagen.', 'Comentarios', 'Eliminar comentario', 'No hay comentarios todavía añaden. Añadir el primero!', 'Para publicar un comentario tienes que estar registrado.', 'No se encuentra la imagen...', 'Tu comentario', 'Captcha', 'Contacto', 'Si usted tiene preguntas o preocupaciones, por favor póngase en contacto con nosotros -', 'Asunto del mensaje', 'Mensaje', 'Enviar', 'Política de privacidad', 'Moderador', 'Contacto con el usuario', 'Aquí usted puede enviar un mensaje al usuario.', 'Usuario no encontrado con el nombre de inicio de sesión especificado', 'Términos y Condiciones', 'Repita', 'Acepto', 'y', 'Política de privacidad', 'Proporcione información adicional', 'Fotos y videos', 'En la sala de espera', 'Categorías', 'Etiquetas', 'Creado', 'Usuarios', 'Top', 'Último', 'Comentarios recientes', 'Búsqueda', 'Búsqueda', 'Los usuarios de la búsqueda', 'Etiqueta', 'Crear su imagen', 'Guardar imagen', 'Crear una nueva imagen', 'El color en formato hexadecimal', 'Añadir una imagen de disco', 'Tipos', 'Imagen', 'Mem', 'Color de fondo', 'Color de la montura', 'Vista previa', 'Crear', 'Creado', 'Imagen', 'En la inicio', 'Encontrado nada', 'Mapa del sitio', 'SOBRE NOSOTROS', 'CATEGORÍAS', 'Etiquetas', 'Top', 'Login', 'Usuarios', 'Perfilar', 'Añadido', 'Categorías', '¿Seguro de que elimina un objeto? Además, eliminará todos los comentarios.', 'Error: Tipo de archivo no válido, o un archivo de gran tamaño.', 'Error: añadir el archivo correspondiente desde el disco.', 'Error: La URL no existe.', 'Error: introducir la dirección de una película de Youtube.', 'Error: introducir la dirección de una película de Vimeo.', 'Error: introducir la dirección de una película de Dailymotion.', 'Error no identificado.', 'Usted ya ha añadido la imagen o el vídeo similar.', 'Número no válido de categorías.', 'Para una descripción más larga!', '¡Identifícate!', 'Guardado!', 'Los datos se han actualizado correctamente.', 'Dadas las contraseñas son diferentes.', 'Introduzca la contraseña antigua correcta.', 'La contraseña es demasiado largo!', 'Rellene todos los campos.', 'Cuenta no se ha activado todavía.', 'Suministrado ninguno.', 'Cuenta Se ha establecido, confirme su dirección de correo electrónico haciendo clic en el enlace de activación.', 'La nueva contraseña ha sido enviada a su dirección de correo electrónico.', 'Incorrectamente prescrito código.', 'Comentario es demasiado largo!', 'El mensaje fue enviado', 'Inicio de sesión no válido', 'Ingresar ya está en uso.', 'Dirección de correo electrónico no es válida.', 'E-mail ya existe en la base de datos.', 'Este campo es obligatorio.', 'Cuenta ha sido activada, Ahora puede iniciar sesión.', 'Código de activación no válido o la cuenta ya ha sido activada.', 'E-mail no se ha registrado en la base de datos.', 'Este sitio utiliza galletas (cookies), por lo que nuestro servicio puede funcionar mejor.', 'Entiendo', '¿Seguro de que elimina un comentario?', 'Potente', 'Debilidades', 'INICIO', 'Página siguiente', 'CREADO', 'USUARIOS', 'Mem de cuadro', 'Error 404. Página no existe.', 'SI', 'NO', 'Usted debe estar conectado para poder emitir su voto!', 'El mejor comentario:'),
('niemiecki', 'Neuen', 'Es funktionierte', 'SEHEN', 'Titel', 'Beschreibung', 'Film Youtube', 'Film Vimeo', 'Film Dailymotion', 'Bildplatte', 'Bild des Internets', 'Kategorie', 'Schlagworte (dezimal)', 'Hinzufügen', 'Sie können Orte auf der Karte markieren', 'Bild bearbeiten', 'Speichern', 'Ein Fehler ist aufgetreten!', 'Wartezimmer', 'Autor', 'Datum', 'Kommentare', 'Sie müssen angemeldet sein, um das Bild zu sehen!', 'Seite', 'Sie haben keine Bilder hinzugefügt ...', 'Aktiv', 'Fertiggestellt', 'Startdatum', 'Datum der Fertigstellung', 'Nicht die Konkurrenz gefunden ...', 'Wettbewerbe', 'Wettbewerb', 'Gewinner', 'Status', 'Konto', 'Login', 'E-Mail', 'Statistiken', 'Datum der Registrierung', 'Bilder hinzugefügt', 'Die Bilder auf dem Haupt', 'Hinzugefügt Kommentare', 'Erstellt Bilder', 'Ihre Bilder und Video', 'Stimmen', 'Löschen', 'Nichts gefunden', 'Personenbezogenen Daten', 'Name', 'Adresse', 'Städte filmen', 'Ihr Passwort ändern', 'Altes Passwort', 'Neues Passwort', 'Neues Passwort wiederholen', 'Ändern Sie Ihr Passwort', 'Einloggen', 'Anmelden oder E-Mail', 'Kennwort', 'Anmeldung mit Facebook', 'Ihr Passwort zurücksetzen', 'Anmeldung', 'Zurück zur Website', 'Über uns', 'TOP', 'HINZUFÜGEN', 'SCHAFFEN', 'WARTERAUM', 'MAP OBJEKTE', 'MAP NUTZER', 'WETTBEWERBE', 'KONTO', 'LOG OUT', 'ANMELDUNG', 'LOGIN', 'Zufällig', 'Alle Bilder und Videos auf der Website hinzugefügt werden durch die Nutzer und seinem Besitzer übernimmt keine Verantwortung für sie.', 'Kartenobjekte', 'Hier können Sie die Objekte entsprechend ihrer Lage auf der Karte finden!', 'Nicht hinzugefügt jeden Ort', 'Karte Benutzer', 'Hier können Sie die Benutzer von ihrem Standort auf der Karte finden', 'Die Haupt', 'Sie können nicht Bild laden.', 'Kommentare', 'Kommentar löschen', 'Noch keine Kommentare hinzugefügt. Fügen Sie der Erste!', 'So fügen Sie einen Kommentar, den Sie müssen angemeldet sein.', 'Kein Bild gefunden ...', 'Kommentar', 'Captcha', 'Kontakt', 'Wenn Sie Fragen oder Bedenken haben, kontaktieren Sie uns bitte -', 'Nachricht Betreff', 'Nachricht', 'Senden', 'Datenschutz', 'Moderator', 'Vermieter kontaktieren', 'Hier können Sie eine Nachricht an den Benutzer senden.', 'Kein Benutzer mit dem angegebenen Benutzernamen gefunden', 'Vorschriften', 'Wiederholung', 'Akzeptiert', 'und', 'Datenschutz', 'Geben Sie zusätzliche Informationen', 'Bilder und Videos', 'Im Wartezimmer', 'Kategorie', 'Tags', 'Erstellt', 'Benutzer', 'Beste', 'Kürzlich', 'Neueste Kommentare', 'Suche', 'Suche', 'Suchmaschinen-Nutzer', 'Etikett', 'Erstellen Sie Ihr eigenes Bild', 'Bild speichern', 'Erstellen Sie ein neues Bild', 'Die Farbe im Hexadezimal-Format', 'Fügen Sie ein Bild von der Festplatte', 'Typ', 'Bild', 'Mem', 'Hintergrundfarbe', 'Rahmenfarbe', 'Vorschau', 'Schaffen', 'Erstellt', 'Bilder', 'Auf der Haupt', 'Nichts gefunden', 'Sitemap', 'ÜBER UNS', 'KATEGORIE', 'Tagi', 'Top', 'Einloggen', 'Mitglieder', 'Profil', 'Zusätzlich', 'Kategorien', 'Sind Sie sicher, dass Sie ein Objekt zu löschen? Sie werden auch alle Kommentare gelöscht werden.', 'Fehler: falsche Dateityp oder Datei für die große Größe.', 'Fehler: fügen Sie die entsprechende Datei von der Festplatte .', 'Fehler: Die angegebene URL nicht existiert.', 'Fehler: Geben Sie die Adresse eines Films von Youtube.', 'Fehler: Geben Sie die Adresse des Video-Website Vimeo.', 'Fehler: Geben Sie die Adresse des Videodienstes Dailymotion .', 'Nicht identifizierte Fehler.', 'Sie haben bereits ein ähnliches Bild oder Video aufgenommen.', 'Ungültige Anzahl von Kategorien.', 'Für eine lange Beschreibung!', 'Einloggen!', 'Gespeichert!', 'Die Daten wurden korrekt aktualisiert.', 'Die Passwörter sind unterschiedlich.', 'Auf einen guten altes Passwort ein.', 'Das Passwort ist zu lang!', 'Füllen Sie alle Felder aus.', 'Das Konto wurde noch nicht aktiviert.', 'Nicht verfügbar.', 'Konto wurde eingerichtet, bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf den Aktivierungslink klicken.', 'Das neue Passwort wurde an Ihre E-Mail versendet.', 'Unsachgemäß Code gespeichert.', 'Kommentar ist zu lang!', 'Die Nachricht wurde gesendet', 'Ungültige Login', 'Anmeldung ist bereits vergeben.', 'Ungültige E-Mail-Adresse ein.', 'E-Mail existiert bereits in der Datenbank.', 'Dieses Feld ist obligatorisch.', 'Konto aktiviert wurde, können Sie sich jetzt einloggen.', 'Falscher Aktivierungscode oder das Konto wurde bereits aktiviert.', 'E-Mail-Adresse ist nicht in der Datenbank registriert.', 'Diese Seite benutzt Cookies (Cookies), so dass unser Service besser arbeiten kann.', 'Ich verstehe', 'Wollen Sie wirklich Ihr Kommentar zu löschen?', 'Stark', 'Schwächen', 'HOME', 'Nächste Seite', 'ERSTELLT', 'NUTZER', 'Mem von Bild', '404 Fehlerseite existiert nicht.', 'JA', 'NEIN', 'Sie müssen eingeloggt sein, um deine Stimme abgeben!', 'Bester Kommentar:'),
('polski', 'Dodaj nowy', 'Udało się', 'ZOBACZ', 'Tytuł', 'Opis', 'Film z Youtube', 'Film z Vimeo', 'Film z Dailymotion', 'Obrazek z dysku', 'Obrazek z Internetu', 'Kategoria', 'Tagi (po przecinku)', 'Dodaj', 'Możesz zaznaczyć lokalizacje na mapie', 'Edytuj obrazek', 'Zapisz', 'Wystąpił błąd!', 'Poczekalnia', 'Autor', 'Data', 'Komentarzy', 'Musisz się zalogować aby zobaczyć obrazek!', 'Strona', 'Nie dodano żadnych obrazków...', 'Aktywny', 'Zakończony', 'Data rozpoczęcia', 'Data zakończenia', 'Nie znaleziono konkursu...', 'Konkursy', 'Konkurs', 'Zwycięzca', 'Status', 'Konto', 'Login', 'Email', 'Statystyki', 'Data rejestracji', 'Dodanych obrazków', 'Obrazków na głównej', 'Dodanych komentarzy', 'Stworzonych obrazków', 'Twoje obrazki i video', 'Głosów', 'Usuń', 'Nic nie znaleziono', 'Dane osobowe', 'Imię i nazwisko', 'Adres zamieszkania', 'Miasta do filmowania', 'Zmiana hasła', 'Stare hasło', 'Nowe hasło', 'Powtórz nowe hasło', 'Zmień hasło', 'Zaloguj się', 'Login lub email', 'Hasło', 'Zaloguj się przez Facebook', 'Reset hasła', 'Rejestracja', 'Wróć do serwisu', 'O nas', 'TOP', 'DODAJ', 'STWÓRZ', 'POCZEKALNIA', 'MAPA OBIEKTÓW', 'MAPA UŻYTKOWNIKÓW', 'KONKURSY', 'KONTO', 'WYLOGUJ', 'REJESTRACJA', 'LOGOWANIE', 'Losowo wybrane', 'Wszelkie obrazki i filmy na stronie są dodawane przez użytkowników serwisu i jego właściciel nie bierze za nie odpowiedzialności.', 'Mapa obiektów', 'Tutaj możesz znaleźć obiekty wg ich umiejscowienia na mapie!', 'Nie dodano jeszcze żadnych lokalizacji', 'Mapa użytkowników', 'Tutaj możesz znaleźć użytkowników wg ich lokalizacji na mapie', 'Na główną', 'Nie można załadować obrazka.', 'Komentarze', 'Usuń komentarz', 'Nie dodano jeszcze komentarzy. Dodaj pierwszy!', 'Aby móc dodać komentarz musisz się zalogować.', 'Nie znaleziono obrazka...', 'Treść komentarza', 'Captcha', 'Kontakt', 'W razie pytań lub wątpliwości zapraszamy do kontaktu z nami - ', 'Temat wiadomości', 'Wiadomość', 'Wyślij', 'Polityka prywatności', 'Moderator', 'Kontakt z użytkownikiem', 'Tutaj możesz wysłać wiadomość do użytkownika.', 'Nie znaleziono użytkownika o podanym loginie', 'Regulamin', 'Powtórz', 'Akceptuje', 'oraz', 'Politykę prywatności', 'Podaj dodatkowe informacje', 'Obrazków i filmów', 'W poczekalni', 'Kategorii', 'Tagów', 'Stworzonych', 'Uzytkowników', 'Najlepsze', 'Najnowsze', 'Najnowsze komentarze', 'Wyszukiwarka', 'Szukaj', 'Wyszukiwarka użytkowników', 'Tag', 'Stwórz swój obrazek', 'Zapisz obrazek', 'Stwórz nowy obrazek', 'Kolor w formacie hexadecymalnym', 'Dodaj obrazek z dysku', 'Typ', 'Obrazek', 'Mem', 'Kolor tła', 'Kolor ramki', 'Podgląd', 'Stwórz', 'Stworzone', 'Obrazków', 'Na głównej', 'Nic nie znaleziono', 'Mapa strony', 'O NAS', 'KATEGORIE', 'Tagi', 'Top', 'Logowanie', 'Użytkownicy', 'Profil', 'Dodano', 'Kategorie', 'Czy na pewno usunąć obiekt? Zostaną usunięte również wszystkie komentarze.', 'Błąd: niewłaściwy typ pliku, lub plik o za dużym rozmiarze.', 'Błąd: dodaj właściwy plik z dysku.', 'Błąd: podany adres url nie istnieje.', 'Błąd: podaj adres filmu z serwisu Youtube.', 'Błąd: podaj adres filmu z serwisu Vimeo.', 'Błąd: podaj adres filmu z serwisu Dailymotion.', 'Niezidentyfikowany błąd.', 'Dodałeś już podobny obrazek lub video.', 'Nieprawidłowy numer kategorii.', 'Za długi opis!', 'Zaloguj się!', 'Zapisano!', 'Dane zostały poprawnie zaktualizowane.', 'Podane hasła są różne.', 'Podaj właściwe stare hasło.', 'Hasło jest za długie!', 'Wypełnij wszystkie pola.', 'Konto nie zostało jeszcze aktywowane.', 'Podane dane są nieprawidłowe.', 'Konto zostało założone, potwierdź adres e-mail klikając w link aktywacyjny.', 'Nowe hasło zostało wysłane na Twój adres e-mail.', 'Nieprawidłowo przepisany kod.', 'Komentarz jest za długi!', 'Wiadomość została wysłana', 'Nieprawidłowy login', 'Login jest już zajęty.', 'Nieprawidłowy adres email.', 'Adres e-mail istnieje już w bazie.', 'To pole jest obowiązkowe.', 'Konto zostało aktywowane, możesz teraz się zalogować.', 'Nieprawidłowy kod aktywacyjny lub konto zostało już aktywowane.', 'Adres email nie jest zarejestrowany w bazie.', 'Ta strona używa ciasteczek (cookies), dzięki którym nasz serwis może działać lepiej.', 'Rozumiem', 'Czy na pewno usunąć komentarz?', 'Mocne', 'Słabe', 'GŁÓWNA', 'Następna strona', 'STWORZONE', 'UŻYTKOWNICY', 'Mem z obrazka', 'Błąd 404. Strona nie istnieje.', 'TAK', 'NIE', 'Musisz być zalogowany aby móc oddawać głosy!', 'Najlepszy komentarz:'),
('rosyjski', 'Добавить новый', 'Удалось', 'ПОСМОТРИ', 'Заглавие', 'Описание', 'Фильм с Youtube', 'Фильм с Vimeo', 'Фильм с Dailymotion', 'Картинка из компьютера', 'Картинка из Интернета', 'Категория', 'Теги (после запятой)', 'Добавить', 'Можешь отметить место на карте', 'Редактировать картинку', 'Записать', 'Получилась ошибка!', 'Песочница', 'Автор', 'Дата', 'Комментариев', 'Надо войти, чтобы посмотреть картинку!', 'Страница', 'Нет добавленных картинок...', 'Активный', 'Оконченный', 'Дата начала', 'Дата конца', 'Конкурс не найден...', 'Конкурсы', 'Конкурс', 'Победитель', 'Статус', 'Аккаунт', 'Имя пользователя', 'E-mail', 'Статистикa', 'Дата регистрации', 'Добавленных картинок', 'Картинок на главной', 'Добавленных комментариев', 'Созданных картинок', 'Твои картинки и видео', 'Голосов', 'Удалить', 'Ничего не найдено', 'Личные данные', 'Имя и фамилия', 'Адрес', 'Города для снимок', 'Сброс пароля', 'Старый пароль', 'Новый пароль', 'Повторить новый пароль', 'Сбросить пароль', 'Вход', 'Имя пользователя или e-mail', 'Пароль', 'Войти через Facebook', 'Сброс пароля', 'Регистрация', 'Вернуться на сайт', 'О нас', 'ЛУЧШИЕ', 'ДОБАВИТЬ', 'СОЗДАТЬ', 'ПЕСОЧНИЦА', 'КАРТА ОБЪЕКТОВ', 'КАРТА ПОЛЬЗОВАТЕЛЕЙ', 'КОНКУРСЫ', 'АККАУНТ', 'ВЫХОД', 'РЕГИСТРАЦИЯ', 'ВХОД', 'Случайные', 'Все снимки и видео на сайте добавляются пользователями сервиса, а его владелец не несет никакой ответственности за них.', 'Карта объектов', 'Здесь можешь найти объекты в соответствии с их расположением на карте', 'Еще нет добавленных местоположений', 'Карта пользователей', 'Здесь можешь найти пользователей в сответствии с их местоположением', 'На главную', 'Загрузить изображение невозможно', 'Комментарии', 'Удалить комментарий', 'Еще нет комментариев. Добавь первый!', 'Чтобы оставить комментарий, вы должны быть авторизованы.', 'Картинка не нейдена...', 'Текст комментария', 'Защитный код', 'Контакт', 'Если у вас есть вопросы или проблемы, пожалуйста, свяжитесь с на', 'Тема сообщения', 'Сообщение', 'Отправить', 'Политика конфиденциальности', 'Модератор', 'Контакт с пользователем', 'Здесь можешь отправить сообщение пользователю.', 'Пользователь с указанным именем не найден', 'Регулямин', 'Повторить', 'Принимаю', 'и', 'Политику конфиденциальности', 'Предоставить дополнительную информацию', 'Картинок и фильмов', 'В песочнице', 'Категорией', 'Тегов', 'Созданных', 'Пользователей', 'Лучшие', 'Новые', 'Новые комментарии', 'Поиск', 'Поиск', 'Поиск пользователей', 'Тег', 'Создать свою картинку', 'Сохранить изображение', 'Создать новую картинку', 'Цвет в шестнадцатеричном формате', 'Добавить картинку из компьютера', 'Вид', 'Картинка', 'Мем', 'Цвет фона', 'Цвет рамы', 'Предварительный просмотр', 'Создать', 'Созданы', 'Картинок', 'На главной', 'Ничего не найдено', 'Карта сайта', 'О НАС', 'КАТЕГОРИИ', 'Теги', 'Лучшие', 'Вход', 'Пользователи', 'Профиль', 'Добавлено', 'Категории', 'Вы уверены, что вы удаляете объект? Будут удалены также все комментарии.', 'Ошибка: недопустимый тип файла, или файл слишком большого размера', 'Ошибка: добавь соответствующий файл с диска.', 'Ошибка: URL не существует.', 'Ошибка: введи адрес фильма из Youtube.', 'Ошибка: введи адрес фильма из Vimeo.', 'Ошибка: введи адрес фильма из Dailymotion.', 'Неизвестная ошибка.', 'Вы уже добавили аналогичную картинку или видео.', 'Неверный номер категории.', 'Слишком длинное описание!', 'Необходимо войти!', 'Сохранено!', 'Данные правильно обновлены.', 'Пароли разные.', 'Введи правильный старый пароль.', 'Пароль слишком долгий!', 'Заполни все поля.', 'Аккаунт не был активирован.', 'Введены неправильные данные.', 'Аккаунт был создан, подтвердите свой адрес e-mail, нажав на ссылку активации.', 'Новый пароль был отправлен на Твой адрес e-mail.', 'Неправильно прописан код.', 'Комментарий слишком долгий!', 'Сообщение отправлено', 'Неправильное имя пользователя', 'Имя пользователя уже занято.', 'Неправильный адрес e-mail.', 'Адрес e-mail уже существует в базе данных.', 'Это поле является обязательным.', 'Аккаунт был активирован, теперь можешь войти в систему.', 'Неверный код активации или аккаунт уже был активирован.', 'Адрес e-mail не зарегистрирован в базе данных.', 'Этот сайт использует файлы cookie, чтобы наш сервис работал лучше.', 'Понимаю', 'Ты уверен, что хочешь удалить комментарий?', 'Нравится', 'Не нравится', 'ГЛАВНАЯ', 'Следующая страница', 'СОЗДАННЫЕ', 'ПОЛЬЗОВАТЕЛИ', 'Мем с картинки', 'Error 404. Ошибка не существует.', 'ДА', 'НЕ', 'Вы должны войти в систему, чтобы иметь возможность отдать свой голос!', 'Лучший комментарий:'),
('włoski', 'Aggiungere un nuovo', 'Ha funzionato', 'VEDERE', 'Titolo', 'Descrizione', 'Film Youtube', 'Film Vimeo', 'Film Dailymotion', 'Picture disk', 'Immagine di Internet', 'Categoria', 'Tag (decimale)', 'Aggiungere', 'È possibile contrassegnare le posizioni sulla mappa', 'Modifica immagine', 'Salva', 'È verificato un errore!', 'Sala d\'attesa', 'Autore', 'Data', 'Commenti', 'Devi essere registrato per vedere la foto!', 'Pagina', 'Non hai ancora aggiunto immagini ...', 'Attivo', 'Completato', 'Data di inizio', 'Data di completamento', 'Non trovato la competizione ...', 'Concorsi', 'Concorso', 'Vincitore', 'Stato', 'Conto', 'Accesso', 'E mail', 'Statistica', 'Data di registrazione', 'Immagini aggiunte', 'Le immagini sul principale', 'Commenti aggiunti', 'Immagini create', 'Le vostre immagini e video', 'Voti', 'Cancellare', 'Non abbiamo trovato nulla', 'Dati personali', 'Nome', 'Indirizzo', 'Città riprese', 'Modificare la password', 'Vecchia password', 'Nuova password', 'Ripetere la nuova password', 'Modificare la password', 'Entra', 'Login o e-mail', 'Password', 'Collegati a Facebook', 'Cambia la tua password', 'Registrazione', 'Tornare al sito', 'Chi siamo', 'TOP', 'AGGIUNGI', 'CREARE', 'SALA D\'ATTESA', 'MAP OGGETTI', 'MAPPARE GLI UTENTI', 'CONCORSI', 'CONTO', 'LOG OUT', 'REGISTRAZIONE', 'LOGIN', 'Casuale', 'Tutte le foto ei video sul sito vengono aggiunti dagli utenti e il suo proprietario non si assume alcuna responsabilità per loro.', 'Mappa oggetti', 'Qui si possono trovare gli oggetti in base alla loro posizione sulla mappa!', 'Non aggiunto alcun luogo', 'Mappa utenti', 'Qui si possono trovare utenti con la loro posizione sulla mappa', 'Il principale', 'Non è possibile caricare immagini.', 'Commenti', 'Elimina commento', 'Nessun commento ancora aggiunto. Aggiungere il primo!', 'Per aggiungere un commento devi essere loggato.', 'Non trovato foto ...', 'Commento', 'Captcha', 'Contatto', 'Se avete domande o dubbi, non esitate a contattarci -', 'Messaggio oggetto', 'Messaggio', 'Inviare', 'Tutela della privacy', 'Moderatore', 'Contatta il proprietario', 'Qui è possibile inviare un messaggio all\'utente.', 'Nessun utente è stato trovato con il nome di login specificato', 'Normativa', 'Ripetizione', 'Accetta', 'e', 'Tutela della privacy', 'Fornire informazioni aggiuntive', 'Foto e video', 'Nella sala d\'attesa', 'Categoria', 'Tag', 'Creato', 'Utenti', 'Migliore', 'Recente', 'Commenti recenti', 'Ricerca', 'Ricerca', 'Gli utenti dei motori di ricerca', 'Etichetta', 'Crea la tua immagine', 'Salva immagine', 'Creare una nuova immagine', 'Il colore in formato esadecimale', 'Aggiungere un\'immagine da disco', 'Tipo', 'Immagine', 'Mem', 'Colore di sfondo', 'Colore del telaio', 'Anteprima', 'Creare', 'Creato', 'Immagini', 'Sulla principale', 'Non abbiamo trovato nulla', 'Mappa del sito', 'CHI SIAMO', 'CATEGORIA', 'Etichetta', 'Top', 'Entra', 'Utenti', 'Profilo', 'Aggiunto', 'Categorie', 'Sei sicuro di eliminare un oggetto? Essi potranno anche essere cancellati tutti i commenti.', 'Errore: il tipo di file sbagliato, o il file per le grandi dimensioni.', 'Errore: aggiungere il file appropriato dal disco.', 'Errore: L\'URL specificato non esiste.', 'Errore: inserire l\'indirizzo di un film da Youtube.', 'Errore: inserire l\'indirizzo del sito di video Vimeo.', 'Errore: inserire l\'indirizzo del servizio di video Dailymotion.', 'Errore non identificato.', 'Hai già aggiunto una foto o un video simile.', 'Numero non valido di categorie.', 'Per una descrizione lunga!', 'Effettua l\'accesso!', 'Salvato!', 'I dati sono stati aggiornati correttamente.', 'Le password sono diverse.', 'Fornire una corretta vecchia password.', 'La password è a lungo!', 'Compila tutti i campi.', 'L\'account non è stato ancora attivato.', 'Nessun sito web inserito.', 'Account è stato istituito, confermare il tuo indirizzo e-mail cliccando sul link di attivazione.', 'La nuova password è stata inviata al tuo indirizzo e-mail.', 'Non correttamente conservati codice.', 'Commento è troppo lungo!', 'Il messaggio è stato inviato', 'Login non valido', 'Accesso è già stato preso.', 'Indirizzo di posta elettronica valido.', 'E-mail esiste già nel database.', 'Questo campo è obbligatorio.', 'Account è stato attivato, è ora possibile il login.', 'Codice di attivazione non corretto o l\'account è già stato attivato.', 'Indirizzo e-mail non è registrato nel database.', 'Questo sito utilizza cookies (biscotti), in modo che il nostro servizio potrebbe funzionare meglio.', 'Capisco', 'Vuoi veramente cancellare il commento?', 'Forte', 'Debolezza', 'HOME', 'Pagina seguente', 'CREATO', 'UTENTI', 'Mem dall\'immagine', '404 pagina di errore non esiste.', 'SI', 'NON', 'Devi essere registrato per essere in grado di esprimere il vostro voto!', 'Miglior commento:');");
			
			$base_url = $_POST['url'];
			if(substr($base_url, 0, 7) != "http://" and substr($base_url, 0, 8) != "https://" ) {
				$base_url = 'http://'.$base_url;
			}
			if(substr($base_url, -1)=='/'){
				$base_url = substr($base_url,0,-1);
			}
			
			$szablon = 'default';
			if (!file_exists('../views/'.$szablon) ) {
				$dirs = array_filter(glob('../views/*'), 'is_dir');
				$szablon = substr($dirs[0],9);
			}
			
			mysql_query("INSERT INTO `automatyzacja` (`wlacz`, `dni_wiecej`, `dni_mniej`, `glosy`, `komentarze`, `min_szerokosc`, `min_wysokosc`, `generuj_sitemap`) VALUES(1, 3, 7, 1, 1, 400, 500, 1);");
			mysql_query("INSERT INTO `cms` (`login`, `haslo`) VALUES('".$_POST['logincms']."', md5('".$_POST['haslocms']."'))");
			mysql_query("INSERT INTO `ustawienia` (`base_url`, `tytul`, `stopka_url`, `stopka_nazwa`, `szablon`, `ile_na_strone`, `konto_ile_na_strone`, `mapa`, `limit_mapa`, `rozmiar_upload`, `tworzenie`, `reklama_co_obrazkow`, `logo_w_ramce`, `wybor_kolor_ramki`, `logo_w_ramce_tekst`, `logo_w_ramce_kolor`, `memy`, `udostepnij_fb`, `jezyk`,`mapa_center`, `email_rejestracja_temat`, `email_rejestracja_tresc`, `email_rejestracja_fb_temat`, `email_rejestracja_fb_tresc`, `email_reset_temat`, `email_reset_tresc`, `email_kontakt_temat`, `email_kontakt_tresc`, `komentarze_glosy`, `komentarze_pokaz_najlepszy`, `komentarze_facebook`, `stopka`) VALUES('".$base_url."', 'Demoty online', '', '', '".$szablon."', 8, 20, 1, 20, 5000, 1, 3, 1, 1, '', '#000000', 1, 1, 'polski', '52.072754, 19.028321','Witamy na stronie {tytul}', '&lt;p&gt;\r\n	Witaj na stronie &lt;strong&gt;{tytul}&lt;/strong&gt;!&lt;br /&gt;\r\n	Dziękujemy za rejestrację.&lt;br /&gt;\r\n	Żeby ją dokończyć kliknij w link: {link_aktywacyjny}&lt;br /&gt;\r\n	Pozdrawiamy&lt;br /&gt;\r\n	Zesp&amp;oacute;ł {tytul}&lt;/p&gt;\r\n', 'Witamy na stronie {tytul}', '&lt;p&gt;\r\n	Witaj na stronie {tytul}!&lt;br /&gt;\r\n	Dziękujemy za rejestrację poprzez konto Facebook.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Tw&amp;oacute;j login to: &lt;b&gt;{login}&lt;/b&gt;&lt;br /&gt;\r\n	Twoje hasło: &lt;b&gt;{haslo}&lt;/b&gt;&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Po zalogowaniu możesz zmienić hasło.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Pozdrawiamy&lt;br /&gt;\r\n	Zesp&amp;oacute;ł {tytul}&lt;/p&gt;\r\n', 'Reset hasła - {tytul}', '&lt;p&gt;\r\n	Witaj!&lt;/p&gt;\r\n&lt;p&gt;\r\n	Twoje nowe hasło do serwisu {tytul} to:&lt;b&gt; {haslo}&lt;/b&gt;&lt;br /&gt;\r\n	Tw&amp;oacute;j login to: &lt;b&gt;{login}&lt;/b&gt;&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Po zalogowaniu możesz zmienić hasło.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Jeśli jeszcze nie aktywowałeś swojego konta kliknij w link: {link_aktywacyjny}&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Pozdrawiamy&lt;br /&gt;\r\n	Zesp&amp;oacute;ł {tytul}&lt;/p&gt;\r\n', 'Wiadomość ze strony {tytul}', '&lt;p&gt;\r\n	Witaj!&lt;/p&gt;\r\n&lt;p&gt;\r\n	Została do Ciebie wysłana wiadomość ze strony {host} od:&lt;br /&gt;\r\n	Imię i nazwisko: {imie}&lt;br /&gt;\r\n	Adres e-mail: {email}&lt;br /&gt;\r\n	Temat wiadomości: {temat}&lt;br /&gt;\r\n	Wiadomość: {tresc}&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n	Pozdrawiamy&lt;br /&gt;\r\n	Zesp&amp;oacute;ł {tytul}&lt;/p&gt;\r\n', '1', '1', '1', '&lt;p&gt;&lt;a href=&quot;http://scripts.pl/pl/demotywatory/44-skrypt-typu-demotywatory.html&quot; target=&quot;_blank&quot; title=&quot;Skrypt typu demotywatory&quot;&gt;Skrypt typu demotywatory&lt;/a&gt;. Project © 2014 - 2017 by &lt;a href=&quot;http://wyremski.pl&quot; target=&quot;_blank&quot; title=&quot;Tworzenie Stron Internetowych&quot;&gt;Kamil Wyremski&lt;/a&gt;.&lt;/p&gt;')");

			chmod("../cache", 0777);
			chmod("../tmp", 0777);
			chmod("../cms/cache", 0777);
			chmod("../cms/tmp", 0777);
			chmod("../obrazy", 0777);
			chmod("../upload", 0777);
			chmod("../sitemap.xml", 0777);
			chmod("../config/db.php", 0644);
			
			array_map('unlink', glob("../tmp/*"));
			array_map('unlink', glob("../cms/tmp/*"));
	
			header('location: ../cms');
		}
	}
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
	<meta name="author" content="Kamil Wyremski - wyremski.pl">
	<title>Instalator skryptu</title>
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="engine.js"></script>
</head>
<body>
<div id="strona">
	<a href="http://wyremski.pl" title="Tworzenie stron www"><img src="../cms/images/cms.png" alt="CMS Kamil Wyremski" id="logo"/></a>
	<h1 style="display:none">Instalator skryptu created by wyremski.pl</h1>
	<h2>Witaj w programie instalacyjnym!<br>Prosimy o wypełnienie poniższych pól<br>aby wstępnie skonfigurować stronę.</h2>
	<?php
		if(isset($error)){
			echo('<h3>'.$error.'</h3>');
		}
	?>
	<form method="post" action="">
		<table>
			<tr>
				<td>Adres URL strony:</td>
				<td><input type="text" name="url" placeholder="Adres URL" value="<?php if(isset($_POST['url'])){echo($_POST['url']);}?>" required/></td>
			</tr>
			<tr>
				<td>Serwer bazy danych:</td>
				<td><input type="text" name="serwer" placeholder="Serwer mysql" value="<?php if(isset($_POST['serwer'])){echo($_POST['serwer']);}?>" required/></td>
			</tr>
			<tr>
				<td>Port serwera bazy danych:</td>
				<td><input type="text" name="port" placeholder="Port serwera" value="<?php if(isset($_POST['port'])){echo($_POST['port']);}?>" required/></td>
			</tr>
			<tr>
				<td>Nazwa użytkownika bazy danych:</td>
				<td><input type="text" name="uzytkownik" placeholder="Użytkownik" value="<?php if(isset($_POST['uzytkownik'])){echo($_POST['uzytkownik']);}?>" required/></td>
			</tr>
			<tr>
				<td>Nazwa bazy danych:</td>
				<td><input type="text" name="nazwa" placeholder="Nazwa bazy" value="<?php if(isset($_POST['nazwa'])){echo($_POST['nazwa']);}?>" required/></td>
			</tr>
			<tr>
				<td>Hasło do bazy danych:</td>
				<td><input type="password" name="haslo" placeholder="Hasło do bazy" value="<?php if(isset($_POST['haslo'])){echo($_POST['haslo']);}?>"/></td>
			</tr>
			<tr>
				<td>Login do systemu CMS:</td>
				<td><input type="text" name="logincms" placeholder="Login do CMS" value="<?php if(isset($_POST['logincms'])){echo($_POST['logincms']);}?>" required/></td>
			</tr>
			<tr>
				<td>Hasło do systemu CMS:</td>
				<td><span class="red">Podane hasła są różne</span><input type="password" name="haslocms" placeholder="Hasło do CMS" value="<?php if(isset($_POST['haslocms'])){echo($_POST['haslocms']);}?>" required/></td>
			</tr>
			<tr>
				<td>Powtórz hasło do systemu CMS:</td>
				<td><input type="password" name="haslocms2" placeholder="Hasło do CMS" required/></td>
			</tr>
		</table>
		<input type="submit" value="Zapisz"/>
	</form>
	<p style="text-align: left">W razie problemów z instalacją zmień uprawnienia poniższych plików i folderów na wartość 0777:
	<br>cache
	<br>tmp
	<br>cms/cache
	<br>cms/tmp
	<br>obrazy
	<br>upload
	<br>sitemap.xml
	<br>config/db.php - w tym ostatnim po zakończonej instalacji zmień na 0644</p>
</div>
<br><br><br>
<footer>CMS v3 Copyright and project © 2014 - 2016 by <a href="http://wyremski.pl" target="_blank" title="Tworzenie Stron Internetowych">Kamil Wyremski</a>. All rights reserved.</footer>
</body>
</html>
