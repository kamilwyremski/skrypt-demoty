
	<div class="miniaturki">
		<div class="miniaturki_relative">
		{if $item.wybor_obrazka=='z_dysku' || $item.wybor_obrazka=='stworzony'}
			<span class="miniaturka_helper"></span>
			<img src="{$ustawienia.base_url}/upload/{$item.url}" class="miniaturka" alt="{$item.tytul}" onerror="this.src='{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png'">
		{elseif $item.wybor_obrazka=='z_internetu'}
			<span class="miniaturka_helper"></span>
			<img src="{$item.url}" class="miniaturka" alt="{$item.tytul}" onerror="this.src='{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png'">
		{elseif $item.wybor_obrazka=='z_youtube' || $item.wybor_obrazka=='z_vimeo' || $item.wybor_obrazka=='z_dailymotion'}
			<span class="miniaturka_helper"></span>
			<img src="{$item.miniaturka}" class="miniaturka" alt="{$item.tytul}" onerror="this.src='{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png'">
		{/if}
			<div class="opis_miniaturki">
				<div class="miniaturka_table">
					<div class="miniaturka_table-cell">
						{if $item.glowna==0}<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.poczekalnia}" title="{$tlumaczenia_teksty.poczekalnia}">{$tlumaczenia_teksty.poczekalnia}</a></p>{/if}
						{if isset($item.nazwa)}<p><a href="{$ustawienia.base_url}/{$tlumaczenia_linki.kategoria}/{$item.prosta_nazwa}" title="{$item.nazwa}">{$item.nazwa}</a></p>{/if}
						<p><b>{$item.data|date_format:"%d-%m-%Y"}</b></p>
						<p>{$tlumaczenia_teksty.komentarzy}: <b>{$item.ile_komentarzy}</b></p>
						<p>{$tlumaczenia_teksty.glosow}: <b>{$item.glosy}</b></p>
						<a href="{$ustawienia.base_url}/{$item.id},{$item.prosty_tytul}" title="{$item.tytul}">{$tlumaczenia_teksty.zobacz}</a>
						{if $strona=='konto'}
							<a href="{$ustawienia.base_url}/{$tlumaczenia_linki.edycja}/{$item.id},{$item.prosty_tytul}" class="konto_edytuj_obrazek" title="{$tlumaczenia_teksty.edytuj}"></a>
							<a href="#" class="konto_usun_obrazek" data-id="{$item.id}" title="{$tlumaczenia_teksty.usun}"></a>
						{/if}
					</div>
				</div>
			</div>
		</div>
		<h4><a href="{$ustawienia.base_url}/{$item.id},{$item.prosty_tytul}" title="{$item.tytul}">{$item.tytul|truncate:32}</a></h4>
	</div>

