<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

// The plugin to load should be supplied via GET
$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
if (!preg_match('%^AM?P_(\w*?)\.php$%i', $plugin))
	message($lang_common['Bad request'], false, '404 Not Found');

// AP_ == Admins only, AMP_ == admins and moderators
$prefix = substr($plugin, 0, strpos($plugin, '_'));
if ($pun_user['g_moderator'] == '1' && $prefix == 'AP')
	message($lang_common['No permission'], false, '403 Forbidden');

// Make sure the file actually exists
if (!file_exists(FORUM_ROOT.'plugins/'.$plugin))
	message(sprintf($lang_back['No plugin message'], $plugin));

// Construct REQUEST_URI if it isn't set
if (!isset($_SERVER['REQUEST_URI']))
	$_SERVER['REQUEST_URI'] = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '').'?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_common['Admin'], str_replace('_', ' ', substr($plugin, strpos($plugin, '_') + 1, -4)));
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';

// Attempt to load the plugin. We don't use @ here to suppress error messages,
// because if we did and a parse error occurred in the plugin, we would only
// get the "blank page of death"
include FORUM_ROOT.'plugins/'.$plugin;
if (!defined('FORUM_PLUGIN_LOADED'))
	message(sprintf($lang_back['Plugin failed message'], $plugin));

// Output the clearer div
?>
	<div class="clearer"></div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
