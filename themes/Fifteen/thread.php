<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="main container">
	<div class="row thread">
		<div class="col-sm-3">
			<div class="list-group list-group-luna">
				<a class="list-group-item" href="viewforum.php?id=<?php echo $cur_thread['forum_id'] ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_thread['forum_name']) ?></a>
			</div>
			<?php if (!$luna_user['is_guest'] && $luna_config['o_thread_subscriptions'] == '1') { ?>
			<hr />
			<div class="list-group list-group-luna">
				<?php if ($cur_thread['is_subscribed']) { ?>
					<a class="list-group-item list-group-enabled" href="misc.php?action=unsubscribe&amp;tid=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-star-o"></span> <?php _e('Unsubscribe', 'luna') ?></a>
				<?php } else { ?>
					<a class="list-group-item" href="misc.php?action=subscribe&amp;tid=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-star"></span> <?php _e('Subscribe', 'luna') ?></a>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($is_admmod): ?>
			<hr />
			<div class="list-group list-group-luna">
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate thread', 'luna') ?></a>
				<?php if($num_pages > 1) { ?>
					<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&tid=<?php echo $id ?>&action=all<?php echo $token_url ?>"><span class="fa fa-fw fa-list"></span> <?php _e('Show all comments', 'luna') ?></a>
				<?php } ?>
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&move_threads=<?php echo $id ?>"><span class="fa fa-fw fa-arrows-alt"></span> <?php _e('Move thread', 'luna') ?></a>
				<?php if ($cur_thread['closed'] == '1') { ?>
					<a class="list-group-item list-group-disabled" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&open=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-check"></span> <?php _e('Open thread', 'luna') ?></a>
				<?php } else { ?>
					<a class="list-group-item list-group-enabled" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&close=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-times"></span> <?php _e('Close thread', 'luna') ?></a>
				<?php } ?>
	
				<?php if ($cur_thread['pinned'] == '1') { ?>
					<a class="list-group-item list-group-enabled" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&unpin=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Unpin thread', 'luna') ?></a>
				<?php } else { ?>
					<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&pin=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Pin thread', 'luna') ?></a>
				<?php } ?>
	
				<?php if ($cur_thread['important'] == '1') { ?>
					<a class="list-group-item list-group-enabled" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&unimportant=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-map-marker"></span> <?php _e('Unimportant thread', 'luna') ?></a>
				<?php } else { ?>
					<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&important=<?php echo $id ?><?php echo $token_url ?>"><span class="fa fa-fw fa-map-marker"></span> <?php _e('Important thread', 'luna') ?></a>
				<?php } ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="col-sm-9">
			<div class="title-block title-block-primary">
				<span class="pull-right"><?php echo $paging_links ?></span>
				<h2><?php echo luna_htmlspecialchars($cur_thread['subject']) ?></h2>
			</div>
			<?php draw_comment_list(); ?>
			<?php if ($comment_field): ?>
				<form method="post" action="comment.php?tid=<?php echo $id ?>" onsubmit="window.onbeforeunload=null;this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
				<?php draw_editor('10', 1); ?>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>