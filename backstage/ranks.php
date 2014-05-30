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

if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

// Add a rank
if (isset($_POST['add_rank']))
{
	$rank = luna_trim($_POST['new_rank']);
	$min_posts = luna_trim($_POST['new_min_posts']);

	if ($rank == '')
		message_backstage($lang['Must enter title message']);

	if ($min_posts == '' || preg_match('%[^0-9]%', $min_posts))
		message_backstage($lang['Must be integer message']);

	// Make sure there isn't already a rank with the same min_posts value
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'ranks WHERE min_posts='.$min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
		message_backstage(sprintf($lang['Dupe min posts message'], $min_posts));

	$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_posts) VALUES(\''.$db->escape($rank).'\', '.$min_posts.')') or error('Unable to add rank', __FILE__, __LINE__, $db->error());

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	redirect('backstage/ranks.php');
}


// Update a rank
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$rank = luna_trim($_POST['rank'][$id]);
	$min_posts = luna_trim($_POST['min_posts'][$id]);

	if ($rank == '')
		message_backstage($lang['Must enter title message']);

	if ($min_posts == '' || preg_match('%[^0-9]%', $min_posts))
		message_backstage($lang['Must be integer message']);

	// Make sure there isn't already a rank with the same min_posts value
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'ranks WHERE id!='.$id.' AND min_posts='.$min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
		message_backstage(sprintf($lang['Dupe min posts message'], $min_posts));

	$db->query('UPDATE '.$db->prefix.'ranks SET rank=\''.$db->escape($rank).'\', min_posts='.$min_posts.' WHERE id='.$id) or error('Unable to update rank', __FILE__, __LINE__, $db->error());

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	redirect('backstage/ranks.php');
}


// Remove a rank
else if (isset($_POST['remove']))
{
	$id = intval(key($_POST['remove']));

	$db->query('DELETE FROM '.$db->prefix.'ranks WHERE id='.$id) or error('Unable to delete rank', __FILE__, __LINE__, $db->error());

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	redirect('backstage/ranks.php');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Ranks']);
$focus_element = array('ranks', 'new_rank');
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('ranks');

?>
<h2><?php echo $lang['Ranks'] ?></h2>
<?php if ($luna_config['o_ranks'] == 0) { ?>
<div class="alert alert-danger">
	<?php echo sprintf($lang['Ranks disabled'], '<a href="features.php">'.$lang['Features'].'</a>') ?>
</div>
<?php } ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Add rank subhead'] ?></h3>
    </div>
	<form id="ranks" method="post" action="ranks.php">
		<fieldset>
			<table class="table">
				<thead>
					<tr>
						<th class="col-lg-4"><?php echo $lang['Rank title label'] ?></th>
						<th class="col-lg-4"><?php echo $lang['Minimum posts label'] ?></th>
						<th class="col-lg-4"><?php echo $lang['Actions'] ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="text" class="form-control" name="new_rank" maxlength="50" tabindex="1" /></td>
						<td><input type="text" class="form-control" name="new_min_posts" maxlength="7" tabindex="2" /></td>
						<td><input class="btn btn-primary" type="submit" name="add_rank" value="<?php echo $lang['Add'] ?>" tabindex="3" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Edit remove subhead'] ?></h3>
    </div>
	<form id="ranks" method="post" action="ranks.php">
		<fieldset>
<?php

$result = $db->query('SELECT id, rank, min_posts FROM '.$db->prefix.'ranks ORDER BY min_posts') or error('Unable to fetch rank list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
{

?>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th class="col-lg-4"><?php echo $lang['Rank title label'] ?></th>
						<th class="col-lg-4"><?php echo $lang['Minimum posts label'] ?></th>
						<th class="col-lg-4"><?php echo $lang['Actions'] ?></th>
					</tr>
				</thead>
				<tbody>
<?php

	while ($cur_rank = $db->fetch_assoc($result))
		echo "\t\t\t\t\t\t\t\t".'<tr><td><input type="text" class="form-control" name="rank['.$cur_rank['id'].']" value="'.luna_htmlspecialchars($cur_rank['rank']).'" maxlength="50" /></td><td><input type="text" class="form-control" name="min_posts['.$cur_rank['id'].']" value="'.$cur_rank['min_posts'].'" maxlength="7" /></td><td><div class="btn-group"><input class="btn btn-primary" type="submit" name="update['.$cur_rank['id'].']" value="'.$lang['Update'].'" /><input class="btn btn-danger" type="submit" name="remove['.$cur_rank['id'].']" value="'.$lang['Remove'].'" /></div></td></tr>'."\n";

?>
				</tbody>
			</table>
<?php

}
else
	echo "\t\t\t\t\t\t\t".'<div class="panel-body"><p>'.$lang['No ranks in list'].'</p></div>'."\n";

?>
		</fieldset>
	</form>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
