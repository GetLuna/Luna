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

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['About']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('about');

//Update checking
?>
<div class="alert alert-info">
<?php
    if ($pun_config['o_index_update_check'] == 1) {
        $latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/master/version.txt'));
        if (version_compare(FORUM_VERSION, $latest_version, '<')) { ?>
            <h4><?php echo sprintf($lang['Available'], $latest_version) ?></h4>
            <div class="btn-group">
                <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], $latest_version) ?></a>
                <a href="http://modernbb.be/changelog.php#modernbb<?php echo $latest_version ?>" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
            </div>
            <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-default"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
        <?php } else { ?>
            <h4><?php echo $lang['ModernBB intro'].' '.FORUM_VERSION ?></h4>
            <div class="btn-group">
                <a href="http://modernbb.be/changelog.php#modernbb<?php echo FORUM_VERSION ?>" class="btn btn-primary"><?php echo $lang['Changelog'] ?></a>
                <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-primary"><?php echo sprintf($lang['Download'], FORUM_VERSION) ?></a>
            </div>
<?php	}
    }
?>
</div>
<h2><?php echo $lang['What new'] ?> <?php echo FORUM_VERSION ?></h2>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Maintenance releases'] ?></h3>
        </div>
        <div class="panel-body">
            <p>
                <?php echo sprintf($lang['Version release'], '2.1.1') ?> <?php echo sprintf($lang['Maintenance version'], '8') ?>
            </p>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Headline feature 1 title'] ?></h3>
		</div>
        <div class="panel-body">
            <div class="thumbnail">
                <img src="../img/backstage/headline1.png" width="997" height="215" />
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <p><b><?php echo $lang['Headline feature 1 head 1'] ?></b><br />
                    <?php echo $lang['Headline feature 1 info 1'] ?></p>
                </div>
                <div class="col-lg-6">
                    <p><b><?php echo $lang['Headline feature 1 head 2'] ?></b><br />
                    <?php echo $lang['Headline feature 1 info 2'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Headline feature 2 title'] ?></h3>
        </div>
        <div class="panel-body">
            <p><?php echo $lang['Headline feature 2 info'] ?></p>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Other new'] ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-4">
                    <p><b><?php echo $lang['Minor feature 1 head'] ?></b>
                    <br /><?php echo $lang['Minor feature 1 info'] ?></p>
                </div>
                <div class="col-lg-4">
                    <p><b><?php echo $lang['Minor feature 2 head'] ?></b>
                    <br /><?php echo $lang['Minor feature 2 info'] ?></p>
                </div>
                <div class="col-lg-4">
                    <p><b><?php echo $lang['Minor feature 3 head'] ?></b>
                    <br /><?php echo $lang['Minor feature 3 info'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
