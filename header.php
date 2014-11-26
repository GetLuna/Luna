<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache'); // For HTTP/1.0 compatibility

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Prevent site from being embedded in a frame 
header('X-Frame-Options: deny'); 

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

if (isset($required_fields)) {
// Output JavaScript to validate form (make sure required fields are filled out)

?>
<script type="text/javascript">
/* <![CDATA[ */
function process_form(the_form) {
	var required_fields = {
<?php
	// Output a JavaScript object with localised field names
	$tpl_temp = count($required_fields);
	foreach ($required_fields as $elem_orig => $elem_trans) {
		echo "\t\t\"".$elem_orig.'": "'.addslashes(str_replace('&#160;', ' ', $elem_trans));
		if (--$tpl_temp) echo "\",\n";
		else echo "\"\n\t};\n";
	}
?>
	if (document.all || document.getElementById) {
		for (var i = 0; i < the_form.length; ++i) {
			var elem = the_form.elements[i];
			if (elem.name && required_fields[elem.name] && !elem.value && elem.type && (/^(?:text(?:area)?|password|file)$/i.test(elem.type))) {
				alert('"' + required_fields[elem.name] + '" <?php echo $lang['required field'] ?>');
				elem.focus();
				return false;
			}
		}
	}
	return true;
}
/* ]]> */
</script>
<?php

}

// Generate avatar
$user_avatar = draw_user_avatar($luna_user['id'], '');

// Navbar data
$links = array();
$menu_title = $luna_config['o_board_title'];

$num_new_pm = 0;
if ($luna_config['o_pms_enabled'] == '1' && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1') {
	// Check for new messages
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'messages WHERE showed=0 AND show_message=1 AND owner='.$luna_user['id']) or error('Unable to check the availibility of new messages', __FILE__, __LINE__, $db->error());
	$num_new_pm = $db->result($result);
	
	if ($num_new_pm > 0)
		$new_inbox = '<span class="label label-danger">'.$num_new_pm.'</span>&nbsp;&nbsp;&nbsp;&nbsp;';	
	else
		$new_inbox = '';	
}

if (!$luna_user['is_admmod'])
	$backstage = '';
else
	$backstage = '<li><a href="backstage/"><span class="fa fa-fw fa-tachometer"></span></a></li>';

$result = $db->query('SELECT id, url, name, disp_position, visible FROM '.$db->prefix.'menu ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

if ($luna_user['is_guest'])
	$usermenu = '<li id="navregister"'.((FORUM_ACTIVE_PAGE == 'register') ? ' class="active"' : '').'><a href="register.php">'.$lang['Register'].'</a></li>
				 <li><a href="#" data-toggle="modal" data-target="#login">'.$lang['Login'].'</a></li>';
elseif ($zset && $luna_config['o_notifications'] == '1')
	$usermenu = $backstage.'
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-circle"></i></a>
					<ul class="dropdown-menu notification-menu">
						<li role="presentation" class="dropdown-header">Notifications</li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-user text-success"></i> Odd mentioned you in "You can do anything"</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-inbox text-warning"></i> New PM from Mellow</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-arrow-circle-up text-danger"></i> Luna 0.1.3400 is available</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-user text-info"></i> New comment in "Luna Preview 1"</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="fa fa-user text-success"></i> Zoe mentioned you in "Express Yourself"</a></li>
						<li class="divider"></li>
						<li><a class="pull-right" href="#">More <i class="fa fa-arrow-right"></i></a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle avatar-item" data-toggle="dropdown">'.$new_inbox."".$user_avatar.' <span class="fa fa-fw fa-angle-down"></a>
					<ul class="dropdown-menu">
						<li><a href="inbox.php">Inbox</a></li>
						<li><a href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a></li>
						<li><a href="me.php?id='.$luna_user['id'].'">Me</a></li>
						<li class="divider"></li>
						<li><a href="help.php">'.$lang['Help'].'</a></li>
						<li class="divider"></li>
						<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a></li>
					</ul>
				</li>
	';
else
	$usermenu = $backstage.'
				<li class="dropdown">
					<a href="#" class="dropdown-toggle avatar-item" data-toggle="dropdown">'.$user_avatar.' <span class="fa fa-angle-down"></a>
					<ul class="dropdown-menu">
						<li><a href="inbox.php">Inbox</a></li>
						<li><a href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a></li>
						<li><a href="me.php?id='.$luna_user['id'].'">Me</a></li>
						<li class="divider"></li>
						<li><a href="help.php">'.$lang['Help'].'</a></li>
						<li class="divider"></li>
						<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a></li>
					</ul>
				</li>
	';

if ($db->num_rows($result) > 0)
	while ($cur_item = $db->fetch_assoc($result))
		if ($cur_item['visible'] == '1')
			$links[] = '<li><a href="'.$cur_item['url'].'">'.$cur_item['name'].'</a></li>';

require load_page('login.php');

// Announcement
if ($luna_config['o_announcement'] == '1')
	$announcement = '<div class="alert alert-info announcement"><div>'.$luna_config['o_announcement_message'].'</div></div>';
else
	$announcement = '';