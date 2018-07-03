<?php

if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

require_once 'libs/htmlpurifier/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.SafeIframe', true);
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
$purifier = new HTMLPurifier($config);

