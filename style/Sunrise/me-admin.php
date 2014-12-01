<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="col-sm-3 profile-nav">
<?php
    generate_me_menu('admin');
?>
</div>
<div class="col-sm-9 col-profile">
<h2 class="profile-h2"><?php echo $lang['Section admin'] ?></h2>
<form id="profile7" method="post" action="me.php?section=admin&amp;id=<?php echo $id ?>">
<?php

    if ($luna_user['g_moderator'] == '1') {

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Delete ban legend'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <p><input class="btn btn-danger" type="submit" name="ban" value="<?php echo $lang['Ban user'] ?>" /></p>
            </fieldset>
        </div>
    </div>
<?php

    } else {
        if ($luna_user['id'] != $id) {

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Group membership legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
				<div class="input-group">
					<select id="group_id" class="form-control" name="group_id">
<?php

            $result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

            while ($cur_group = $db->fetch_assoc($result)) {
                if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $luna_config['o_default_user_group'] && $user['g_id'] == ''))
                    echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
                else
                    echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
            }

?>
					</select> 
					<span class="input-group-btn"> 
						<input type="submit" class="btn btn-primary" name="update_group_membership" value="<?php echo $lang['Save'] ?>" /> 
					</span> 
				</div> 
            </fieldset>
        </div>
    </div>
<?php

        }

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Delete ban legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <button type="submit" class="btn btn-danger" name="delete_user"?><?php echo $lang['Delete user'] ?></button> <button type="submit" class="btn btn-danger" name="ban"><?php echo $lang['Ban user'] ?></button>
            </fieldset>
        </div>
    </div>
<?php

        if ($user['g_moderator'] == '1' || $user['g_id'] == FORUM_ADMIN) {

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Set mods legend'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="update_forums" value="<?php echo $lang['Update forums'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <p><?php echo $lang['Moderator in info'] ?></p>
<?php

            $result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

            $cur_category = 0;
            while ($cur_forum = $db->fetch_assoc($result)) {
                if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
                    if ($cur_category)
                        echo "\n\t\t\t\t\t\t\t\t".'</div>';

                    if ($cur_category != 0)
                        echo "\n\t\t\t\t\t\t\t".'</div>'."\n";

                    echo "\t\t\t\t\t\t\t".'<div>'."\n\t\t\t\t\t\t\t\t".'<br /><strong>'.luna_htmlspecialchars($cur_forum['cat_name']).'</strong>'."\n\t\t\t\t\t\t\t\t".'<div>';
                    $cur_category = $cur_forum['cid'];
                }

                $moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

                echo "\n\t\t\t\t\t\t\t\t\t".'<input type="checkbox" name="moderator_in['.$cur_forum['fid'].']" value="1"'.((in_array($id, $moderators)) ? ' checked="checked"' : '').' /> '.luna_htmlspecialchars($cur_forum['forum_name']).'<br />'."\n";
            }

?>
            </fieldset>
        </div>
    </div>
<?php

        }
    }

    if ($luna_user['g_id'] == FORUM_ADMIN)
        $posts_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Posts table'].'</label><div class="col-sm-9"><input type="text" class="form-control" name="num_posts" value="'.$user['num_posts'].'" maxlength="8" /></div></div>';
    else
        $posts_field = '';


?>
</form>
<form id="profile1" class="form-horizontal" method="post" action="me.php?section=admin&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['User tools'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <?php echo $posts_field ?>
                <?php if ($luna_user['is_admmod']): ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Admin note'] ?></label>
                    <div class="col-sm-9">
                        <input id="admin_note" type="text" class="form-control" name="admin_note" value="<?php echo luna_htmlspecialchars($user['admin_note']) ?>" maxlength="30" />
                    </div>
                </div>
                <?php endif; ?>
            </fieldset>
        </div>
    </div>
</form>