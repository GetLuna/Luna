<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the admin_index.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_index.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['Index']);
define('PUN_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
	generate_admin_menu('index');

//Update checking
$latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/version2.0/version.txt'));
if (preg_match("/^[0-9.-]{1,}$/", $latest_version)) {
	if (FORUM_VERSION < $latest_version) { ?>
		<div class="alert alert-info alert-update">
          <h4><?php echo sprintf($lang_admin_common['Available'], $latest_version) ?></h4>
          <?php echo $lang_admin_common['Update info'] ?><br />
          <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>" class="btn btn-primary"><?php echo sprintf($lang_admin_common['Download'], $latest_version) ?></a>
          <a href="http://modernbb.be/changelog.php#modernbb<?php echo $latest_version ?>" class="btn btn-primary"><?php echo $lang_admin_common['Changelog'] ?></a>
          <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>" class="btn"><?php echo sprintf($lang_admin_common['Download'], FORUM_VERSION) ?></a>
        </div>
    <?php }
}
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
</div>
<div class="content">
    <h2><span><?php echo $lang_admin_index['About head'] ?></span></h2>
    <dl>
        <dt><?php echo $lang_admin_index['ModernBB version label'] ?></dt>
        <dd>
            <?php printf($lang_admin_index['ModernBB version data'].'<a href="about.php">'.$pun_config['o_cur_version'].'</a>') ?>
        </dd>
        <dt><?php echo $lang_admin_index['Server statistics label'] ?></dt>
        <dd>
            <a href="statistics.php"><?php echo $lang_admin_index['View server statistics'] ?></a>
        </dd>
    </dl>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
