<?php

if(!isset($cms_login)){
	die('brak dostepu');
}

$q = mysql_query('select * from tagi order by nazwa');
while($dane = mysql_fetch_array($q)){$tagi[] = $dane;}
if(isset($tagi)){$smarty->assign("tagi", $tagi);}
	
