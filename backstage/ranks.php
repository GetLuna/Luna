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

// Add a rank
if (isset($_POST['add_rank']))
{
	$rank = pun_trim($_POST['new_rank']);
	$min_posts = pun_trim($_POST['new_min_posts']);

	if ($rank == '')
		message($lang_back['Must enter title message']);

	if ($min_posts == '' || preg_match('%[^0-9]%', $min_posts))
		message($lang_back['Must be integer message']);

	// Make sure there isn't already a rank with the same min_posts value
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'ranks WHERE min_posts='.$min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
		message(sprintf($lang_back['Dupe min posts message'], $min_posts));

	$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_posts) VALUES(\''.$db->escape($rank).'\', '.$min_posts.')') or error('Unable to add rank', __FILE__, __LINE__, $db->error());

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	redirect('backstage/ranks.php', $lang_back['Rank added redirect']);
}


// Update a rank
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$rank = pun_trim($_POST['rank'][$id]);
	$min_posts = pun_trim($_POST['min_posts'][$id]);

	if ($rank == '')
		message($lang_back['Must enter title message']);

	if ($min_posts == '' || preg_match('%[^0-9]%', $min_posts))
		message($lang_back['Must be integer message']);

	// Make sure there isn't already a rank with the same min_posts value
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'ranks WHERE id!='.$id.' AND min_posts='.$min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result))
		message(sprintf($lang_back['Dupe min posts message'], $min_posts));

	$db->query('UPDATE '.$db->prefix.'ranks SET rank=\''.$db->escape($rank).'\', min_posts='.$min_posts.' WHERE id='.$id) or error('Unable to update rank', __FILE__, __LINE__, $db->error());

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	redirect('backstage/ranks.php', $lang_back['Rank updated redirect']);
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

	redirect('backstage/ranks.php', $lang_back['Rank removed redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Ranks']);
$focus_element = array('ranks', 'new_rank');
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('ranks');

?>
<h2><?php echo $lang_back['Ranks head'] ?></h2>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Add rank subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="ranks" method="post" action="ranks.php">
            <fieldset>
                <p><?php echo $lang_back['Add rank info'].' '.($pun_config['o_ranks'] == '1' ? sprintf($lang_back['Ranks enabled'], '<a href="options.php#ranks">'.$lang_back['Options'].'</a>') : sprintf($lang_back['Ranks disabled'], '<a href="options.php#ranks">'.$lang_back['Options'].'</a>')) ?></p>
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="tcl" scope="col"><?php echo $lang_back['Rank title label'] ?></th>
                            <th class="tc2" scope="col"><?php echo $lang_back['Minimum posts label'] ?></th>
                            <th class="hidehead" scope="col"><?php echo $lang_back['Actions label'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tcl"><input type="text" class="form-control"name="new_rank" size="24" maxlength="50" tabindex="1" /></td>
                            <td class="tc2"><input type="text" class="form-control"name="new_min_posts" size="7" maxlength="7" tabindex="2" /></td>
                            <td><input class="btn btn-primary" type="submit" name="add_rank" value="<?php echo $lang_back['Add'] ?>" tabindex="3" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Edit remove subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="ranks" method="post" action="ranks.php">
            <fieldset>
<?php

$result = $db->query('SELECT id, rank, min_posts FROM '.$db->prefix.'ranks ORDER BY min_posts') or error('Unable to fetch rank list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
{

?>
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="tcl" scope="col"><?php echo $lang_back['Rank title label'] ?></th>
                            <th class="tc2" scope="col"><?php echo $lang_back['Minimum posts label'] ?></th>
                            <th class="hidehead" scope="col"><?php echo $lang_back['Actions label'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php

	while ($cur_rank = $db->fetch_assoc($result))
		echo "\t\t\t\t\t\t\t\t".'<tr><td class="tcl"><input type="text" class="form-control"name="rank['.$cur_rank['id'].']" value="'.pun_htmlspecialchars($cur_rank['rank']).'" size="24" maxlength="50" /></td><td class="tc2"><input type="text" class="form-control"name="min_posts['.$cur_rank['id'].']" value="'.$cur_rank['min_posts'].'" size="7" maxlength="7" /></td><td><input class="btn btn-primary" type="submit" name="update['.$cur_rank['id'].']" value="'.$lang_back['Update'].'" />&#160;<input class="btn btn-danger" type="submit" name="remove['.$cur_rank['id'].']" value="'.$lang_back['Remove'].'" /></td></tr>'."\n";

?>
                    </tbody>
                </table>
<?php

}
else
	echo "\t\t\t\t\t\t\t".'<p>'.$lang_back['No ranks in list'].'</p>'."\n";

?>
            </fieldset>
        </form>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
