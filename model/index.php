<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

global $akcja;

if(isset($_GET['search']) and $_GET['search']!=''){
	$szukaj = explode('-', prosta_nazwa(filtruj($_GET['search'])));
	for($i=0; $i < count($szukaj); $i++){
		$qt = mysql_query('select id from tagi where prosta_nazwa like "%'.$szukaj[$i].'%"');
		while($dane = mysql_fetch_array($qt)){$tagi_id[] = '-'.$dane['id'].'-';} 
		$qn = mysql_query('select id from obrazki where prosty_tytul like "%'.$szukaj[$i].'%"');
		while($dane = mysql_fetch_array($qn)){$obrazki_id[] = $dane['id'];} 
	}
	if(isset($tagi_id)){$tagi_id = implode('%" or obrazki.tagi like "%',$tagi_id);}else{$tagi_id = 'false';}
	if(isset($obrazki_id)){$obrazki_id = implode('" or obrazki.id="',$obrazki_id);}else{$obrazki_id = 'false';}
	$warunek = '(obrazki.tagi like "%'.$tagi_id.'%" or obrazki.id="'.$obrazki_id.'") order by obrazki.data_glowna desc';
	$smarty->assign("podstrona_tytul", $tlumaczenia_teksty['szukaj'].': '.filtruj($_GET['search']));
	$title = $tlumaczenia_teksty['szukaj'].': '.filtruj($_GET['search']).' - '.$title;
	
}elseif($akcja=='kategoria' and isset($_GET['id']) and $_GET['id']!=''){
	$qk = mysql_query('select id, nazwa from kategorie where prosta_nazwa="'.filtruj($_GET['id']).'" limit 1');
	while($dane = mysql_fetch_array($qk)){
		$podkategorie = '';
		$qk2 = mysql_query('select id from kategorie where kategoria_glowna="'.$dane['id'].'"');
		while($dane2 = mysql_fetch_array($qk2)){
			$podkategorie .=' or obrazki.kategoria='.$dane2['id'];
			$qk3 = mysql_query('select id from kategorie where kategoria_glowna="'.$dane2['id'].'"');
			while($dane3 = mysql_fetch_array($qk3)){
				$podkategorie .=' or obrazki.kategoria='.$dane3['id'];
			}
		}
		$warunek = 'obrazki.glowna=1 and (obrazki.kategoria = '.$dane['id'].' '.$podkategorie.') order by obrazki.data_glowna desc';
		$smarty->assign("podstrona_tytul", $tlumaczenia_teksty['kategoria'].': '.$dane['nazwa']);
		$title = $dane['nazwa'].' - '.$title;
	} 
}elseif($akcja=='tag' and isset($_GET['id']) and $_GET['id']!=''){
	$qk = mysql_query('select id, nazwa from tagi where prosta_nazwa="'.filtruj($_GET['id']).'" limit 1');
	while($dane = mysql_fetch_array($qk)){
		$warunek = 'obrazki.glowna=1 and obrazki.tagi like "%-'.$dane['id'].'-%" order by obrazki.data_glowna desc';
		$smarty->assign("podstrona_tytul", $tlumaczenia_teksty['tag'].': '.$dane['nazwa']);
		$title = $dane['nazwa'].' - '.$title;
	} 
}elseif($akcja=='poczekalnia'){
	$warunek = 'obrazki.glowna=0 order by obrazki.data desc';
	$smarty->assign("podstrona_tytul", $tlumaczenia_teksty['w_poczekalni']);
	$title = $tlumaczenia_teksty['w_poczekalni'].' - '.$title;
}elseif($akcja=='top'){
	$warunek = 'obrazki.glowna=1 order by obrazki.glosy desc, obrazki.data_glowna desc';
	$smarty->assign("podstrona_tytul", $tlumaczenia_teksty['top']);
	$title = $tlumaczenia_teksty['top'].' - '.$title;
}else{
	$warunek = 'obrazki.glowna=1 order by obrazki.data_glowna desc';
}

$limit_start = policz_strony($ustawienia['ile_na_strone'], 'obrazki', $warunek);
$ip = get_client_ip();
if(isset($uzytkownik)){$autor_id=$uzytkownik['id'];}else{$autor_id=0;}

$q = mysql_query('select obrazki.*, kategorie.nazwa, kategorie.prosta_nazwa, uzytkownicy.login, (select count(1) from komentarze where obrazek_id=obrazki.id) as ile_komentarzy, (select glos from glosy where obrazek_id=obrazki.id and (ip="'.$ip.'" or (autor_id!=0 and autor_id="'.$autor_id.'")) limit 1) as glos from obrazki LEFT JOIN uzytkownicy ON obrazki.autor_id = uzytkownicy.id LEFT JOIN kategorie ON kategorie.id = obrazki.kategoria where true and '.$warunek.' limit '.$limit_start.','.$ustawienia['ile_na_strone'].'');

while($dane = mysql_fetch_array($q)){
	$tagi = explode('-', $dane['tagi']);
	$dane['tagi'] = '';
	for($i=0; $i <= count($tagi) - 1; $i++){
		if($tagi[$i]>0){
			$q2 = mysql_query('select nazwa, prosta_nazwa from tagi where id='.$tagi[$i].'');
			while($dane2 = mysql_fetch_array($q2)){$tagi_nazwa= $dane2;}
			$dane['tagi'] .= ' <a href="'.$ustawienia['base_url'].'/'.$tlumaczenia_linki['tag'].'/'.$tagi_nazwa['prosta_nazwa'].'" title="Tag: '.$tagi_nazwa['nazwa'].'">'.$tagi_nazwa['nazwa'].'</a> ';
		}
	}
	$dane['opis'] = htmlspecialchars_decode($dane['opis']);
	$obrazki[] = $dane;
}
if(isset($obrazki)){
	$smarty->assign("obrazki", $obrazki);
}else{
	pobierz_losowe_obrazki();
}

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();

