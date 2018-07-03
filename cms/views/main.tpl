<!doctype html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="Keywords" content="CMS, Content Management System, System zarządzania treścią, edycja strony www, tworzenie stron www, strony internetowe">
	<meta name="Description" content="CMS - system zarządzania treścią dla Twojej strony internetowej. Tutaj możesz w łatwy sposób, bez znajomości języka HTML edytować treści Twojej strony internetowej. System stworzony przez: Kamil Wyremski - wyremski.pl">
	<meta name="author" content="Kamil Wyremski - wyremski.pl">
	<title>{$title}</title>
	
	<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oregano&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="views/css/global.css">
	<link rel="stylesheet" type="text/css" href="views/css/style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="shortcut icon" href="img/favicon.ico"/>
	
	<script src="js/jquery-2.1.4.min.js"></script> 
	<script src="js/jquery.slimscroll.min.js"></script>
	<script src="js/ckeditor/ckeditor.js"></script>
	<script src="js/datepicker.js"></script>
    <script src="js/engine_cms.js"></script>
	<script src="js/whcookies.js"></script>
</head>
<body>
<aside id="menu">
	<div id="top_menu">
		<a href="http://wyremski.pl" title="Tworzenie stron internetowych" target="_blank"><img src="images/cms.png" alt="Logo" id="logo"/></a>
		<h2>Witaj {$cms_login}!</h2>
		<h2><a href="?akcja=ustawienia_cms" title="Ustawienia systemu CMS"><i class="ikona ikona_ustawienia"></i>CMS</a>
		<a href="?wyloguj" title="Wyloguj z systemu CMS"><i class="ikona ikona_klodka"></i>Wyloguj</a></h2>
	</div>
	<nav id="menu_inside">
		<ul id="menu_ul">
			<li><a href="{$ustawienia.base_url}/cms" title="Home"><i class="ikona ikona_ok2"></i>Home</a></li>
			<li><a href="?akcja=konkursy" title="Konkursy"><i class="ikona ikona_twarz"></i>Konkursy</a></li>
			<li><a href="?akcja=kategorie" title="Kategorie"><i class="ikona ikona_zawieszka"></i>Kategorie</a></li>
			<li><a href="?akcja=tagi" title="Tagi"><i class="ikona ikona_tag"></i>Tagi</a></li>
			<li><a href="?akcja=obrazki&poczekalnia" title="Obrazki i filmy w poczekalni"><i class="ikona ikona_zegarek"></i>Poczekalnia</a></li>
			<li><a href="?akcja=obrazki" title="Wszystkie obrazki i filmy"><i class="ikona ikona_video"></i>Obrazki i filmy</a>
				{if isset($kategorie)}
				<ul>
					<li><a href="?akcja=obrazki" title="Wszystkie obrazki i filmy" class="green_menu">Wszystkie</a></li>
					<li><a href="?akcja=obrazki&poczekalnia" title="Obrazki i filmy w poczekalni" class="green_menu">Poczekalnia</a></li>
					{foreach key=key item=item from=$kategorie name=kategorie}
					<li><a href="?akcja=obrazki&id={$item.id}&nazwa={$item.prosta_nazwa}" title="Kategoria: {$item.nazwa}" class="kategorie_podmenu_1">{$item.nazwa|replace:' ':'&nbsp;'}</a></li>
						{if isset($item.podkategorie)}
							{foreach key=key2 item=item2 from=$item.podkategorie name=kategorie2}
								<li><a href="?akcja=obrazki&id={$item2.id}&nazwa={$item2.prosta_nazwa}" title="Kategoria: {$item2.nazwa}" class="kategorie_podmenu_2">{$item2.nazwa|replace:' ':'&nbsp;'}</a></li>
								{if isset($item2.podkategorie)}
									{foreach key=key3 item=item3 from=$item2.podkategorie name=kategorie3}
										<li><a href="?akcja=obrazki&id={$item3.id}&nazwa={$item3.prosta_nazwa}" title="Kategoria: {$item3.nazwa}" class="kategorie_podmenu_3">{$item3.nazwa|replace:' ':'&nbsp;'}</a></li>
									{/foreach}
								{/if}
							{/foreach}
						{/if}
					{/foreach}
				</ul>	
				{/if}		
			</li>
			<li><a href="?akcja=stworzone" title="Stworzone obrazki"><i class="ikona ikona_telefon"></i>Stworzone obrazki</a></li>
			<li><a href="?akcja=dodaj_z_dysku" title="Dodaj obrazki z dysku komputera"><i class="ikona ikona_telefon"></i>Dodaj obrazki z dysku</a></li>
			<li><a href="?akcja=uzytkownicy" title="Użytkownicy"><i class="ikona ikona_twarz"></i>Użytkownicy</a>
				<ul>
					<li><a href="?akcja=uzytkownicy&nieaktywni" title="Nieaktywni użytkownicy" class="green_menu">Nieaktywni</a></li>
					<li><a href="?akcja=uzytkownicy&aktywni" title="Aktywni użytkownicy">Aktywni</a></li>
					<li><a href="?akcja=uzytkownicy&moderatorzy" title="Moderatorzy serwisu">Moderatorzy</a></li>
					<li><a href="?akcja=uzytkownicy" title="Wszyscy użytkownicy">Wszyscy</a></li>
				</ul>
			</li>
			<li><a href="?akcja=komentarze" title="Komentarze"><i class="ikona ikona_dokumenty"></i>Komentarze</a>
				{if isset($kategorie)}
				<ul>
					<li><a href="?akcja=komentarze" title="Wszystkie komentarze" class="green_menu">Wszystkie</a></li>
					{foreach key=key item=item from=$kategorie name=kategorie}
					<li><a href="?akcja=komentarze&id={$item.id}&nazwa={$item.prosta_nazwa}" title="Komentarze w kategorii: {$item.nazwa}" class="kategorie_podmenu_1">{$item.nazwa}</a></li>
						{if isset($item.podkategorie)}
							{foreach key=key2 item=item2 from=$item.podkategorie name=kategorie2}
								<li><a href="?akcja=komentarze&id={$item2.id}&nazwa={$item2.prosta_nazwa}" title="Komentarze w kategorii: {$item2.nazwa}" class="kategorie_podmenu_2">{$item2.nazwa}</a></li>
								{if isset($item2.podkategorie)}
									{foreach key=key3 item=item3 from=$item2.podkategorie name=kategorie3}
										<li><a href="?akcja=komentarze&id={$item3.id}&nazwa={$item3.prosta_nazwa}" title="Komentarze w kategorii: {$item3.nazwa}" class="kategorie_podmenu_3">{$item3.nazwa}</a></li>
									{/foreach}
								{/if}
							{/foreach}
						{/if}
					{/foreach}
				</ul>	
				{/if}		
			</li>
			<li><a href="?akcja=boksy" title="Boksy - edytuj"><i class="ikona ikona_olowek"></i>Boksy - edycja</a></li>
			<li><a href="?akcja=memy_obrazki" title="Edytuj obrazki do memów"><i class="ikona ikona_telefon"></i>Memy - obrazki</a></li>
			<li><a href="?akcja=onas" title="O nas - edytuj"><i class="ikona ikona_dokument"></i>O nas - edycja</a></li>
			<li><a href="?akcja=regulamin" title="Regulamin i polityka prywatności - edytuj"><i class="ikona ikona_skrzynka"></i>Regulamin i PP</a></li>
			<li><a href="?akcja=logo" title="Edytuj logo i znak wodny"><i class="ikona ikona_telefon"></i>Logo i znak wodny</a></li>
			<li><a href="?akcja=ustawienia" title="Ustawienia"><i class="ikona ikona_ustawienia2"></i>Ustawienia</a></li>
			<li><a href="?akcja=jezyki" title="Języki - edytuj"><i class="ikona ikona_dokument"></i>Języki</a></li>
			<li><a href="?akcja=maile" title="Maile - edytuj"><i class="ikona ikona_olowek"></i>Maile - edycja</a></li>
			<li><a href="?akcja=automatyzacja" title="Automatyzacja"><i class="ikona ikona_budzik"></i>Automatyzacja</a></li>
		</ul>
	</nav>
</aside>
<section id="strona">
	{include file="$strona.html"}
</section>
<footer>CMS v3 Copyright and project © 2014 - 2016 by <a href="http://wyremski.pl" target="_blank" title="Tworzenie Stron Internetowych">Kamil Wyremski</a>. All rights reserved.</footer>
</body>
</html>
