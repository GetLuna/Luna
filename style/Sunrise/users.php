<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row">
	<div class="col-sm-5">
		<h2><?php echo $lang['Users'] ?></h2>
	</div>
	<?php if ($luna_user['g_search_users'] == '1'): ?>
	<div class="col-sm-7">
		<span class="pull-right">
			<form class="form-inline" id="userlist" method="get" action="userlist.php">
				<select class="form-control" name="sort">
					<option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang['Sort username'] ?></option>
					<option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang['Sort registered'] ?></option>
					<option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang['Sort no of posts'] ?></option>
				</select>
				<div class="input-group">
					<input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php echo $lang['Search'] ?>" maxlength="25" />
					<span class="input-group-btn">
						<button class="btn btn-primary" type="submit" name="search" accesskey="s" /><span class="fa fa-search"></span></button>
					</span>
				</div>
			</form>
		</span>
	</div>
	<?php endif; ?>
</div>

<div class="row">
    <div class="col-sm-12">
        <ul class="pagination pagination-user">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>

<div class="userlist">
	<div class="row forum-header">
		<div class="col-sm-8 col-xs-9"><?php echo $lang['Username'] ?></div>
		<div class="col-sm-1 align-center hidden-xs"><p class="text-center"><?php echo $lang['Posts table'] ?></p></div>
		<div class="col-sm-3 col-xs-3"><?php echo $lang['Registered table'] ?></div>
	</div>
<?php

// Retrieve a list of user IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT u.id FROM '.$db->prefix.'users AS u WHERE u.id>1 AND u.group_id!='.FORUM_UNVERIFIED.(!empty($where_sql) ? ' AND '.implode(' AND ', $where_sql) : '').' ORDER BY '.$sort_query.', u.id ASC LIMIT '.$start_from.', 50') or error('Unable to fetch user IDs', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result)) {
    $user_ids = array();
    for ($i = 0;$cur_user_id = $db->result($result, $i);$i++)
        $user_ids[] = $cur_user_id;

    // Grab the users
    $result = $db->query('SELECT u.id, u.username, u.title, u.num_posts, u.registered, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id IN('.implode(',', $user_ids).') ORDER BY '.$sort_query.', u.id ASC') or error('Unable to fetch user list', __FILE__, __LINE__, $db->error());

    while ($user_data = $db->fetch_assoc($result)) {
        $user_title_field = get_title($user_data);
        $user_avatar = generate_avatar_markup($user_data['id']);

?>
	<div class="row user-row">
		<div class="col-sm-8 col-xs-9">
			<span class="user-avatar thumbnail">
				<?php echo $user_avatar; ?>
			</span>
			<span class="userlist-name"><?php echo '<a href="profile.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?> <small><?php echo $user_title_field ?></small></span>
		</div>
		<div class="col-sm-1 collum-count align-center hidden-xs"><p class="text-center"><?php echo forum_number_format($user_data['num_posts']) ?></p></div>
		<div class="col-sm-3 col-xs-3 collum-count"><?php echo format_time($user_data['registered'], true) ?></div>
	</div>
<?php

    }
} else
    echo "\t\t\t".'<p>'.$lang['No hits'].'</p>'."\n";

?>
</div>

<div class="row">
    <div class="col-sm-12">
        <ul class="pagination pagination-user">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>
<?php

    require FORUM_ROOT.'footer.php';