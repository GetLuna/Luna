<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: login.php");
}

// Load the admin_index.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_about.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['About']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
	generate_admin_menu('about');

//Update checking
$latest_version = trim(@file_get_contents('https://raw.github.com/ModernBB/ModernBB/master/version.txt'));
if (preg_match("/^[0-9.-]{1,}$/", $latest_version)) {
	if (FORUM_VERSION < $latest_version) { ?>
		<div class="alert alert-info alert-update">
          <h4><?php echo sprintf($lang_admin_common['Available'], $latest_version) ?></h4>
          <?php echo $lang_admin_common['Update info'] ?><br />
          <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang_admin_common['Download'], $latest_version) ?></a>
          <a href="http://modernbb.be/changelog.php#modernbb<?php echo $latest_version ?>" class="btn btn-primary"><?php echo $lang_admin_common['Changelog'] ?></a>
          <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn"><?php echo sprintf($lang_admin_common['Download'], FORUM_VERSION) ?></a>
        </div>
    <?php }
}
?>
<div class="alert alert-update alert-info">
    <h2><?php echo $lang_about['ModernBB intro'] ?> <?php echo FORUM_VERSION ?></h2>
    <a href="http://modernbb.be/changelog.php#modernbb<?php echo FORUM_VERSION ?>" class="btn btn-primary"><?php echo $lang_admin_common['Changelog'] ?></a>
	<a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn btn-primary"><?php echo sprintf($lang_admin_common['Download'], FORUM_VERSION) ?></a>
</div>
<div class="content">
    <h2><?php echo $lang_about['What new'] ?> <?php echo FORUM_VERSION ?></h2>
    <h3><?php echo $lang_about['Headline feature 1 title'] ?></h3>
    <img src="admin/img/dashboard.png" width="1065" height="250" />
	<div class="row-fluid">
      <div class="span6"><p><b><?php echo $lang_about['Headline feature 1 head 1'] ?></b><br />
      <?php echo $lang_about['Headline feature 1 info 1'] ?></p></div>
      <div class="span6"><p><b><?php echo $lang_about['Headline feature 1 head 2'] ?></b><br />
      <?php echo $lang_about['Headline feature 1 info 2'] ?></p></div>
      <p><b><?php echo $lang_about['Headline feature 1 head 3'] ?></b><br />
      <?php echo $lang_about['Headline feature 1 info 3'] ?></p>
	</div>
    <h3><?php echo $lang_about['Headline feature 2 title'] ?></h3>
    <img src="admin/img/update.png" width="981" height="89" />
	<p><?php echo $lang_about['Headline feature 2 info'] ?></p>
    <h3><?php echo $lang_about['More new'] ?></h3>
    <div class="row-fluid">
    	<div class="span4">
        	<p><b><?php echo $lang_about['Second feature 1 head'] ?></b></p>
        	<img src="admin/img/login.png" width="366" height="318" />
            <p><?php echo $lang_about['Second feature 1 info'] ?></p>
        </div>
    	<div class="span4">
        	<p><b><?php echo $lang_about['Second feature 2 head'] ?></b></p>
        	<img src="admin/img/styles.png" width="366" height="318" />
            <p><?php echo $lang_about['Second feature 2 info'] ?></p>
        </div>
    	<div class="span4">
        	<p><b><?php echo $lang_about['Second feature 3 head'] ?></b></p>
        	<img src="admin/img/database.png" width="366" height="318" />
            <p><?php echo $lang_about['Second feature 3 info'] ?></p>
        </div>
    </div>
	<div class="row-fluid">
      <div class="span4">
          <p><b><?php echo $lang_about['Second feature 4 head'] ?>.</b>
          <br /><?php echo $lang_about['Second feature 4 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Second feature 5 head'] ?></b>
          <br /><?php echo $lang_about['Second feature 5 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Second feature 6 head'] ?></b>
          <br /><?php echo $lang_about['Second feature 6 info'] ?></p>
      </div>
	</div>
    <h3>Other small improvements</h3>
	<div class="row-fluid">
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 1 head'] ?>.</b>
          <br /><?php echo $lang_about['Minor feature 1 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 2 head'] ?></b>
          <br /><?php echo $lang_about['Minor feature 2 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 3 head'] ?></b>
          <br /><?php echo $lang_about['Minor feature 3 info'] ?></p>
      </div>
	</div>
	<div class="row-fluid">
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 4 head'] ?>.</b>
          <br /><?php echo $lang_about['Minor feature 4 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 5 head'] ?></b>
          <br /><?php echo $lang_about['Minor feature 5 info'] ?></p>
      </div>
      <div class="span4">
          <p><b><?php echo $lang_about['Minor feature 6 head'] ?></b>
          <br /><?php echo $lang_about['Minor feature 6 info'] ?></p>
      </div>
	</div>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
