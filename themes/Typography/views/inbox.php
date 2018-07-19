<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="main profile container">
	<div class="jumbotron default">
		<h2><?php echo $user['username'] ?></h2>
	</div>
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('inbox'); ?>
		</div>
		<div class="col-12 col-sm-9">
			<form method="post" class="tab-set" action="inbox.php">
				<div class="title-block title-block-primary">
					<h2><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Inbox', 'luna') ?></h2>
				</div>
				<div class="tab-content">
					<div class="btn-toolbar btn-toolbar-profile">
						<div class="btn-group">
							<button type="submit" name="markread" class="btn btn-primary"><span class="fas fa-fw fa-eye"></span> <?php _e('Read', 'luna') ?></button>
							<button type="submit" name="markunread" class="btn btn-primary"><span class="fas fa-fw fa-eye-slash"></span> <?php _e('Unread', 'luna') ?></button>
						</div>
						<div class="btn-group">
							<a href="#" data-toggle="modal" data-target="#delete-form" class="btn btn-danger"><span class="fas fa-fw fa-trash"></span> <?php _e('Delete', 'luna') ?></a>
							<?php include load_page('inbox-delete-comment.php'); ?>
						</div>
						<div class="btn-group pull-right">
							<a type="button" class="btn btn-success" href="new_inbox.php"><span class="fas fa-fw fa-pencil-alt"></span> <?php _e('New', 'luna') ?></a>
						</div>
					</div>
					<?php typography_paginate($paging_links) ?>
					<?php
					if ($luna_user['g_inbox_limit'] != '0' && !$luna_user['is_admmod']) {
						$per_cent_box = ceil($luna_user['num_inbox'] / $luna_user['g_inbox_limit'] * '100');
						echo '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="'.$per_cent_box.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$per_cent_box.'%;"><span class="progress-text">'.$per_cent_box.'%</span></div></div>';
					}
					?>
					<input type="hidden" name="box" value="0" />
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-hover">
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
			$result = $db->query('SELECT * FROM '.$db->prefix.'messages WHERE show_message=1 AND owner='.$id.' ORDER BY last_comment DESC LIMIT '.$limit) or error("Unable to find the list of the Inbox messages.", __FILE__, __LINE__, $db->error());

            $comment_count = 0;
			
			// If there are messages in this folder.
			if ($db->num_rows($result)) {
				while ($cur_mess = $db->fetch_assoc($result)) {
					++$comment_count;
					$item_status = ($comment_count % 2 == 0) ? 'roweven' : 'rowodd';
					if ($cur_mess['showed'] == '0') {
						$item_status .= ' inew';
						$icon_type = 'icon icon-new';
						$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.'<strong>'.luna_htmlspecialchars($cur_mess['subject']).'</strong>'.'</a>';
					} else {
						$icon_type = 'icon';
						$subject = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'">'.luna_htmlspecialchars($cur_mess['subject']).'</a>';
					}
			
					$last_comment = '<a href="viewinbox.php?tid='.$cur_mess['shared_id'].'&amp;mid='.$cur_mess['id'].'&amp;pid='.$cur_mess['last_comment_id'].'#p'.$cur_mess['last_comment_id'].'">'.format_time($cur_mess['last_comment']).'</a> <span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_mess['last_commenter']).'</span>';
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
				
								if(count($ids_list) > $i)
									echo '<br />';
							}
						} else
							echo luna_htmlspecialchars($cur_mess['receiver']);
					?>
								</td>
								<td><?php echo $last_comment ?></td>
							</tr>
			<?php
				}
			} else
				echo '<tr><td colspan="5">'.__('No messages', 'luna').'</td></tr>';
			?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>