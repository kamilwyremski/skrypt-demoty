{if isset($boksy)}
	{foreach item=itemBoks key=key from=$boksy name=boksy}
		{if $itemBoks.rodzaj=='tekst'}<div class="boks boks_tekst">{$itemBoks.tresc}</div>
		{elseif $itemBoks.rodzaj=='statystyki'}
			<div class="boks boks_statystyki">
				<h3>{$tlumaczenia_teksty.statystyki}</h3>
				<table>
					<tr><td>{$tlumaczenia_teksty.obrazkow_i_filmow}:</td><td>{$statystyki.obrazki}</td></tr>
					<tr><td>{$tlumaczenia_teksty.w_poczekalni}:</td><td>{$statystyki.poczekalnia}</td></tr>
					<tr><td>{$tlumaczenia_teksty.komentarzy}:</td><td>{$statystyki.komentarze}</td></tr>
					<tr><td>{$tlumaczenia_teksty.glosow}:</td><td>{$statystyki.glosy}</td></tr>
					<tr><td>{$tlumaczenia_teksty.kategorii}:</td><td>{$statystyki.kategorie}</td></tr>
					<tr><td>{$tlumaczenia_teksty.tagow}:</td><td>{$statystyki.tagi}</td></tr>
					{if isset($statystyki.stworzone)}<tr><td>{$tlumaczenia_teksty.stworzonych}:</td><td>{$statystyki.stworzone}</td></tr>{/if}
					<tr><td>{$tlumaczenia_teksty.uzytkownikow}:</td><td>{$statystyki.uzytkownicy}</td></tr>
				</table>
			</div>
		{elseif $itemBoks.rodzaj=='kategorie' and isset($kategorie)}
			<div class="boks boks_kategorie">
				<h3>{$tlumaczenia_teksty.kategorie}</h3>
				<ul>
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
				</ul>	
			</div>
		{elseif ($itemBoks.rodzaj=='top' and isset($top)) || ($itemBoks.rodzaj=='nowe' and isset($nowe))}
			<div class="boks boks_miniaturki">
				{if $itemBoks.rodzaj=='top'}
					<h3>{$tlumaczenia_teksty.najlepsze}</h3>
					{assign var="tablica" value=$top}
				{else}
					<h3>{$tlumaczenia_teksty.najnowsze}</h3>
					{assign var="tablica" value=$nowe}
				{/if}				
				{foreach key=key item=item from=$tablica name=tablica}
					{if $smarty.foreach.tablica.index == $itemBoks.ilosc}
						{break}
					{/if}
					{include file="miniaturki.tpl"}
				{/foreach}
			</div>	
		{elseif $itemBoks.rodzaj=='komentarze' && isset($nowe_komentarze)}
			<div class="boks boks_komentarze">
				<h3>{$tlumaczenia_teksty.najnowsze_komentarze}</h3>
				{foreach key=key item=item from=$nowe_komentarze name=nowe_komentarze}
					{if $smarty.foreach.nowe_komentarze.index == $itemBoks.ilosc}
						{break}
					{/if}
					<div class="boks_komentarz">
						<p><a href="{$ustawienia.base_url}/{$item.id},{$item.prosty_tytul}" title="{$item.tytul}">{$item.tresc|truncate:160}</a></p>
						<p class="autor"><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.profil}/{$item.login}" title="{$item.login}">{$item.login}</a>, {$item.data|date_format:"%d-%m-%Y"} o <a href="{$ustawienia.base_url}/{$item.id},{$item.prosty_tytul}" title="{$item.tytul}">{$item.tytul}</a></p>
					</div>
				{/foreach}
			</div>
		{elseif $itemBoks.rodzaj=='wyszukiwarka'}
			<div class="boks boks_wyszukiwarka">
				<h3>{$tlumaczenia_teksty.wyszukiwarka}</h3>
				<form action="{$ustawienia.base_url}" method="get">
					<input type="text" name="search" placeholder="{$tlumaczenia_teksty.szukaj}" title="{$tlumaczenia_teksty.szukaj}" required {if isset($smarty.get.search)}value="{$smarty.get.search}"{/if}>
					<input type="submit" value=""/>
				</form>
				<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.uzytkownicy}" title="{$tlumaczenia_teksty.wyszukiwarka_uzytkownikow}">{$tlumaczenia_teksty.wyszukiwarka_uzytkownikow}</a>
			</div>
		{elseif $itemBoks.rodzaj=='tagi' and isset($tagi)}
			<div class="boks boks_tagi">
				<h3>{$tlumaczenia_teksty.tagi}</h3>
				{foreach key=key item=item from=$tagi name=tagi}
					<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.tag}/{$item.prosta_nazwa}" title="{$tlumaczenia_teksty.tag}: {$item.nazwa}" style="font-size: {$item.rozmiar}px">{$item.nazwa}</a>
				{/foreach}
			</div>
		{elseif $itemBoks.rodzaj=='mapa' and $ustawienia.mapa==1}
			<div class="boks boks_mapa">
				<h3>{$tlumaczenia_teksty.mapa_obiektow}</h3>
				<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.mapa}" title="{$tlumaczenia_teksty.mapa_obiektow}"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/mapa.jpg" alt="{$tlumaczenia_teksty.mapa_obiektow}" id="mapa_miniatura"></a>
			</div>
		{elseif $itemBoks.rodzaj=='stopka'}
			<div class="boks boks_stopka">
				<h3>{$tlumaczenia_teksty.mapa_strony}</h3>
				<ul>
					<li><a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}">{$ustawienia.tytul|truncate:32}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.onas}" title="{$tlumaczenia_teksty.onas}">{$tlumaczenia_teksty.onas}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.top}" title="{$tlumaczenia_teksty.top}">{$tlumaczenia_teksty.top}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.poczekalnia}" title="{$tlumaczenia_teksty.poczekalnia}">{$tlumaczenia_teksty.poczekalnia}</a></li>
					{if $ustawienia.mapa==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.mapa}" title="{$tlumaczenia_teksty.mapa_obiektow}">{$tlumaczenia_teksty.mapa_obiektow}</a></li>{/if}
					{if $ustawienia.mapa_uzytkownikow==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.mapa_uzytkownikow}" title="{$tlumaczenia_teksty.mapa_uzytkownikow}">{$tlumaczenia_teksty.mapa_uzytkownikow}</a></li>{/if}
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.dodaj}" title="{$tlumaczenia_teksty.dodaj}">{$tlumaczenia_teksty.dodaj}</a></li>
					{if $ustawienia.tworzenie==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.stworz}" title="{$tlumaczenia_teksty.stworz}">{$tlumaczenia_teksty.stworz}</a></li>{/if}
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.rejestracja}" title="{$tlumaczenia_teksty.rejestracja}">{$tlumaczenia_teksty.rejestracja}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.regulamin}" title="{$tlumaczenia_teksty.regulamin}">{$tlumaczenia_teksty.regulamin}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.polityka_prywatnosci}" title="{$tlumaczenia_teksty.polityka_prywatnosci}">{$tlumaczenia_teksty.polityka_prywatnosci}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.logowanie}" title="{$tlumaczenia_teksty.logowanie}">{$tlumaczenia_teksty.logowanie}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.reset_hasla}" title="{$tlumaczenia_teksty.reset_hasla}">{$tlumaczenia_teksty.reset_hasla}</a></li>
					<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.uzytkownicy}" title="{$tlumaczenia_teksty.uzytkownicy}">{$tlumaczenia_teksty.uzytkownicy}</a></li>
					{if $ustawienia.tworzenie==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.stworzone}" title="{$tlumaczenia_teksty.stworzone}">{$tlumaczenia_teksty.stworzone}</a></li>{/if}
					{if $ustawienia.konkursy==1}<li><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.konkursy}" title="{$tlumaczenia_teksty.konkursy}">{$tlumaczenia_teksty.konkursy}</a></li>{/if}
				</ul>
			</div>
		{elseif $itemBoks.rodzaj=='konkurs' and isset($konkurs_boks)}
			<div class="boks">
				<h3>{$tlumaczenia_teksty.konkurs}!</h3>
				<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.konkurs}/{$konkurs_boks.id},{$konkurs_boks.prosty_tytul}" title="{$konkurs_boks.tytul}"><h4>{$konkurs_boks.tytul}</h4></a>
				<p style="margin-top:5px">{$tlumaczenia_teksty.data_koniec}: {$konkurs_boks.koniec|date_format:"%d-%m-%Y"}</p>
			</div>
		{/if}
	{/foreach}
{/if}
	