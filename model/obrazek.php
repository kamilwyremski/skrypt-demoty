<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($uzytkownik) and isset($_POST['komentarz']) and isset($_POST['id']) and isset($_POST['tresc']) and isset($_POST['captcha'])){
	$blad = false;
	if($_POST['captcha']!=$_SESSION['captcha']){
		$blad = true;
		$smarty->assign("blad", $tlumaczenia_teksty['blad_captcha']);
	}elseif($_POST['tresc']==''){
		$blad = true;
		$smarty->assign("blad", $tlumaczenia_teksty['blad_wszystkie_pola']);
	}elseif(strlen($_POST['tresc'])>1300){
		$blad = true;
		$smarty->assign("blad", $tlumaczenia_teksty['blad_dlugi_komentarz']);
	}elseif(mysql_num_rows(mysql_query('select id from obrazki where id="'.$_POST['id'].'"'))==0){
		$blad = true;
		$smarty->assign("blad", $tlumaczenia_teksty['blad_inny']);
	}
	if($blad){
		$smarty->assign("tresc", $_POST['tresc']);
	}else{
		mysql_query('insert into komentarze values(null, "'.$uzytkownik['id'].'", "'.$_POST['id'].'", "'.htmlspecialchars(filtruj($_POST['tresc'])).'", "'.date("Y-m-d H:i:s").'")');
	}	
}

$q = mysql_query('select obrazki.id, obrazki.kategoria, obrazki.tagi, obrazki.glowna, obrazki.tytul, obrazki.prosty_tytul, obrazki.opis, obrazki.wybor_obrazka, obrazki.url, obrazki.miniaturka, obrazki.mapa, obrazki.autor_id, obrazki.glosy, obrazki.data, uzytkownicy.login from obrazki, uzytkownicy where obrazki.autor_id = uzytkownicy.id and obrazki.id="'.$_GET['id'].'" limit 1');
$ip = get_client_ip();
if(isset($uzytkownik)){$autor_id=$uzytkownik['id'];}else{$autor_id=0;}
while($dane = mysql_fetch_array($q)){
	$dane['ile_komentarzy'] = mysql_num_rows(mysql_query('select id from komentarze where obrazek_id="'.$dane['id'].'"'));
	$dane['opis'] = htmlspecialchars_decode($dane['opis']);
	$dane['glos'] = 0;
	$q2 = mysql_query('select glos from glosy where obrazek_id="'.$dane['id'].'" and (ip="'.$ip.'" or (autor_id!=0 and autor_id="'.$autor_id.'")) limit 1');
	while($dane2 = mysql_fetch_array($q2)){	$dane['glos'] = $dane2['glos'];}
	$q3 = mysql_query('select nazwa, prosta_nazwa from kategorie where id="'.$dane['kategoria'].'" limit 1');
	while($dane3 = mysql_fetch_array($q3)){
		$dane['nazwa'] = $dane3['nazwa'];
		$dane['prosta_nazwa'] = $dane3['prosta_nazwa'];
	}
	$obrazek = $dane;
}
if(isset($obrazek)){
	$tagi = explode('-', $obrazek['tagi']);
	$obrazek['tagi'] = '';
	for($i=0; $i <= count($tagi) - 1; $i++){
		if($tagi[$i]>0){
			$q = mysql_query('select nazwa, prosta_nazwa from tagi where id='.$tagi[$i].'');
			while($dane = mysql_fetch_array($q)){$tagi_nazwa= $dane;}
			$obrazek['tagi'] .= ' <a href="'.$ustawienia['base_url'].'/'.$tlumaczenia_linki['tag'].'/'.$tagi_nazwa['prosta_nazwa'].'" title="Tag: '.$tagi_nazwa['nazwa'].'">'.$tagi_nazwa['nazwa'].'</a> ';
		}
	}
	$smarty->assign("obrazek", $obrazek);
	$title = $obrazek['tytul'].' - '.$ustawienia['tytul'];
	if($obrazek['opis']!=''){$description = substr($obrazek['opis'], 0, 300);}
	
	$najlepszy_komentarz = array('id' => 0, 'glosy' => 0);
	
	$q = mysql_query('select komentarze.id, komentarze.tresc, komentarze.data, komentarze.autor_id, uzytkownicy.login from komentarze, uzytkownicy where komentarze.obrazek_id = "'.$_GET['id'].'" and komentarze.autor_id = uzytkownicy.id');
	while($dane = mysql_fetch_assoc($q)){
		if($ustawienia['komentarze_glosy']){
			$dane['glos'] = 0;
			$q2 = mysql_query('select glos from komentarze_glosy where komentarz_id="'.$dane['id'].'" and (ip="'.$ip.'" or (autor_id!=0 and autor_id="'.$autor_id.'")) limit 1');
			while($dane2 = mysql_fetch_assoc($q2)){	$dane['glos'] = $dane2['glos'];}
			$dane['glosy'] = mysql_num_rows(mysql_query('select 1 from komentarze_glosy where komentarz_id="'.$dane['id'].'" and glos="1"')) - mysql_num_rows(mysql_query('select 1 from komentarze_glosy where komentarz_id="'.$dane['id'].'" and glos="-1"'));
			if($ustawienia['komentarze_pokaz_najlepszy'] and $dane['glosy']>$najlepszy_komentarz['glosy']){
				$najlepszy_komentarz['glosy'] = $dane['glosy'];
				$najlepszy_komentarz['id'] = $dane['id'];
				$najlepszy_komentarz['dane'] = $dane;
			}
		}
		$dane['tresc'] = htmlspecialchars_decode($dane['tresc']);
		$komentarze[] = $dane;
	}
	if(isset($komentarze)){$smarty->assign("komentarze", $komentarze);}
	if($ustawienia['komentarze_glosy'] and $ustawienia['komentarze_pokaz_najlepszy'] and $najlepszy_komentarz['id']!=0 and $najlepszy_komentarz['glosy']!=0){
		$smarty->assign("najlepszy_komentarz", $najlepszy_komentarz['dane']);
	}
}else{
	include('model/404.php');
	pobierz_losowe_obrazki();
}

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

