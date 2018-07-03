<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

$ustawienia["regulamin"] = htmlspecialchars_decode($ustawienia["regulamin"]);

pobierz_kategorie();
pobierz_boksy();
pobierz_dane_do_boksow();
pobierz_losowe_obrazki();


