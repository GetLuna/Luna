<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';


if (!$pun_user['is_admmod'])
	message($lang_common['No permission'], false, '403 Forbidden');

// Load the admin_index.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_index.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['Index']);
define('PUN_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
	generate_admin_menu('');

?>
<div class="content">
    <h2><span><?php echo $lang_admin_index['Forum admin head'] ?></span></h2>
    <p><?php echo $lang_admin_index['Welcome to admin'] ?></p>
    <ul>
        <li><span><?php echo $lang_admin_index['Welcome 1'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 2'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 3'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 4'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 5'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 6'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 7'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 8'] ?></span></li>
        <li><span><?php echo $lang_admin_index['Welcome 9'] ?></span></li>
    </ul>

    <h2><span><?php echo $lang_admin_index['About head'] ?></span></h2>
    <dl>
        <dt><?php echo $lang_admin_index['Server statistics label'] ?></dt>
        <dd>
            <a href="admin_statistics.php"><?php echo $lang_admin_index['View server statistics'] ?></a>
        </dd>
    </dl>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
