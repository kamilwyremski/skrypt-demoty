<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($uzytkownik)){
	if(isset($_POST['zmiana_hasla']) and isset($uzytkownik)){
		global $smarty, $uzytkownik, $tlumaczenia_teksty;
		
		if(isset($_POST['stare_haslo']) and isset($_POST['nowe_haslo']) and isset($_POST['powtorz_haslo'])){
			if(strlen($_POST['nowe_haslo'])<=32){
				$q = mysql_query('select * from uzytkownicy where id="'.$uzytkownik['id'].'" and haslo="'.md5($_POST['stare_haslo']).'" limit 1');
				while($dane = mysql_fetch_array($q)){$wynik = $dane;}
				if(isset($wynik)){
					if($_POST['nowe_haslo']==$_POST['powtorz_haslo']){
						mysql_query('update uzytkownicy set haslo="'.md5($_POST['nowe_haslo']).'" where id = "'.$uzytkownik['id'].'" limit 1');
						$smarty->assign("ok", $tlumaczenia_teksty['dane_zaktualizowane']);
					}else{
						$smarty->assign("blad", $tlumaczenia_teksty['blad_hasla_rozne']);
					}
				}else{
					$smarty->assign("blad", $tlumaczenia_teksty['blad_stare_haslo']);
				}
			}else{
				$smarty->assign("blad", $tlumaczenia_teksty['blad_dlugie_haslo']);
			}
		}else{
			$smarty->assign("blad", $tlumaczenia_teksty['blad_wszystkie_pola']);
		}
	}

	if(isset($_POST['zmiana_danych_osobowych']) and isset($uzytkownik)){
		global $smarty, $uzytkownik;
		
		if(isset($_POST['zaznacz_na_mapie']) and isset($_POST['mapa']) and $_POST['mapa']!=''){$mapa=filtruj($_POST['mapa']);}else{$mapa='';}

		mysql_query('update uzytkownicy set imie="'.filtruj($_POST['imie']).'", adres="'.filtruj($_POST['adres']).'", miasta="'.filtruj($_POST['miasta']).'", mapa="'.$mapa.'" where id = "'.$uzytkownik['id'].'" limit 1');
		
		$smarty->assign("ok_dane_osobowe", $tlumaczenia_teksty['dane_zaktualizowane']);
		logowanie();
	}

	$uzytkownik['ile_obrazkow'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$uzytkownik['id'].'"'));
	$uzytkownik['ile_obrazkow_glowna'] = mysql_num_rows(mysql_query('select id from obrazki where autor_id="'.$uzytkownik['id'].'" and glowna=1'));
	$uzytkownik['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where autor_id="'.$uzytkownik['id'].'"'));
	if($ustawienia['tworzenie']==1){
		$uzytkownik['ile_stworzono'] = mysql_num_rows(mysql_query('select id from stworzone where autor_id="'.$uzytkownik['id'].'"'));
	}
	$smarty->assign("uzytkownik", $uzytkownik);

	$limit_start = policz_strony($ustawienia['konto_ile_na_strone'], 'obrazki', 'autor_id="'.$uzytkownik['id'].'"');
	
	$q = mysql_query('select id, tytul, glowna, prosty_tytul, wybor_obrazka, url, miniaturka, glosy, data, kategoria from obrazki where autor_id="'.$uzytkownik['id'].'" order by data desc limit '.$limit_start.','.$ustawienia['konto_ile_na_strone'].'');
	while($dane = mysql_fetch_array($q)){
		$q2 = mysql_query('select nazwa, prosta_nazwa from kategorie where id="'.$dane['kategoria'].'" limit 1');
		while($dane2 = mysql_fetch_array($q2)){	
			$dane['nazwa'] = $dane2['nazwa'];
			$dane['prosta_nazwa'] = $dane2['prosta_nazwa'];
		}
		$dane['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where obrazek_id="'.$dane['id'].'"'));
		$konto_obrazki[] = $dane;
	}
	if(isset($konto_obrazki)){
		$smarty->assign("konto_obrazki", $konto_obrazki);
	}else{
		pobierz_losowe_obrazki();
	}
	
	pobierz_kategorie();
	pobierz_boksy();
	pobierz_dane_do_boksow();
}else{
	header('Location: '.$tlumaczenia_linki['logowanie'].'?redirect='.$tlumaczenia_linki['konto']);
}
