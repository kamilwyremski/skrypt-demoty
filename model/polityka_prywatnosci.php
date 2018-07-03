<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$ustawienia["polityka_prywatnosci"] = htmlspecialchars_decode($ustawienia["polityka_prywatnosci"]);

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();
pobierz_losowe_obrazki();


