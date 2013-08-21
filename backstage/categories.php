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

// Add a new category
if (isset($_POST['add_cat']))
{
	$new_cat_name = pun_trim($_POST['new_cat_name']);
	if ($new_cat_name == '')
		message($lang_back['Must enter name message']);

	$db->query('INSERT INTO '.$db->prefix.'categories (cat_name) VALUES(\''.$db->escape($new_cat_name).'\')') or error('Unable to create category', __FILE__, __LINE__, $db->error());

	redirect('backstage/categories.php', $lang_back['Category added redirect']);
}

// Delete a category
else if (isset($_POST['del_cat']) || isset($_POST['del_cat_comply']))
{
	$cat_to_delete = intval($_POST['cat_to_delete']);
	if ($cat_to_delete < 1)
		message($lang_common['Bad request']);

	if (isset($_POST['del_cat_comply'])) // Delete a category with all forums and posts
	{
		@set_time_limit(0);

		$result = $db->query('SELECT id FROM '.$db->prefix.'forums WHERE cat_id='.$cat_to_delete) or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
		$num_forums = $db->num_rows($result);

		for ($i = 0; $i < $num_forums; ++$i)
		{
			$cur_forum = $db->result($result, $i);

			// Prune all posts and topics
			prune($cur_forum, 1, -1);

			// Delete the forum
			$db->query('DELETE FROM '.$db->prefix.'forums WHERE id='.$cur_forum) or error('Unable to delete forum', __FILE__, __LINE__, $db->error());
		}

		// Locate any "orphaned redirect topics" and delete them
		$result = $db->query('SELECT t1.id FROM '.$db->prefix.'topics AS t1 LEFT JOIN '.$db->prefix.'topics AS t2 ON t1.moved_to=t2.id WHERE t2.id IS NULL AND t1.moved_to IS NOT NULL') or error('Unable to fetch redirect topics', __FILE__, __LINE__, $db->error());
		$num_orphans = $db->num_rows($result);

		if ($num_orphans)
		{
			for ($i = 0; $i < $num_orphans; ++$i)
				$orphans[] = $db->result($result, $i);

			$db->query('DELETE FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $orphans).')') or error('Unable to delete redirect topics', __FILE__, __LINE__, $db->error());
		}

		// Delete the category
		$db->query('DELETE FROM '.$db->prefix.'categories WHERE id='.$cat_to_delete) or error('Unable to delete category', __FILE__, __LINE__, $db->error());

		// Regenerate the quick jump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		redirect('backstage/categories.php', $lang_back['Category deleted redirect']);
	}
	else // If the user hasn't confirmed the delete
	{
		$result = $db->query('SELECT cat_name FROM '.$db->prefix.'categories WHERE id='.$cat_to_delete) or error('Unable to fetch category info', __FILE__, __LINE__, $db->error());
		$cat_name = $db->result($result);

		$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Categories']);
		define('FORUM_ACTIVE_PAGE', 'admin');
		require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('categories');

?>
<div class="content">
    <form method="post" action="categories.php">
        <input type="hidden" name="cat_to_delete" value="<?php echo $cat_to_delete ?>" />
        <fieldset>
            <h2><?php echo $lang_back['Confirm delete subhead'] ?></h2>
            <p><?php printf($lang_back['Confirm delete info'], pun_htmlspecialchars($cat_name)) ?></p>
            <p class="warntext"><?php echo $lang_back['Delete category warn'] ?></p>
        </fieldset>
        <p class="control-group">
        	<input class="btn btn-danger" type="submit" name="del_cat_comply" value="<?php echo $lang_back['Delete'] ?>" />
            <a class="btn" href="javascript:history.go(-1)"><?php echo $lang_back['Go back'] ?></a>
        </p>
    </form>
</div>
<?php

		require FORUM_ROOT.'backstage/footer.php';
	}
}

else if (isset($_POST['update'])) // Change position and name of the categories
{
	$categories = $_POST['cat'];
	if (empty($categories))
		message($lang_common['Bad request']);

	foreach ($categories as $cat_id => $cur_cat)
	{
		$cur_cat['name'] = pun_trim($cur_cat['name']);
		$cur_cat['order'] = pun_trim($cur_cat['order']);

		if ($cur_cat['name'] == '')
			message($lang_back['Must enter name message']);

		if ($cur_cat['order'] == '' || preg_match('%[^0-9]%', $cur_cat['order']))
			message($lang_back['Must enter integer message']);

		$db->query('UPDATE '.$db->prefix.'categories SET cat_name=\''.$db->escape($cur_cat['name']).'\', disp_position='.$cur_cat['order'].' WHERE id='.intval($cat_id)) or error('Unable to update category', __FILE__, __LINE__, $db->error());
	}

	// Regenerate the quick jump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_quickjump_cache();

	redirect('backstage/categories.php', $lang_back['Categories updated redirect']);
}

// Generate an array with all categories
$result = $db->query('SELECT id, cat_name, disp_position FROM '.$db->prefix.'categories ORDER BY disp_position') or error('Unable to fetch category list', __FILE__, __LINE__, $db->error());
$num_cats = $db->num_rows($result);

for ($i = 0; $i < $num_cats; ++$i)
	$cat_list[] = $db->fetch_assoc($result);

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Categories']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('categories');

?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Add categories head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="categories.php">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2"><?php echo $lang_back['Add category label'] ?></th>
                        <td>
                            <input type="text" class="form-control"name="new_cat_name" size="35" maxlength="80" placeholder="Category name" tabindex="1" />
                            <input class="btn btn-primary" type="submit" name="add_cat" value="<?php echo $lang_back['Add new submit'] ?>" tabindex="2" />
                            <span class="help-block"><?php printf($lang_back['Add category help'], '<a href="forums.php">'.$lang_back['Forums'].'</a>') ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<?php if ($num_cats): ?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Delete categories head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="categories.php">
            <fieldset>
                <table class="table">
                    <tr>
                        <th class="col-md-2">
                            <?php echo $lang_back['Delete category label'] ?>
                        </th>
                        <td>
                            <select class="form-control" name="cat_to_delete" tabindex="3">
<?php

foreach ($cat_list as $cur_cat)
echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_cat['id'].'">'.pun_htmlspecialchars($cur_cat['cat_name']).'</option>'."\n";

?>
                            </select>
                            <input class="btn btn-danger" type="submit" name="del_cat" value="<?php echo $lang_back['Delete'] ?>" tabindex="4" />
                            <span class="help-block"><?php echo $lang_back['Delete category help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<?php endif; ?>
<?php if ($num_cats): ?>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Edit categories head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="categories.php">
            <fieldset>
                <table class="table" >
                    <thead>
                        <tr>
                            <th class="tcl" scope="col"><?php echo $lang_back['Category name label'] ?></th>
                            <th scope="col"><?php echo $lang_back['Category position label'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php

foreach ($cat_list as $cur_cat)
{

?>
                        <tr>
                            <td class="tcl"><input type="text" class="form-control"name="cat[<?php echo $cur_cat['id'] ?>][name]" value="<?php echo pun_htmlspecialchars($cur_cat['cat_name']) ?>" size="35" maxlength="80" /></td>
                            <td><input type="text" class="form-control"name="cat[<?php echo $cur_cat['id'] ?>][order]" value="<?php echo $cur_cat['disp_position'] ?>" size="3" maxlength="3" /></td>
                        </tr>
<?php

}

?>
					</tbody>
				</table>
				<div class="control-group">
					<input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang_back['Update'] ?>" />
				</div>
			</fieldset>
		</form>
    </div>
</div>
<?php endif; 

require FORUM_ROOT.'backstage/footer.php';
