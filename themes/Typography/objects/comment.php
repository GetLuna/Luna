<div id="p<?php echo $cur_comment['id'] ?>" class="comment comment-default <?php echo ($comment_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if (!isset($inbox)) { if ($cur_comment['id'] == $cur_thread['first_comment_id']) echo ' firstcomment'; if ($comment_count == 1) echo ' only-comment'; if ($cur_comment['marked'] == true) echo ' marked'; if ($cur_comment['soft'] == true) echo ' soft'; } ?><?php if (!isset($inbox) && $cur_comment['id'] == $cur_thread['answer'] && $cur_forum['solved'] == 1) echo ' answer'; ?>">
	<div class="comment-header">
		<div class="media">
			<img src="<?php echo get_avatar( (!isset($inbox))? $cur_comment['commenter_id'] : $cur_comment['sender_id'] ) ?>" alt="Avatar">
			<div class="media-body">
				<h4><?php printf(__('By %s', 'luna'), $username) ?><small> <?php __('on', 'luna') ?> <a class="commenttime" href="<?php if (!isset($inbox)) { echo 'thread.php?pid='.$cur_comment['id'].'#p'.$cur_comment['id']; } else { echo 'viewinbox.php?tid='.$cur_comment['shared_id'].'&mid='.$cur_comment['mid']; } ?>"><?php echo format_time($cur_comment['commented']) ?></a></small></h4>
				<h6><?php echo get_title( $cur_comment ) ?><?php if ($cur_comment['commenter_id'] != 1) { ?> &middot; <?php echo forum_number_format($cur_comment['num_comments']) ?> <?php _e( 'comments', 'luna' ) ?><?php } ?></h6>
			</div>
		</div>
	</div>
	<div class="comment-body">
		<?php echo $cur_comment['message'] ?>
        <?php if ($cur_comment['admin_note'] != '') { ?>
            <div class="alert alert-danger">
                <h4>Admin note</h4>
                <?php echo $cur_comment['admin_note'] ?>
            </div>
        <?php } ?>
		<?php if (!isset($inbox)) { if ($cur_comment['edited'] != '') echo '<p class="comment-edited"><em>'.__('Last edited by', 'luna').' '.luna_htmlspecialchars($cur_comment['edited_by']).' ('.format_time($cur_comment['edited']).')</em></p>'; }; ?>
		<?php if (!$luna_user['is_guest']) { ?><div class="comment-actions btn-group fade-50"><?php if (count($comment_actions)) echo implode("", $comment_actions) ?></div><?php } ?>
		<?php if ($signature != '') echo '<hr />'.'<div class="comment-signature">'.$signature.'</div>'; ?>
	</div>
</div>