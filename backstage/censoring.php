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

// Load the language file
require FORUM_ROOT.'lang/'.$admin_language.'/language.php';

// Add a censor word
if (isset($_POST['add_word']))
{
	$search_for = pun_trim($_POST['new_search_for']);
	$replace_with = pun_trim($_POST['new_replace_with']);

	if ($search_for == '')
		message($lang['Must enter word message']);

	$db->query('INSERT INTO '.$db->prefix.'censoring (search_for, replace_with) VALUES (\''.$db->escape($search_for).'\', \''.$db->escape($replace_with).'\')') or error('Unable to add censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php', $lang['Word added redirect']);
}

// Update a censor word
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$search_for = pun_trim($_POST['search_for'][$id]);
	$replace_with = pun_trim($_POST['replace_with'][$id]);

	if ($search_for == '')
		message($lang['Must enter word message']);

	$db->query('UPDATE '.$db->prefix.'censoring SET search_for=\''.$db->escape($search_for).'\', replace_with=\''.$db->escape($replace_with).'\' WHERE id='.$id) or error('Unable to update censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php', $lang['Word updated redirect']);
}

// Remove a censor word
else if (isset($_POST['remove']))
{
	$id = intval(key($_POST['remove']));

	$db->query('DELETE FROM '.$db->prefix.'censoring WHERE id='.$id) or error('Unable to delete censor word', __FILE__, __LINE__, $db->error());

	// Regenerate the censoring cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censoring_cache();

	redirect('backstage/censoring.php',  $lang['Word removed redirect']);
}

$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang['Admin'], $lang['Censoring']);
$focus_element = array('censoring', 'new_search_for');
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('censoring');

?>
<h2><?php echo $lang['Censoring head'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Add word subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="censoring" method="post" action="censoring.php">
            <fieldset>
                <p><?php echo $lang['Add word info'].' '.($pun_config['o_censoring'] == '1' ? sprintf($lang['Censoring enabled'], '<a href="features.php">'.$lang['Features'].'</a>') : sprintf($lang['Censoring disabled'], '<a href="features.php">'.$lang['Features'].'</a>')) ?></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $lang['Censored word label'] ?></th>
                            <th><?php echo $lang['Replacement label'] ?></th>
                            <th><?php echo $lang['Action label'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" name="new_search_for" size="24" maxlength="60" tabindex="1" /></td>
                            <td><input type="text" class="form-control" name="new_replace_with" size="24" maxlength="60" tabindex="2" /></td>
                            <td><input class="btn btn-primary" type="submit" name="add_word" value="<?php echo $lang['Add'] ?>" tabindex="3" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Edit remove subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="censoring" method="post" action="censoring.php">
            <fieldset>
<?php

$result = $db->query('SELECT id, search_for, replace_with FROM '.$db->prefix.'censoring ORDER BY id') or error('Unable to fetch censor word list', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result))
{

?>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo $lang['Censored word label'] ?></th>
                            <th><?php echo $lang['Replacement label'] ?></th>
                            <th><?php echo $lang['Action label'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
<?php

while ($cur_word = $db->fetch_assoc($result))
echo "\t\t\t\t\t\t\t\t".'<tr><td><input type="text" class="form-control" name="search_for['.$cur_word['id'].']" value="'.pun_htmlspecialchars($cur_word['search_for']).'" size="24" maxlength="60" /></td><td><input type="text" class="form-control" name="replace_with['.$cur_word['id'].']" value="'.pun_htmlspecialchars($cur_word['replace_with']).'" size="24" maxlength="60" /></td><td><input class="btn btn-primary" type="submit" name="update['.$cur_word['id'].']" value="'.$lang['Update'].'" /><input class="btn btn-danger" type="submit" name="remove['.$cur_word['id'].']" value="'.$lang['Remove'].'" /></td></tr>'."\n";

?>
                    </tbody>
                </table>
<?php

}
else
echo "\t\t\t\t\t\t\t".'<p>'.$lang['No words in list'].'</p>'."\n";

?>
            </fieldset>
        </form>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
