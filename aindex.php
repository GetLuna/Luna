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
          <a href="http://modernbb.be/downloads/<?php echo $latest_version ?>.zip" class="btn btn-primary"><?php echo sprintf($lang_admin_common['Download'], $latest_version) ?></a>
          <a href="http://modernbb.be/changelog.php#modernbb<?php echo $latest_version ?>" class="btn btn-primary"><?php echo $lang_admin_common['Changelog'] ?></a>
          <a href="http://modernbb.be/downloads/<?php echo FORUM_VERSION ?>.zip" class="btn"><?php echo sprintf($lang_admin_common['Download'], FORUM_VERSION) ?></a>
        </div>
    <?php }
}
?>
<div class="content">
	<h2>Welcome to Backstage</h2>
	<p>Welcome to the ModernBB dashboard: Backstage. This is where you control your forums while thinking "yay".</p>
    <div class="row-fluid">
		<div class="span3">
			<h6>Back-up</h6>
			<p>Create a new database backup.</p>
			<a class="btn btn-primary" href="#">Download</a>
		</div>
		<div class="span9"><h6>New reports - <a href="reports.php">view all</a></h6>
			<table class="table">
            	<thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>By</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>04.08.2013</td>
                        <td>Person</td>
                        <td>This is spam! Do something...</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>02.08.2013</td>
                        <td>Fisher</td>
                        <td>Reclame about stuff.</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>01.08.2013</td>
                        <td>Baz</td>
                        <td>'Cuz I'm not the spammer. Get I kudo's?</td>
                    </tr>
                <tbody>
            </table>
        </div>
	</div>
    <div class="row-fluid">
		<div class="span8">
			<h6><?php echo $lang_admin_index['About head'] ?></h6>
            <table class="table">
            	<thead>
                    <tr>
                        <th class="span3"><?php echo $lang_admin_index['ModernBB version label'] ?></th>
                        <td><?php printf($lang_admin_index['ModernBB version data'].'<a href="about.php">'.$pun_config['o_cur_version'].'</a>') ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_admin_index['Server statistics label'] ?></th>
                        <td><a href="statistics.php"><?php echo $lang_admin_index['View server statistics'] ?></a></td>
                    </tr>
                </thead>
            </table>
		</div>
		<div class="span4">
			<h6>Statistics</h6>
            <table class="table">
            	<thead>
                    <tr>
                        <td style="text-align:center;"><h4><b><b>213.362</b></b><br />posts</h4></td>
                        <td style="text-align:center;"><h4><b><b>32.134</b></b><br />topics</h4></td>
                        <td style="text-align:center;"><h4><b><b>14.287</b></b><br />users</h4></td>
                    </tr>
                </thead>
            </table>
		</div>
    </div>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
