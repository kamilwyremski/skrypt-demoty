<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="Keywords" content="{$keywords}">
	<meta name="Description" content="{$description}">
	<title>{$title}</title>
	<base href="{$ustawienia.base_url}">
	<meta property="og:image" content="{if isset($obrazek)}{if $obrazek.wybor_obrazka=='z_dysku'}{$ustawienia.base_url}/upload/{$obrazek.url}{elseif $obrazek.wybor_obrazka=='z_internetu'}{$obrazek.url}{elseif $obrazek.wybor_obrazka=='z_youtube'}{$obrazek.miniaturka}{/if}{else}{$ustawienia.base_url}/obrazy/logo_facebook.png{/if}"/>
	<meta property="og:description" content="{$description}"/>
	<meta property="og:title" content="{$title}"/>
	
	<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet'>
	<link href='http://fonts.googleapis.com/css?family=Oswald&subset=latin,latin-ext' rel='stylesheet'>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,latin-ext' rel='stylesheet'>
	<link rel="stylesheet" href="{$ustawienia.base_url}/views/{$ustawienia.szablon}/css/style.css"/>
	<link rel="stylesheet" href="{$ustawienia.base_url}/views/{$ustawienia.szablon}/css/menu.css"/>
	<link rel="stylesheet" href="{$ustawienia.base_url}/views/{$ustawienia.szablon}/css/mobile.css"/>
	<link rel="shortcut icon" href="{$ustawienia.base_url}/obrazy/favicon.ico"/>
	
	<script src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/js/jquery-2.1.4.min.js"></script>
	<script src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/js/jquery.slimscroll.min.js"></script>
	<script>
		var tlumaczenia_teksty = [];
		tlumaczenia_teksty['na_pewno_usunac'] = "{$tlumaczenia_teksty.na_pewno_usunac|escape:javascript}";
		tlumaczenia_teksty['usunac_komentarz'] = "{$tlumaczenia_teksty.usunac_komentarz|escape:javascript}";
		tlumaczenia_teksty['cookies_tekst'] = "{$tlumaczenia_teksty.cookies_tekst|escape:javascript}";
		tlumaczenia_teksty['cookies_zamknij'] = "{$tlumaczenia_teksty.cookies_zamknij|escape:javascript}";
		tlumaczenia_teksty['komentarz_niezalogowany'] = "{$tlumaczenia_teksty.komentarz_niezalogowany|escape:javascript}";
	</script>
	<script src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/js/engine.js"></script>
	<script src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/js/whcookies.js"></script>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/sdk.js#xfbml=1&appId={$ustawienia.facebook_api}&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
{if $menu}
<nav id="menu">
	{if $ustawienia.logo_obrazek==1}
		<a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}" id="logo"><img src="{$ustawienia.base_url}/obrazy/logo.png" alt="{$ustawienia.tytul}" onerror="this.src=''"></a><h1 style="display: none">{$ustawienia.tytul}</h1>
	{else}
		<h1><a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}">{$ustawienia.tytul}</a></h1>
	{/if}
	<ul>
		{if $ustawienia.onas!=''}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.onas}" title="{$tlumaczenia_teksty.menu_onas}">{$tlumaczenia_teksty.menu_onas}</a></li>{/if}
		{if isset($kategorie)}
			<li><a href="{$ustawienia.base_url}" title="{$tlumaczenia_teksty.menu_kategorie}" id="menu_kategorie">{$tlumaczenia_teksty.menu_kategorie} +</a>
				<ul>
					<div id="menu_scroll">
					{foreach key=key item=item from=$kategorie name=kategorie}
						<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.kategoria}/{$item.prosta_nazwa}" title="{$item.nazwa}">{$item.nazwa}</a>
							{if isset($item.podkategorie)}
							<ul>
								{foreach key=key2 item=item2 from=$item.podkategorie name=kategorie2}
									<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.kategoria}/{$item2.prosta_nazwa}" title="{$item2.nazwa}">{$item2.nazwa}</a>
										{if isset($item2.podkategorie)}
										<ul>
											{foreach key=key3 item=item3 from=$item2.podkategorie name=kategorie3}
												<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.kategoria}/{$item3.prosta_nazwa}" title="{$item3.nazwa}">{$item3.nazwa}</a></li>
											{/foreach}
										</ul>
										{/if}
									</li>
								{/foreach}
							</ul>
							{/if}
						</li>
					{/foreach}
					</div>
				</ul>	
			</li>
		{/if}	
		<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.top}" title="{$tlumaczenia_teksty.menu_top}">{$tlumaczenia_teksty.menu_top}</a></li>
		<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.dodaj}" title="{$tlumaczenia_teksty.menu_dodaj}">{$tlumaczenia_teksty.menu_dodaj}</a></li>
		{if $ustawienia.tworzenie==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.stworz}" title="{$tlumaczenia_teksty.menu_stworz}">{$tlumaczenia_teksty.menu_stworz}</a></li>{/if}
		<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.poczekalnia}" title="{$tlumaczenia_teksty.menu_poczekalnia}">{$tlumaczenia_teksty.menu_poczekalnia}</a></li>
		{if $ustawienia.mapa==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.mapa}" title="{$tlumaczenia_teksty.menu_mapa_obiektow}">{$tlumaczenia_teksty.menu_mapa_obiektow}</a></li>{/if}
		{if $ustawienia.konkursy==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.konkursy}" title="{$tlumaczenia_teksty.menu_konkursy}">{$tlumaczenia_teksty.menu_konkursy}</a></li>{/if}
		{if isset($uzytkownik)}
			<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.konto}" title="{$tlumaczenia_teksty.menu_konto}">{$tlumaczenia_teksty.menu_konto}</a></li>
			<li><a href="{$ustawienia.base_url}?log_out" title="{$tlumaczenia_teksty.menu_wyloguj}" style="text-decoration: underline">{$tlumaczenia_teksty.menu_wyloguj}</a></li>
		{else}
			<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.rejestracja}" title="{$tlumaczenia_teksty.menu_rejestracja}">{$tlumaczenia_teksty.menu_rejestracja}</a></li>
			<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.logowanie}" title="{$tlumaczenia_teksty.menu_logowanie}" style="text-decoration: underline" {if $ustawienia.logowanie_facebook==1 && isset($url_facebook)}class="logowanie_facebook_mini"{/if}>{$tlumaczenia_teksty.menu_logowanie}</a>{if $ustawienia.logowanie_facebook==1 && isset($url_facebook)}<a href="{$url_facebook}" title="{$tlumaczenia_teksty.zaloguj_przez_fb}" class="logowanie_facebook_mini"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/fb-icon.gif"></a>{/if}</li>
		{/if}
	</ul>
</nav>
<div id="strona">
	<section id="strona_lewo">
		<div id="strona_lewo_inside">
		
			{include file="$strona.html"}
			
			{if $ustawienia.adsense!='' && $strona!='index'}
				<div class="adsense">
					{$ustawienia.adsense}
				</div>
			{/if}
			
			{if isset($losowe_obrazki)}
				<div id="losowe_obrazki">
					<h3>{$tlumaczenia_teksty.losowo_wybrane}</h3>
					{foreach item=item key=key from=$losowe_obrazki}
						{include file="miniaturki.tpl"}
					{/foreach}
				</div>
			{/if}
		</div>
	</section>
	<section id="strona_prawo">
	
		{include file="panel.tpl"}
		
	</section>
</div>
<footer>
	<p>{$tlumaczenia_teksty.stopka_opis}</p>
	<p>Copyright © 2017 by <a href="{$ustawienia.stopka_url}" target="_blank" title="{$ustawienia.stopka_nazwa}">{$ustawienia.stopka_nazwa}</a></p>	
	{$ustawienia.stopka}
</footer>
{else}
	{include file="$strona.html"}
{/if}

{if $ustawienia.facebook!=''}
<div id="facebook2_2" style="right: -304px; z-index: 10001; background: #ffffff; padding: 0px; width: 300px; height:80%; max-height: 520px; position: fixed; top: 62px; border: 2px solid #3B95D8">
	<a href="#" target="_blank"></a><img style="position: absolute; left:-33px;" src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/facebook.png" alt="Facebook"/>  
	<div class="fb-like-box" data-href="{$ustawienia.facebook}" data-width="300" data-height="510" data-show-faces="true" data-stream="true" data-header="false"></div>
</div>
{/if}

{$ustawienia.analytics}

</body>
</html>
