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


$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Profile'], $lang['Settings']);
define('FORUM_ACTIVE_PAGE', 'me');
require load_page('header.php');

require load_page('settings2.php');
require load_page('footer.php');