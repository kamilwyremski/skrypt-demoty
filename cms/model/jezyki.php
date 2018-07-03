<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

if(isset($_POST['akcja']) and isset($_POST['jezyk']) and $_POST['jezyk']!=''){
	$jezyk = filtruj($_POST['jezyk']);
	if($_POST['akcja']=='ustaw_aktywny_jezyk'){
		mysql_query('update `ustawienia` set jezyk="'.$jezyk.'" limit 1');
		pobierz_ustawienia();
	}elseif($_POST['akcja']=='dodaj_jezyk'){
		if(mysql_num_rows(mysql_query('select 1 from tlumaczenia_linki where jezyk="'.$jezyk.'" limit 1'))){
			$smarty->assign("blad", 'Wybrana nazwa jest już zajęta!');
		}else{
			mysql_query('CREATE TEMPORARY TABLE temp_table SELECT * FROM tlumaczenia_linki WHERE jezyk="'.$ustawienia['jezyk'].'"');
			mysql_query('UPDATE temp_table SET jezyk="'.$jezyk.'", podstawowy = 0');
			mysql_query('INSERT INTO tlumaczenia_linki SELECT * from temp_table;');
			mysql_query('DROP TABLE temp_table;');
			if(isset($_POST['kopiuj_teksty'])){
				mysql_query('CREATE TEMPORARY TABLE temp_table SELECT * FROM tlumaczenia_teksty WHERE jezyk="'.$ustawienia['jezyk'].'"');
				mysql_query('UPDATE temp_table SET jezyk="'.$jezyk.'"');
				mysql_query('INSERT INTO tlumaczenia_teksty SELECT * from temp_table;');
				mysql_query('DROP TABLE temp_table;');
			}else{
				mysql_query('INSERT INTO `tlumaczenia_teksty`(`jezyk`) VALUES ("'.$jezyk.'")');
			}
		}
	}elseif($_POST['akcja']=='edytuj_jezyk' and isset($_POST['nowa_nazwa']) and $_POST['nowa_nazwa']!=''){
		$nowa_nazwa = filtruj($_POST['nowa_nazwa']);
		if(mysql_num_rows(mysql_query('select 1 from tlumaczenia_linki where jezyk="'.$nowa_nazwa.'" and jezyk!="'.$jezyk.'" limit 1'))){
			$smarty->assign("blad", 'Wybrana nazwa jest już zajęta!');
		}else{
			mysql_query('update `tlumaczenia_linki` set jezyk="'.$nowa_nazwa.'" where jezyk="'.$jezyk.'" limit 1');
			mysql_query('update `tlumaczenia_teksty` set jezyk="'.$nowa_nazwa.'" where jezyk="'.$jezyk.'" limit 1');
		}
	}elseif($_POST['akcja']=='usun_jezyk'){
		if(mysql_num_rows(mysql_query('select 1 from tlumaczenia_linki where jezyk="'.$jezyk.'" and podstawowy = 1 limit 1'))){
			$smarty->assign("blad", 'Nie możesz usunąć podstawowego języka!');
		}else{
			mysql_query('delete from `tlumaczenia_linki` where jezyk="'.$jezyk.'" limit 1');
			mysql_query('delete from `tlumaczenia_teksty` where jezyk="'.$jezyk.'" limit 1');
			if(!mysql_num_rows(mysql_query('select 1 from tlumaczenia_linki where jezyk="'.$ustawienia['jezyk'].'" limit 1'))){
				$nowy_jezyk = mysql_fetch_assoc(mysql_query('select jezyk from tlumaczenia_linki order by podstawowy desc limit 1'))['jezyk'];
				mysql_query('update `ustawienia` set jezyk="'.$nowy_jezyk.'" limit 1');
			}
		}
	}
}
$q = mysql_query('select jezyk, podstawowy from tlumaczenia_linki order by podstawowy desc, jezyk');
while($dane = mysql_fetch_assoc($q)){$jezyki[] = $dane;}
$smarty->assign("jezyki", $jezyki);

?>