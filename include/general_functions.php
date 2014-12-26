<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Create a new notification
function new_notification($user, $link, $message, $icon) {
	global $db;
	
	$now = time();
	
	$db->query('INSERT INTO '.$db->prefix.'notifications (user_id, message, icon, link, time) VALUES('.$user.', \''.$message.'\', \''.$icon.'\', \''.$link.'\', '.$now.')') or error('Unable to add new notification', __FILE__, __LINE__, $db->error());

}