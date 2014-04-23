<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

// Collect some statistics from the database
if (file_exists(FORUM_CACHE_DIR.'cache_users_info.php'))
	include FORUM_CACHE_DIR.'cache_users_info.php';

if (!defined('FORUM_USERS_INFO_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();
	require FORUM_CACHE_DIR.'cache_users_info.php';
}

if (file_exists(FORUM_CACHE_DIR.'cache_update.php'))
	include FORUM_CACHE_DIR.'cache_update.php';

if ((!defined('FORUM_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24))))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_update_cache();
	require FORUM_CACHE_DIR.'cache_update.php';
}

$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = array_map('intval', $db->fetch_row($result));

if ($stats['total_posts'] == 0) {
	$stats['total_posts'] == '0';
}

if ($stats['total_topics'] == 0) {
	$stats['total_topics'] == '0';
}

$latest_version = $update_cache;

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Index']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('index');
?>

<h2><?php echo $lang['Backstage'] ?><span class="pull-right"><a href="update.php" class="btn btn-default btn-update"><?php echo $lang['Updates'] ?></a></span></h2>
<div class="row">
<?php
//Update checking
	if (version_compare(Version::FORUM_VERSION, $latest_version, '<')) { ?>
<div class="alert alert-info">
    <h4><?php echo sprintf($lang['Available'], $latest_version) ?></h4>
</div>
<?php
    }
if ($luna_user['g_id'] == FORUM_ADMIN) {
?>
    <div class="col-lg-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Backup head'] ?></h3>
            </div>
            <div class="panel-body">
                <a class="btn btn-block btn-primary" href="database.php"><?php echo $lang['Backup button'] ?></a>
            </div>
         </div>
    </div> 
    <div class="col-lg-9">
<?php } else { ?>
    <div class="col-lg-12">
<?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['New reports head'] ?><span class="pull-right"><a class="btn btn-primary" href="reports.php"><?php echo $lang['View all'] ?></a></span></h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th><?php echo $lang['Reported by'] ?></th>
                        <th><?php echo $lang['Date and time'] ?></th>
                        <th><?php echo $lang['Message'] ?></th>
                    </tr>
                </thead>
                <tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_report = $db->fetch_assoc($result))
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.luna_htmlspecialchars($cur_report['reporter']).'</a>' : $lang['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.luna_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.luna_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', luna_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span><a href="viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf($lang['Post ID'], $cur_report['pid']).'</a></span>' : '<span>'.$lang['Deleted'].'</span>';
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
                            <td colspan="4"><?php echo $lang['No new reports'] ?></td>
                        </tr>
<?php

}

?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['About head'] ?></h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-lg-6"><?php echo $lang['ModernBB version label'] ?></th>
                        <th class="col-lg-6"><?php echo $lang['Server statistics label'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php printf($lang['ModernBB version data'].$luna_config['o_cur_version']) ?></td>
                        <td><a href="statistics.php"><?php echo $lang['View server statistics'] ?></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $lang['Statistics head'] ?></h3>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_posts'])) ?></b><br /><?php echo $lang['posts'] ?></h4></td>
                        <td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_topics'])) ?></b><br /><?php echo $lang['topics'] ?></h4></td>
                        <td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_users'])) ?></b><br /><?php echo $lang['users'] ?></h4></td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
