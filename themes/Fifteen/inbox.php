<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($luna_user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
	load_me_nav('inbox');
?>
</div>
<div class="col-sm-9 profile">
	<?php load_inbox_nav('inbox'); ?>
	<p><span class="pages-label"><?php echo paginate($num_pages, $page, 'inbox.php?') ?></span></p>
	<form method="post" action="inbox.php">
		<fieldset>
            <div class="btn-toolbar">
            	<div class="btn-group">
					<button type="submit" name="markread" class="btn btn-primary"><span class="fa fa-fw fa-eye"></span> <?php _e('Mark as read', 'luna') ?></button>
					<button type="submit" name="markunread" class="btn btn-primary"><span class="fa fa-fw fa-eye-slash"></span> <?php _e('Mark as unread', 'luna') ?></button>
                </div>
                <div class="btn-group">
					<a href="#" data-toggle="modal" data-target="#delete-form" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete', 'luna') ?></a>
					<?php include load_page('inbox-delete-post.php'); ?>
                </div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('Inbox', 'luna') ?></h3>
				</div>
				<input type="hidden" name="box" value="0" />
				<table class="table">
					<thead>
						<tr>
							<th style="width: 18px;"><input type="checkbox" id="checkAllButon" value="1" onclick="checkAll('selected_messages[]','checkAllButon');" /></th>
							<th><?php _e('Messages', 'luna') ?></th>
							<th><?php _e('Sender', 'luna') ?></th>
							<th><?php _e('Receiver(s)', 'luna') ?></th>
							<th><?php _e('Last comment', 'luna') ?></th>
						</tr>
					</thead>
					<tbody>
<?php
// Fetch messages
$result = $db->query("SELECT * FROM ".$db->prefix."messages WHERE show_message=1 AND owner='".$luna_user['id']."' ORDER BY last_post DESC LIMIT ".$limit) or error("Unable to find the list of the pms.", __FILE__, __LINE__, $db->error()); 

// If there are messages in this folder.
if ($db->num_rows($result)) {
	while ($cur_mess = $db->fetch_assoc($result)) {
		$item_status = 'roweven';
		if ($cur_mess['showed'] == '0') {
			$item_status .= ' inew';
			$icon_type = 'icon icon-new';
			$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.
					   '<strong>'.luna_htmlspecialchars($cur_mess['subject']).'</strong>'.
					   '</a>';
		} else {
			$icon_type = 'icon';
			$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.
					   luna_htmlspecialchars($cur_mess['subject']).
					   '</a>';
		}
		
		$last_post = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'&amp;pid='.$cur_mess['last_post_id'].'#p'.$cur_mess['last_post_id'].'">'.format_time($cur_mess['last_post']).'</a> <span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_mess['last_poster']).'</span>';
?>
						<tr class="<?php echo $item_status ?>">
							<td>
								<input type="checkbox" name="selected_messages[]" value="<?php echo $cur_mess['shared_id'] ?>" />
							</td>
							<td>
								<div class="<?php echo $icon_type ?>"></div>
								<div><?php echo $subject ?></div>
							</td>
							<td>
		<?php
		if ($luna_user['g_view_users'] == '1')
			echo '<a href="profile.php?id='.$cur_mess['sender_id'].'">'.luna_htmlspecialchars($cur_mess['sender']).'</a>';
		else
			echo luna_htmlspecialchars($cur_mess['sender']);
		?>
							</td>
							<td>
		<?php
			if ($luna_user['g_view_users'] == '1') {
				$ids_list = explode(', ', $cur_mess['receiver_id']);
				$sender_list = explode(', ', $cur_mess['receiver']);
				$sender_list = str_replace('Deleted', __('Deleted', 'luna'), $sender_list);
				
				for($i = '0'; $i < count($ids_list); $i++){
				echo '<a href="profile.php?id='.$ids_list[$i].'">'.luna_htmlspecialchars($sender_list[$i]).'</a>';
				
				if($ids_list[$i][count($ids_list[$i])-'1'])
					echo'<br />';
				} 
			} else
				echo luna_htmlspecialchars($cur_mess['receiver']);
		?>
							</td>
							<td><?php echo $last_post ?></td>
						</tr>
<?php
	}
} else
	echo "\t".'<tr><td colspan="4">'.__('No messages', 'luna').'</td></tr>'."\n";
?>
					</tbody>
				</table>
			</div>
			<p><?php echo paginate($num_pages, $page, 'inbox.php?') ?></p>
		</fieldset>
	</form>
</div>