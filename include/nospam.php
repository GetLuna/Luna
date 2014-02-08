<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://opensource.org/licenses/MIT MIT
 */

define('SPAM_NOT', 0);
define('SPAM_HONEYPOT', 1);
define('SPAM_BLACKLIST', 2);

//
// Check a given IP and email against the stopforumspam API
//
function stopforumspam_check($ip, $email, $username)
{
	$response = @simplexml_load_file('http://www.stopforumspam.com/api?'.http_build_query(array(
		'ip'		=> $ip,
		'email'		=> $email,
//		'username'	=> $username,	// I'm not sure checking by username is a good idea...
	)));
	if ($response === false)
		return false;

	foreach ($response->appears as $appears)
		if ($appears == 'yes')
			return true;

	return false;
}

//
// Report a spammer to stopforumspam database
//
function stopforumspam_report($ip, $email, $username)
{
	global $luna_config;
	
	$context = stream_context_create(array('http' => array(
		'method'	=> 'POST',
		'header'	=> 'Content-type: application/x-www-form-urlencoded',
		'content'	=> http_build_query(array(
			'ip_addr'	=> $ip,
			'email'		=> $email,
			'username'	=> $username,
			'api_key'	=> $luna_config['o_antispam_api'],
		)),
	)));

	return @file_get_contents('http://www.stopforumspam.com/add', false, $context) ? true : false;
}
