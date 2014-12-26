<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Update']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'update');
	
	?>
<div class="panel panel-default">
	<div class="panel-heading">
    	<h3 class="panel-title"><?php echo $lang['Luna updates'] ?></h3>
    </div>
    <div class="panel-body">
		<h3>You're using a development version of Luna. Be sure to stay up-to-date.</h3>
		<p>We release every now and then a new build for Luna, one more stable then the other, for you to check out. You can keep track of this at <a href="http://getluna.org/lunareleases.php">our website</a>. New builds can contain new features, improved features, and/or bugfixes. Note that the updater is not able to see these builds and thus, won't notify you.</p>
    </div>
</div>
<?php

require 'footer.php';
