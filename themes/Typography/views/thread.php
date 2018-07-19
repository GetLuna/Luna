<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="main container">
	<div class="jumbotron default">
		<h2><?php echo luna_htmlspecialchars($cur_thread['subject']) ?></h2>
	</div>
	<div class="thread">
		<div class="btn-toolbar btn-toolbar-options">
			<a class="btn btn-light" href="viewforum.php?id=<?php echo $cur_thread['forum_id'] ?>"><span class="fas fa-fw fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_thread['forum_name']) ?></a>
			<?php if (!$luna_user['is_guest'] && $luna_config['o_thread_subscriptions'] == '1') { ?>
				<?php if ($cur_thread['is_subscribed']) { ?>
					<a class="btn btn-light btn-light-active" href="misc.php?action=unsubscribe&amp;tid=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-star"></span></a>
				<?php } else { ?>
					<a class="btn btn-light" href="misc.php?action=subscribe&amp;tid=<?php echo $id ?><?php echo $token_url ?>"><span class="far fa-fw fa-star"></span></a>
				<?php } ?>
			<?php } ?>
			<?php if ($is_admmod): ?>
				<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>"><span class="fas fa-fw fa-eye"></span></a>
				<?php if($num_pages > 1) { ?>
					<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&tid=<?php echo $id ?>&action=all<?php echo $token_url ?>"><span class="fas fa-fw fa-list"></span></a>
				<?php } ?>
				<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&move_threads=<?php echo $id ?>"><span class="fas fa-fw fa-arrows-alt"></span></a>
				<?php if ($cur_thread['closed'] == '1') { ?>
					<a class="btn btn-light btn-light-active" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&open=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-lock"></span></a>
				<?php } else { ?>
					<a class="btn btn-light btn-light-active" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&close=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-unlock"></span></a>
				<?php } ?>

				<?php if ($cur_thread['pinned'] == '1') { ?>
					<a class="btn btn-light btn-light-active" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&unpin=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-thumbtack"></span></a>
				<?php } else { ?>
					<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&pin=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-thumbtack"></span></a>
				<?php } ?>

				<?php if ($cur_thread['important'] == '1') { ?>
					<a class="btn btn-light btn-light-active" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&unimportant=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-map-marker"></span></a>
				<?php } else { ?>
					<a class="btn btn-light" href="backstage/moderate.php?fid=<?php echo $cur_thread['forum_id'] ?>&important=<?php echo $id ?><?php echo $token_url ?>"><span class="fas fa-fw fa-map-marker"></span></a>
				<?php } ?>
			<?php endif; ?>
		</div>
		<?php typography_paginate($paging_links) ?>
		<?php draw_comment_list() ?>
		<?php typography_paginate($paging_links) ?>
		<?php if ($comment_field): ?>
			<form method="post" action="comment.php?tid=<?php echo $id ?>" onsubmit="window.onbeforeunload=null;this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
			<?php draw_editor('10', 1); ?>
			</form>
		<?php endif; ?>
	</div>
</div>