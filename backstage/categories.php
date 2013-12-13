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

if ($pun_user['g_id'] != FORUM_ADMIN)
	message($lang['No permission'], false, '403 Forbidden');

if (isset($_POST['update'])) // Change position and name of the categories
{
	$categories = $_POST['cat'];
	if (empty($categories))
		message($lang['Bad request'], false, '404 Not Found');

	foreach ($categories as $cat_id => $cur_cat)
	{
		$cur_cat['name'] = pun_trim($cur_cat['name']);
		$cur_cat['order'] = pun_trim($cur_cat['order']);

		if ($cur_cat['name'] == '')
			message($lang['Must enter name message']);

		if ($cur_cat['order'] == '' || preg_match('%[^0-9]%', $cur_cat['order']))
			message($lang['Must enter integer message']);

		$db->query('UPDATE '.$db->prefix.'categories SET cat_name=\''.$db->escape($cur_cat['name']).'\', disp_position='.$cur_cat['order'].' WHERE id='.intval($cat_id)) or error('Unable to update category', __FILE__, __LINE__, $db->error());
	}

	// Regenerate the quick jump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	redirect('backstage/categories.php', $lang['Categories updated redirect']);
}

// Generate an array with all categories
$result = $db->query('SELECT id, cat_name, disp_position FROM '.$db->prefix.'categories ORDER BY disp_position') or error('Unable to fetch category list', __FILE__, __LINE__, $db->error());
$num_cats = $db->num_rows($result);

for ($i = 0; $i < $num_cats; ++$i)
	$cat_list[] = $db->fetch_assoc($result);

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Categories']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('categories');

?>
<h2><?php echo $lang['Categories'] ?></h2>
<?php if ($num_cats): ?>
<form method="post" action="categories.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Edit categories head'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Update positions'] ?>" /></span></h3>
		</div>
		<fieldset>
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $lang['Category name label'] ?></th>
						<th><?php echo $lang['Category position label'] ?></th>
					</tr>
				</thead>
				<tbody>
<?php

foreach ($cat_list as $cur_cat)
{

?>
					<tr>
						<td><input type="text" class="form-control" name="cat[<?php echo $cur_cat['id'] ?>][name]" value="<?php echo pun_htmlspecialchars($cur_cat['cat_name']) ?>" size="35" maxlength="80" /></td>
						<td><input type="text" class="form-control" name="cat[<?php echo $cur_cat['id'] ?>][order]" value="<?php echo $cur_cat['disp_position'] ?>" size="3" maxlength="3" /></td>
					</tr>
<?php

}

?>
				</tbody>
			</table>
		</fieldset>
	</div>
</form>
<?php endif; 

require FORUM_ROOT.'backstage/footer.php';
