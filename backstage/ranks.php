<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: ../login.php");
if ($luna_user['g_id'] != FORUM_ADMIN)
	message_backstage($lang['No permission'], false, '403 Forbidden');

// Add a rank
if (isset($_POST['add_rank'])) {
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
elseif (isset($_POST['update'])) {
	confirm_referrer('backstage/ranks.php');
	
	$rank = $_POST['rank'];
	if (empty($rank))
		message_backstage($lang['Bad request'], false, '404 Not Found');

	foreach ($rank as $item_id => $cur_rank) {
		$cur_rank['rank'] = luna_trim($cur_rank['rank']);
		$cur_rank['min_posts'] = luna_trim($cur_rank['min_posts']);

		if ($cur_rank['rank'] == '')
			message_backstage($lang['Must enter title message']);
		elseif ($cur_rank['min_posts'] == '' || preg_match('%[^0-9]%', $cur_rank['min_posts']))
			message_backstage($lang['Must be integer message']);
		else
			$rank_check = $db->query('SELECT 1 FROM '.$db->prefix.'ranks WHERE id!='.intval($item_id).' AND min_posts='.$cur_rank['min_posts']) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($rank_check) != 0)
				message_backstage(sprintf($lang['Dupe min posts message'], $cur_rank['min_posts']));

		$db->query('UPDATE '.$db->prefix.'ranks SET rank=\''.$db->escape($cur_rank['rank']).'\', min_posts=\''.$cur_rank['min_posts'].'\' WHERE id='.intval($item_id)) or error('Unable to update ranks', __FILE__, __LINE__, $db->error());
	}

	redirect('backstage/ranks.php');
}

// Remove a rank
elseif (isset($_POST['remove'])) {
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
require 'header.php';
	load_admin_nav('users', 'ranks');

if ($luna_config['o_ranks'] == 0) {
?>
<div class="alert alert-danger">
	<?php echo sprintf($lang['Ranks disabled'], '<a href="features.php">'.$lang['Features'].'</a>') ?>
</div>
<?php } ?>
<div class="row">
	<form id="ranks" method="post" action="ranks.php">
		<div class="col-sm-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $lang['Add rank subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="add_rank" tabindex="3"><span class="fa fa-plus"></span> <?php echo $lang['Add'] ?></button></span></h3>
				</div>
				<fieldset>
					<table class="table">
						<tbody>
							<tr>
								<td><input type="text" class="form-control" name="new_rank" placeholder="<?php echo $lang['Rank title label'] ?>" maxlength="50" tabindex="1" /></td>
							</tr>
							<tr>
								<td><input type="text" class="form-control" name="new_min_posts" placeholder="<?php echo $lang['Minimum posts label'] ?>" maxlength="7" tabindex="2" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
	</form>
	<form id="ranks" method="post" action="ranks.php">
		<div class="col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Manage ranks<span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Save'] ?>" /></span></h3>
				</div>
				<fieldset>
<?php

$result = $db->query('SELECT id, rank, min_posts FROM '.$db->prefix.'ranks ORDER BY min_posts') or error('Unable to fetch rank list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {

?>
					<table class="table">
						<thead>
							<tr>
								<th><?php echo $lang['Rank title label'] ?></th>
								<th class="col-lg-2"><?php echo $lang['Minimum posts label'] ?></th>
								<th><?php echo $lang['Actions'] ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	while ($cur_rank = $db->fetch_assoc($result)) {
?>
							<tr>
								<td>
									<input type="text" class="form-control" name="rank[<?php echo $cur_rank['id'] ?>][rank]" value="<?php echo luna_htmlspecialchars($cur_rank['rank']) ?>" maxlength="50" />
								</td>
								<td>
									<input type="text" class="form-control" name="rank[<?php echo $cur_rank['id'] ?>][min_posts]" value="<?php echo $cur_rank['min_posts'] ?>" maxlength="7" />
								</td>
								<td>
									<input class="btn btn-danger" type="submit" name="remove[<?php echo $cur_rank['id'] ?>]" value="<?php echo $lang['Remove'] ?>" />
								</td>
							</tr>
<?php
	}
} else
	echo '<tr><td colspan="3">'.$lang['No ranks in list'].'</td></tr>';
?>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
	</form>
</div>
<?php

require 'footer.php';
