<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

header('HTTP/1.0 404 Not Found');

pobierz_boksy();
pobierz_dane_do_boksow();
pobierz_losowe_obrazki();
	
