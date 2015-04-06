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
	header("Location: login.php");

// Add a censor word
if (isset($_POST['add_word'])) {
	confirm_referrer('backstage/censoring.php');
	
	$search_for = luna_trim($_POST['new_search_for']);
	$replace_with = luna_trim($_POST['new_replace_with']);

	if ($search_for == '') {
		message_backstage($lang['Must enter word message']);
		exit;
	}

	$db->query('INSERT INTO '.$db->prefix.'censoring (search_for, replace_with) VALUES (\''.$db->escape($search_for).'\', \''.$db->escape($replace_with).'\')') or error('Unable to add censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php');
}

// Update a censor word
elseif (isset($_POST['update'])) {
	confirm_referrer('backstage/censoring.php');
	
	$id = intval(key($_POST['update']));

	$search_for = luna_trim($_POST['search_for'][$id]);
	$replace_with = luna_trim($_POST['replace_with'][$id]);

	if ($search_for == '')
		message_backstage($lang['Must enter word message']);

	$db->query('UPDATE '.$db->prefix.'censoring SET search_for=\''.$db->escape($search_for).'\', replace_with=\''.$db->escape($replace_with).'\' WHERE id='.$id) or error('Unable to update censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php');
}

// Remove a censor word
elseif (isset($_POST['remove'])) {
	confirm_referrer('backstage/censoring.php');
	
	$id = intval(key($_POST['remove']));

	$db->query('DELETE FROM '.$db->prefix.'censoring WHERE id='.$id) or error('Unable to delete censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php');
}

$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Censoring']);
$focus_element = array('censoring', 'new_search_for');
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('content', 'censoring');

?>
<div class="row">
	<div class="col-sm-4">
		<form id="censoring" method="post" action="censoring.php">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $lang['Add word subhead'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="add_word" tabindex="3"><span class="fa fa-fw fa-plus"></span> <?php echo $lang['Add'] ?></button></span></h3>
				</div>
					<fieldset>
					<div class="panel-body">
						<p><?php echo $lang['Add word info'] ?></p>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td><input type="text" class="form-control" placeholder="<?php echo $lang['Censored word label'] ?>" name="new_search_for" maxlength="60" tabindex="1" /></td>
							</tr>
							<tr>
								<td><input type="text" class="form-control" placeholder="<?php echo $lang['Replacement label'] ?>" name="new_replace_with" maxlength="60" tabindex="2" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>
	</div>
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $lang['Edit remove words'] ?></h3>
			</div>
			<form id="censoring" method="post" action="censoring.php">
				<fieldset>
					<table class="table table-striped">
						<thead>
							<tr>
								<th class="col-xs-4"><?php echo $lang['Censored word label'] ?></th>
								<th class="col-xs-4"><?php echo $lang['Replacement label'] ?></th>
								<th class="col-xs-4"><?php echo $lang['Action'] ?></th>
							</tr>
						</thead>
						<tbody>
<?php

$result = $db->query('SELECT id, search_for, replace_with FROM '.$db->prefix.'censoring ORDER BY id') or error('Unable to fetch censor word list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {

	while ($cur_word = $db->fetch_assoc($result)) {
?>
							<tr>
								<td>
									<input type="text" class="form-control" name="search_for[<?php echo $cur_word['id'] ?>]" value="<?php echo luna_htmlspecialchars($cur_word['search_for']) ?>" maxlength="60" />
								</td>
								<td>
									<input type="text" class="form-control" name="replace_with[<?php echo $cur_word['id'] ?>]" value="<?php echo luna_htmlspecialchars($cur_word['replace_with']) ?>" maxlength="60" />
								</td>
								<td>
									<div class="btn-group">
										<button class="btn btn-primary" type="submit" name="update[<?php echo $cur_word['id'] ?>]"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Update'] ?></button>
										<button class="btn btn-danger" type="submit" name="remove[<?php echo $cur_word['id'] ?>]"><span class="fa fa-fw fa-trash"></span> <?php echo $lang['Remove'] ?></button>
									</div>
								</td>
								</tr>
<?php
	}
} else
	echo "\t\t\t\t\t\t\t".'<tr><td colspan="3">'.$lang['No words in list'].'</td></tr>'."\n";

?>
						</tbody>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<?php

require 'footer.php';
