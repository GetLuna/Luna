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

// Collect some statistics from the database
if (file_exists(FORUM_CACHE_DIR.'cache_users_info.php'))
	include FORUM_CACHE_DIR.'cache_users_info.php';

if (!defined('PUN_USERS_INFO_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();
	require FORUM_CACHE_DIR.'cache_users_info.php';
}

$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = $db->fetch_row($result);

if ($stats['total_topics'] == NULL) {
	$stats['total_topics'] == '0';
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
			<a class="btn btn-block btn-primary" href="database.php">Create new backup</a>
		</div>
		<div class="span9"><h6>New reports - <a href="reports.php">view all</a></h6>
            <table class="table" cellspacing="0">
                <thead>
                <tr>
                    <th><?php echo $lang_admin_index['Reported by'] ?></th>
                    <th><?php echo $lang_admin_index['Date and time'] ?></th>
                    <th><?php echo $lang_admin_index['Message'] ?></th>
                </tr>
                </thead>
                <tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_report = $db->fetch_assoc($result))
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.pun_htmlspecialchars($cur_report['reporter']).'</a>' : $lang_admin_reports['Deleted user'];
		$post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
		$report_location = array($forum, $topic, $post_id);

?>
                <tr>
                    <td><?php printf($reporter) ?></td>
                    <td><?php printf(format_time($cur_report['created'])) ?></td>
                    <td><?php echo $post ?></td>
                </tr>
<?php

	}
}
else
{

?>
                <tr>
                    <td colspan="4"><p><?php echo $lang_admin_index['No new reports'] ?></p></td>
                </tr>
<?php

}

?>
                </tbody>
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
                        <td style="text-align:center;"><h4><b><b><?php printf(forum_number_format($stats['total_posts'])) ?></b></b><br />posts</h4></td>
                        <td style="text-align:center;"><h4><b><b><?php printf(forum_number_format($stats['total_topics'])) ?></b></b><br />topics</h4></td>
                        <td style="text-align:center;"><h4><b><b><?php printf(forum_number_format($stats['total_users'])) ?></b></b><br />users</h4></td>
                    </tr>
                </thead>
            </table>
		</div>
    </div>
</div>
<?php

require FORUM_ROOT.'admin/footer.php';
