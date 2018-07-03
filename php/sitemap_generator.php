<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

function sitemap_generator(){
	global $ustawienia, $tlumaczenia_linki;
	
	$memory = '64M'; 
	$sitemapFile = dirname(__FILE__)."/../sitemap.xml";

	chmod($sitemapFile, 0777);

	$sitemap_links = array();

	$sitemap_links[] = array('priority'=>'1','url'=>'');
	
	foreach($tlumaczenia_linki as $link => $url){
		if(!(($link=='stworz' and !$ustawienia['tworzenie']) or ($link=='konkursy' and !$ustawienia['konkursy']) or $link=='jezyk' or $link=='podstawowy' or $link=='kategoria' or $link=='tag' or $link=='konto' or $link=='profil' or $link=='edycja' or $link=='konkurs')){
			$sitemap_links[] = array('priority'=>'0.5','url'=>$url);
		}
	}
	
	$q = mysql_query('select id, prosty_tytul from obrazki order by id desc limit 300');
	while($dane = mysql_fetch_array($q)){
		$sitemap_links[] = array('priority'=>'0.9','url'=>$dane['id'].','.$dane['prosty_tytul']);
	}
	
	$q = mysql_query('select id, prosty_tytul from konkursy where start < CURRENT_DATE() order by wlaczony desc');
	while($dane = mysql_fetch_array($q)){
		$sitemap_links[] = array('priority'=>'0.2','url'=>$tlumaczenia_linki['konkurs'].'/'.$dane['id'].','.$dane['prosty_tytul']);
	}
	
	$q = mysql_query('select login from uzytkownicy where aktywny=1 limit 150');
	while($dane = mysql_fetch_array($q)){
		$sitemap_links[] = array('priority'=>'0.4','url'=>$tlumaczenia_linki['profil'].'/'.$dane['login']);
	}

	ini_set('memory_limit', $memory);

	$fh = fopen($sitemapFile, 'w');

	$html = '<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
	fwrite($fh, $html);

	foreach($sitemap_links as $row){
		$entry = "\n";
		$entry .= '<url>';
		$entry .= "\n";
		$entry .= '  <loc>'.$ustawienia['base_url'].'/'.$row['url'].'</loc>';
		$entry .= "\n";
		$entry .= '  <changefreq>daily</changefreq>';
		$entry .= "\n";
		$entry .= '  <priority>'.$row['priority'].'</priority>';
		$entry .= "\n";
		$entry .= '</url>';
		fwrite($fh, $entry);
	}

	$html = '
	</urlset>';
	fwrite($fh, $html);
	fclose($fh);

	chmod($sitemapFile, 0644);
}

