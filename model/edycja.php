<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

if(isset($uzytkownik)){
	
	$blad = false;
	
	if(isset($_POST['kategoria']) and $_POST['kategoria']>0){
		$kategoria = filtruj($_POST['kategoria']);
	}else{
		$kategoria = 0;
	}
		
	if(mysql_num_rows(mysql_query('select id from obrazki where autor_id = "'.$uzytkownik['id'].'" and id="'.filtruj($_GET['id']).'" limit 1'))>0 or $uzytkownik['moderator']==1){
		if(isset($_POST['edytuj']) and isset($_POST['tytul']) and isset($_POST['id'])){
			if($kategoria and !mysql_num_rows(mysql_query('select id from kategorie where id="'.$kategoria.'"'))){
				$blad =  $tlumaczenia_teksty['kategoria'];
			}elseif(strlen($_POST['opis'])>3000){
				$blad =  $tlumaczenia_teksty['opis'];
			}else{
				$tagi_output = '';
				if($_POST['tagi'] != ''){
					$tagi = str_replace(", ",",",filtruj($_POST['tagi']));
					$tagi = explode(',', $tagi);
					for($i=0; $i <= count($tagi) - 1; $i++){
						$tagi_prosta_nazwa = prosta_nazwa($tagi[$i]);
						if($tagi_prosta_nazwa!=''){
							$tag_juz_jest = false;
							$q = mysql_query('select id from tagi where prosta_nazwa = "'.$tagi_prosta_nazwa.'" limit 1');
							while($dane = mysql_fetch_array($q)){
								$tag_id = $dane['id'];
								$tag_juz_jest = true;
							}
							if($tag_juz_jest){
								$tagi_output.='-'.$tag_id.'-';
							}else{
								mysql_query('insert into tagi values(null, "'.$tagi[$i].'", "'.$tagi_prosta_nazwa.'")');
								$tagi_output.='-'.mysql_insert_id().'-';
							}
						}
					}
				}
				if(isset($_POST['zaznacz_na_mapie']) and isset($_POST['mapa']) and $_POST['mapa']!=''){$mapa=$_POST['mapa'];}else{$mapa='';}
				mysql_query('update obrazki set kategoria="'.$kategoria.'", tagi="'.$tagi_output.'", tytul="'.filtruj($_POST['tytul']).'", prosty_tytul="'.prosta_nazwa(filtruj($_POST['tytul'])).'", opis="'.htmlspecialchars(purify($_POST['opis'])).'", mapa="'.$mapa.'" where id="'.filtruj($_POST['id']).'" limit 1');
				$smarty->assign("zapisano", $tlumaczenia_teksty['zapisano']);
			}
		}

		$q = mysql_query('select id, kategoria, tagi, tytul, prosty_tytul, opis, wybor_obrazka, url, miniaturka, mapa, autor_id from obrazki where id="'.filtruj($_GET['id']).'" limit 1');
		while($dane = mysql_fetch_array($q)){
			$dane['opis'] = htmlspecialchars_decode($dane['opis']);
			$obrazek = $dane;
		}

		$tagi = explode('-', $obrazek['tagi']);
		$obrazek['tagi'] = '';
		for($i=0; $i <= count($tagi) - 1; $i++){
			if($tagi[$i]>0){
				$q = mysql_query('select nazwa from tagi where id='.$tagi[$i].'');
				while($dane = mysql_fetch_array($q)){$obrazek['tagi'] .= $dane['nazwa'].', ';}
			}
		}
		
		$smarty->assign("obrazek", $obrazek);
		
	}else{
		$blad = $tlumaczenia_teksty['nie_znaleziono'];
	}
	
	if($blad){
		pobierz_losowe_obrazki();
		$smarty->assign("blad", $blad);
	}

	pobierz_kategorie();
	pobierz_boksy();
	pobierz_dane_do_boksow();
	
}else{
	header('Location: ../'.$tlumaczenia_linki['logowanie']);
}

