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
    header("Location: login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

// Zap a report
if (isset($_POST['zap_id']))
{
	$zap_id = intval(key($_POST['zap_id']));

	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE id='.$zap_id) or error('Unable to fetch report info', __FILE__, __LINE__, $db->error());
	$zapped = $db->result($result);

	if ($zapped == '')
		$db->query('UPDATE '.$db->prefix.'reports SET zapped='.time().', zapped_by='.$pun_user['id'].' WHERE id='.$zap_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'posts SET marked = 0 WHERE id='.$zap_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());

	// Delete old reports (which cannot be viewed anyway)
	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10,1') or error('Unable to fetch read reports to delete', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result) > 0)
	{
		$zapped_threshold = $db->result($result);
		$db->query('DELETE FROM '.$db->prefix.'reports WHERE zapped <= '.$zapped_threshold) or error('Unable to delete old read reports', __FILE__, __LINE__, $db->error());
	}

	redirect('backstage/reports.php', $lang_back['Report zapped redirect']);
}


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Reports']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('reports');

?>
<h2><?php echo $lang_back['New reports head'] ?></h2>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['New reports head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="reports.php?action=zap">
            <fieldset>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $lang_back['Reported by'] ?></th>
                            <th><?php echo $lang_back['Date and time'] ?></th>
                            <th><?php echo $lang_back['Message'] ?></th>
                            <th><?php echo $lang_back['Actions'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_report = $db->fetch_assoc($result))
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.pun_htmlspecialchars($cur_report['reporter']).'</a>' : $lang_back['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.pun_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang_back['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.pun_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang_back['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
		$report_location = array($forum, $topic, $post_id);

?>
                    <tr>
                        <td><?php printf($reporter) ?></td>
                        <td><?php printf(format_time($cur_report['created'])) ?></td>
                        <td>
                            <div class="breadcrumb"><?php echo implode(' ', $report_location) ?></div>
                            <?php echo $post ?>
                        </td>
                        <td><input class="btn btn-primary" type="submit" name="zap_id[<?php echo $cur_report['id'] ?>]" value="<?php echo $lang_back['Zap'] ?>" /></td>
                    </tr>
<?php

	}
}
else
{

?>
                    <tr>
                        <td colspan="4"><p><?php echo $lang_back['No new reports'] ?></p></td>
                    </tr>
<?php

}

?>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Last 10 head'] ?></h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
            <tr>
                <th><?php echo $lang_back['Reported by'] ?></th>
                <th><?php echo $lang_back['Readed by'] ?></th>
                <th><?php echo $lang_back['Date and time'] ?></th>
                <th><?php echo $lang_back['Message'] ?></th>
            </tr>
            </thead>
            <tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id LEFT JOIN '.$db->prefix.'users AS u2 ON r.zapped_by=u2.id WHERE r.zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_report = $db->fetch_assoc($result))
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.pun_htmlspecialchars($cur_report['reporter']).'</a>' : $lang_back['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.pun_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang_back['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.pun_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang_back['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf($lang_back['Post ID'], $cur_report['pid']).'</a></span>' : '<span> <span class="divider">/</span> '.$lang_back['Deleted'].'</span>';
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<a href="../profile.php?id='.$cur_report['zapped_by_id'].'">'.pun_htmlspecialchars($cur_report['zapped_by']).'</a>' : $lang_back['NA'];
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<strong>'.pun_htmlspecialchars($cur_report['zapped_by']).'</strong>' : $lang_back['NA'];
		$report_location = array($forum, $topic, $post_id);

?>
            <fieldset>
                <h3><?php printf($lang_back['Zapped subhead'], format_time($cur_report['zapped']), $zapped_by) ?></h3>
                    <tr>
                        <td><?php printf($reporter) ?></td>
                        <td><?php printf($zapped_by) ?></td>
                        <td><?php printf(format_time($cur_report['zapped'])) ?></td>
                        <td>
                            <div class="breadcrumb"><?php echo implode(' ', $report_location) ?></div>
                            <?php echo $post ?>
                        </td>
                    </tr>
<?php

	}
}
else
{

?>
                    <tr>
                        <td colspan="4"><?php echo $lang_back['No zapped reports'] ?></td>
                    </tr>
<?php

} ?>
                </fieldset>
            </tbody>
        </table>
    </div>
</div>
<?php
require FORUM_ROOT.'backstage/footer.php';
