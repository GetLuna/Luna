<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/parser.php';

// Load the me functions script
require FORUM_ROOT.'include/me_functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : $luna_user['id'];

if ($luna_user['is_admmod']) {
	if ($luna_user['g_id'] == FORUM_ADMIN || $luna_user['g_mod_rename_users'] == '1')
		$username_field = '<input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($user['username']).'" maxlength="25" />';
	else
		$username_field = luna_htmlspecialchars($user['username']);

	$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" />';
	$email_button = '<span class="input-group-btn"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a></span>';
} else {
	$username_field = '<div class="col-sm-9"><input class="form-control" type="text"  value="'.luna_htmlspecialchars($user['username']).'" disabled="disabled" />';

	if ($luna_config['o_regs_verify'] == '1') {
		$email_field = '<input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" disabled />';
		$email_button = '<span class="input-group-btn"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newmail">'.$lang['Change email'].'</a></span>';
	} else {
		$email_field = '<input type="text" class="form-control" name="req_email" value="'.$user['email'].'" maxlength="80" />';
		$email_button = '';
	}
}

if ($luna_user['g_set_title'] == '1')
	$title_field = '<input class="form-control" type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($user['title']).'" maxlength="50" />';

$avatar_user = draw_user_avatar($id, 'visible-lg-inline');
$avatar_set = check_avatar($id);
if ($user_avatar && $avatar_set)
	$avatar_field .= ' <a class="btn btn-primary" href="me.php?action=delete_avatar&amp;id='.$id.'">'.$lang['Delete avatar'].'</a>';
else
	$avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.$lang['Upload avatar'].'</a>';

if ($user['signature'] != '')
	$signature_preview = $parsed_signature;
else
	$signature_preview = $lang['No sig'];

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Settings']);
define('FORUM_ACTIVE_PAGE', 'me');
require load_page('header.php');
require load_page('settings2.php');
require load_page('footer.php');