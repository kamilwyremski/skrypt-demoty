<?php

$title = 'CMS created by Kamil Wyremski';
$strona = 'home';

if(isset($cms_login)){
	if(isset($_GET['akcja'])){
		switch($_GET['akcja']){
			case 'konkursy': 
				$title = "Konkursy - ".$title;
				$strona = 'konkursy';
				include('model/konkursy.php');
				break;
			case 'kategorie':
				$title = "Kategorie - ".$title;
				$strona = 'kategorie';
				include('model/kategorie.php');
				break;
			case 'tagi':
				$title = "Tagi - ".$title;
				$strona = 'tagi';
				include('model/tagi.php');
				break;
			case 'obrazki':
				$title = "Obrazki i filmy - ".$title;
				$strona = 'obrazki';
				include('model/obrazki.php');
				break;
			case 'stworzone':
				$title = "Stworzone obrazki - ".$title;
				$strona = 'stworzone';
				include('model/stworzone.php');
				break;
			case 'dodaj_z_dysku':
				$title = "Dodaj obrazki z dysku - ".$title;
				$strona = 'dodaj_z_dysku';
				include('model/dodaj_z_dysku.php');
				break;
			case 'uzytkownicy':
				$title = "Użytkownicy - ".$title;
				$strona = 'uzytkownicy';
				include('model/uzytkownicy.php');
				break;
			case 'komentarze':
				$title = "Komentarze - ".$title;
				$strona = 'komentarze';
				include('model/komentarze.php');
				break;
			case 'boksy':
				$title = "Boksy - ".$title;
				$strona = 'boksy';
				include('model/boksy.php');
				break;
			case 'memy_obrazki':
				$title = "Memy - obrazki - ".$title;
				$strona = 'memy_obrazki';
				include('model/memy_obrazki.php');
				break;
			case 'onas':
				$title = "O nas - ".$title;
				$strona = 'onas';
				include('model/onas.php');
				break;
			case 'regulamin':
				$title = "Regulamin i polityka prywatności - ".$title;
				$strona = 'regulamin';
				include('model/regulamin.php');
				break;
			case 'logo':
				$title = "Logo i znak wodny - ".$title;
				$strona = 'logo';
				include('model/logo.php');
				break;
			case 'ustawienia':
				$title = "Ustawienia - ".$title;
				$strona = 'ustawienia';
				include('model/ustawienia.php');
				break;
			case 'jezyki':
				$title = "Języki - edycja - ".$title;
				$strona = 'jezyki';
				include('model/jezyki.php');
				break;
			case 'jezyk_linki':
				$title = "Język - edycja linków - ".$title;
				$strona = 'jezyk_linki';
				include('model/jezyk_linki.php');
				break;
			case 'jezyk_slowa':
				$title = "Język - edycja słów - ".$title;
				$strona = 'jezyk_slowa';
				include('model/jezyk_slowa.php');
				break;
			case 'maile':
				$title = "Maile - edycja - ".$title;
				$strona = 'maile';
				include('model/maile.php');
				break;
			case 'automatyzacja':
				$title = "Automatyzacja - ".$title;
				$strona = 'automatyzacja';
				include('model/automatyzacja.php');
				break;
			case 'ustawienia_cms':
				$title = "Ustawienia CMS - ".$title;
				$strona = 'ustawienia_cms';
				include('model/ustawienia_cms.php');
				break;
		}
	}
	
	pobierz_kategorie();
	$smarty->assign("title", $title);
	$smarty->assign('strona',$strona);
	$smarty->assign('ustawienia',$ustawienia);
	$smarty->assign('tlumaczenia_linki',$tlumaczenia_linki);
	$smarty->display('main.tpl');
}else{
	$smarty->display('logowanie.tpl');
}
