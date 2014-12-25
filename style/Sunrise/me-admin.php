<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
    load_me_nav('admin');
?>
</div>
<div class="col-sm-9">
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
                    echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected>'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
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
                <button type="submit" class="btn btn-danger" name="delete_user"><?php echo $lang['Delete user'] ?></button> <button type="submit" class="btn btn-danger" name="ban"><?php echo $lang['Ban user'] ?></button>
            </fieldset>
        </div>
    </div>
<?php
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