<?php
 
if(!isset($ustawienia['base_url'])){
	die('brak dostepu');
}

require_once( realpath(dirname(__FILE__)).'/../libs/facebook/HttpClients/FacebookHttpable.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/HttpClients/FacebookCurl.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/HttpClients/FacebookStreamHttpClient.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/HttpClients/FacebookStream.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookSession.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookRedirectLoginHelper.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookRequest.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookResponse.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookSDKException.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookRequestException.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookOtherException.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/FacebookAuthorizationException.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/GraphObject.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/GraphLocation.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/GraphUser.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/GraphSessionInfo.php' );
require_once( realpath(dirname(__FILE__)).'/../libs/facebook/Entities/AccessToken.php');

// Called class with namespace
use Facebook\FacebookHttpable;
use Facebook\FacebookCurl;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphLocation;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;

FacebookSession::setDefaultApplication($ustawienia['facebook_api'],$ustawienia['facebook_secret']);

$helper = new FacebookRedirectLoginHelper( $ustawienia['base_url'].'/?scope=publish_actions' );
$scope = array('email');

try {
  $session = $helper->getSessionFromRedirect();
}catch( FacebookRequestException $ex ) {
  // Exception
}catch( Exception $ex ) {
  // When validation fails or other local issues
}
 
if(isset($session)){
	$request = new FacebookRequest( $session, 'GET', '/me?fields=email,first_name,last_name' );
	$response = $request->execute();
	$object = $response->getGraphObject();
	require_once(realpath(dirname(__FILE__)).'/../model/rejestracja.php');	
	list($id, $login) = rejestracja_facebook($object); 
	if($id!='' and $login!=''){
		$_SESSION['uzytkownik'] = $login;
		$_SESSION['uzytkownik_id'] = $id;
		logowanie();
	}else{
		$smarty->assign("url_facebook", $helper->getLoginUrl($scope));
	}
}else{
	$smarty->assign("url_facebook", $helper->getLoginUrl($scope));
}
