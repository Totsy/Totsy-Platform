<?php
/**
 * Configure Authentication and Access Control. Request are checked
 * against the session based form adapter.
 */
use lithium\security\Auth;
use lithium\action\Dispatcher;
use lithium\action\Response;

Auth::config(array(
	'userLogin' => array(
		'model' => 'User',
		'adapter' => 'Form',
		'fields' => array('email', 'password')
	)
));

Dispatcher::applyFilter('_call', function($self, $params, $chain) {
	$skip = array('login', 'logout', 'register',"register/facebook","reset");
	$allowed = false;

	#dynamic affiliate pages
	 if(preg_match('#(^a/)[a-zA-Z_]+#', $params['request']->url)) {
		 $allowed = true;
	 }
	 if (array_key_exists('a',$params['request']->query )) {
		 $allowed = true;
	 }
	 #join and invites
	 if(preg_match('#(^invitation/)[a-zA-Z0-9\+_]+#', $params['request']->url)) {
		 $allowed = true;
	 }
	 if(preg_match('#(^join/)[a-zA-Z0-9\+_]+#', $params['request']->url)) {
		 $allowed = true;
	 }
	 #static pages
	 if(preg_match('#(pages/)#', $params['request']->url)) {
		 $allowed = true;
	 }
	 
	 #API
	 if(preg_match('#(api/)#', $params['request']->url)) {
	 	$allowed = true;
	 }
	 
	$granted = in_array($params['request']->url, $skip);
	$granted = $allowed || $granted;
	$granted = $granted || Auth::check('userLogin', $params['request']);



//checks for sailhtur get var gotologin=true, saves event name and redirs to login
if ($params['request']->query['gotologin']=="true") {

	$eventName = "";
	
	if (preg_match("(/sale)", $currentURI)) {
	$URIArray = explode("/", $currentURI);
	$eventName = $URIArray[2];
	}
	
	if ($eventName) {
	//write event name to the session
	Session::write( "eventFromEmailClick", $eventName, array("name"=>"default"));
	}
	
	
	
	return new Response(array('location' => 'Users::login'));
}


	if (!$granted) {
		/* Redirect all non-authenticated users to login page. */
		return new Response(array('location' => 'Users::register'));
	}
	return $chain->next($self, $params, $chain);
});

?>