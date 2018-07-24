<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', dirname(__FILE__).'/');
require LUNA_ROOT.'include/common.php';
require LUNA_ROOT.'include/parser.php';

if ($luna_user['g_view_users'] == '0') {
    message(__('You do not have permission to access this page.', 'luna'), false, '403 Forbidden');
}

// Load the me functions script
require LUNA_ROOT.'include/me_functions.php';
require LUNA_ROOT.'include/email.php';
require LUNA_ROOT.'include/class/user.class.php';

// Include UTF-8 function
require LUNA_ROOT.'include/utf8/substr_replace.php';
require LUNA_ROOT.'include/utf8/ucwords.php'; // utf8_ucwords needs utf8_substr_replace
require LUNA_ROOT.'include/utf8/strcasecmp.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2) {
    message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
}

$result = $db->query('SELECT u.*, g.g_id, g.g_user_title, g.g_moderator FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id='.$id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
    message(__('Bad request. The link you followed is incorrect, outdated or you are simply not allowed to hang around here.', 'luna'), false, '404 Not Found');
}

$user = User::withRow( $db->fetch_assoc( $result ) );

// View or edit?
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']).' / '.__('Profile', 'luna'));
define('LUNA_ACTIVE_PAGE', 'me');
include LUNA_ROOT.'header.php';

require load_page('header.php');
require load_page('profile.php');
require load_page('footer.php');