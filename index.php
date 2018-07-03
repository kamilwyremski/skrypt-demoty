<?php

header('Content-Type: text/html; charset=utf-8');

session_start();
ob_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(0);

include('config/db.php');

require_once('libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = 'views/'.$ustawienia['szablon'];
$smarty->compile_dir = 'tmp';
$smarty->cache_dir = 'cache';

include('php/globalne.php');
include('php/funkcje.php');
include('php/logowanie.php');
include('php/controller.php');

$smarty->assign("title", strip_tags($title));
$smarty->assign("keywords", strip_tags($keywords));
$smarty->assign("description", strip_tags($description));
$smarty->assign("ustawienia", $ustawienia);
$smarty->assign("menu",$menu);
$smarty->assign("strona",$strona);
$smarty->assign("tlumaczenia_linki",$tlumaczenia_linki);
$smarty->assign("tlumaczenia_teksty",$tlumaczenia_teksty);

$smarty->display('main.tpl');
 
