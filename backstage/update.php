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

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Index']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('index');
	
	?>
<h2>ModernBB software updates</h2>
<?php
//Update checking
	?>
		<div class="alert alert-info">
		<?php
        $latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/master/version.txt'));
        if (version_compare(FORUM_VERSION, $latest_version, 'lt')) { ?>
            <h4><?php echo sprintf($lang['Available'], $latest_version) ?></h4>
            <div class="btn-group">
                <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], $latest_version) ?></a>
                <a href="http://modernbb.be/releases/modernbb<?php echo $latest_version ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
            </div>
            <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-default"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
        <?php } elseif (version_compare(FORUM_VERSION, $latest_version, 'eq')) { ?>
            <h4><?php echo $lang['ModernBB intro'].' '.FORUM_VERSION ?></h4>
            <div class="btn-group">
                <a href="http://modernbb.be/releases/modernbb<?php echo FORUM_VERSION ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
                <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
            </div>
		<?php	} else { ?>
            <h4><?php echo sprintf($lang['Development'], FORUM_VERSION, $latest_version) ?></h4>
            <div class="btn-group">
                <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], $latest_version) ?></a>
                <a href="http://modernbb.be/releases/modernbb<?php echo $latest_version ?>.php" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
            </div>
            <div class="btn-group">
                <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-default"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
                <a href="http://modernbb.be/releases/modernbb<?php echo FORUM_VERSION ?>.php" class="btn btn-default"><?php echo $lang['Changelog'] ?></a>
            </div>
		</div>
<?php
    }

require FORUM_ROOT.'backstage/footer.php';
