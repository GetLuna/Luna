<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
    header("Location: ../login.php");

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Moderate']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
load_admin_nav('content', 'moderate');

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Moderate content</h3>
	</div>
	<div class="panel-body">
		<p class="lead">Planned for Luna 1.0 Preview 2 &middot; Core 0.2.35xx</p>
		<p>This is where you'll be able to moderate your board. However, this feature isn't available in the current version, check back soon!</p>
	</div>
</div>
<?php

require 'footer.php';