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


if (!$pun_user['is_admmod'])
	message($lang_common['No permission'], false, '403 Forbidden');

// Load the admin_reports.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/admin_reports.php';

// Zap a report
if (isset($_POST['zap_id']))
{
	confirm_referrer('reports.php');

	$zap_id = intval(key($_POST['zap_id']));

	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE id='.$zap_id) or error('Unable to fetch report info', __FILE__, __LINE__, $db->error());
	$zapped = $db->result($result);

	if ($zapped == '')
		$db->query('UPDATE '.$db->prefix.'reports SET zapped='.time().', zapped_by='.$pun_user['id'].' WHERE id='.$zap_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());

	// Delete old reports (which cannot be viewed anyway)
	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10,1') or error('Unable to fetch read reports to delete', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result) > 0)
	{
		$zapped_threshold = $db->result($result);
		$db->query('DELETE FROM '.$db->prefix.'reports WHERE zapped <= '.$zapped_threshold) or error('Unable to delete old read reports', __FILE__, __LINE__, $db->error());
	}

	redirect('reports.php', $lang_admin_reports['Report zapped redirect']);
}


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_admin_common['Admin'], $lang_admin_common['Reports']);
define('PUN_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'admin/header.php';
	generate_admin_menu('');

?>
<div class="content">
    <h2><?php echo $lang_admin_reports['New reports head'] ?></h2>
    <form method="post" action="reports.php?action=zap">
        <fieldset>
            <table class="table" cellspacing="0">
                <thead>
                <tr>
                    <th><?php echo $lang_admin_reports['Reported by'] ?></th>
                    <th><?php echo $lang_admin_reports['Date and time'] ?></th>
                    <th><?php echo $lang_admin_reports['Message'] ?></th>
                    <th><?php echo $lang_admin_reports['Actions'] ?></th>
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
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.pun_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang_admin_reports['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.pun_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang_admin_reports['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf($lang_admin_reports['Post ID'], $cur_report['pid']).'</a></span>' : '<span> <span class="divider">/</span> '.$lang_admin_reports['Deleted'].'</span>';
		$report_location = array($forum, $topic, $post_id);

?>
                <tr>
                    <td><?php printf($reporter) ?></td>
                    <td><?php printf(format_time($cur_report['created'])) ?></td>
                    <td>
                        <div class="breadcrumb"><?php echo implode(' ', $report_location) ?></div>
                        <?php echo $post ?>
                    </td>
                    <td><input class="btn btn-success" type="submit" name="zap_id[<?php echo $cur_report['id'] ?>]" value="<?php echo $lang_admin_reports['Zap'] ?>" /></td>
                </tr>
<?php

	}
}
else
{

?>
        <tr>
        	<td colspan="4"><p><?php echo $lang_admin_reports['No new reports'] ?></p></td>
        </tr>
<?php

}

?>
                </tbody>
            </table>
        </fieldset>
    </form>
</div>
<div class="content">
    <h2><span><?php echo $lang_admin_reports['Last 10 head'] ?></span></h2>
    <table class="table" cellspacing="0">
        <thead>
        <tr>
            <th><?php echo $lang_admin_reports['Reported by'] ?></th>
            <th><?php echo $lang_admin_reports['Readed by'] ?></th>
            <th><?php echo $lang_admin_reports['Date and time'] ?></th>
            <th><?php echo $lang_admin_reports['Message'] ?></th>
        </tr>
        </thead>
        <tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id LEFT JOIN '.$db->prefix.'users AS u2 ON r.zapped_by=u2.id WHERE r.zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result))
{
	while ($cur_report = $db->fetch_assoc($result))
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.pun_htmlspecialchars($cur_report['reporter']).'</a>' : $lang_admin_reports['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.pun_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang_admin_reports['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.pun_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang_admin_reports['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf($lang_admin_reports['Post ID'], $cur_report['pid']).'</a></span>' : '<span> <span class="divider">/</span> '.$lang_admin_reports['Deleted'].'</span>';
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<a href="../profile.php?id='.$cur_report['zapped_by_id'].'">'.pun_htmlspecialchars($cur_report['zapped_by']).'</a>' : $lang_admin_reports['NA'];
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<strong>'.pun_htmlspecialchars($cur_report['zapped_by']).'</strong>' : $lang_admin_reports['NA'];
		$report_location = array($forum, $topic, $post_id);

?>
        <fieldset>
            <h3><?php printf($lang_admin_reports['Zapped subhead'], format_time($cur_report['zapped']), $zapped_by) ?></h3>
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
                    <td colspan="4"><?php echo $lang_admin_reports['No zapped reports'] ?></td>
                </tr>
<?php

} ?></fieldset></tbody></table>
</div>
<?php
require FORUM_ROOT.'admin/footer.php';
