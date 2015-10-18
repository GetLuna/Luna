<div id="p<?php echo $cur_comment['id'] ?>" class="comment <?php echo ($comment_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if (!isset($inbox)) { if ($cur_comment['id'] == $cur_thread['first_comment_id']) echo ' firstcomment'; if ($comment_count == 1) echo ' only-comment'; if ($cur_comment['marked'] == true) echo ' marked'; if ($cur_comment['soft'] == true) echo ' soft'; } ?><?php if ($cur_comment['id'] == $cur_thread['answer'] && $cur_forum['solved'] == 1) echo ' answer'; ?>">
	<div class="well well-comment">
		<div class="media">
			<div class="media-left">
				<?php echo $user_avatar; ?>
			</div>
			<div class="media-body">
				<h4 class="media-heading"><?php printf(__('By %s', 'luna'), $username) ?><small> <?php __('on', 'luna') ?> <a class="commenttime" href="<?php if (!isset($inbox)) { echo 'thread.php?pid='.$cur_comment['id'].'#p'.$cur_comment['id']; } else { echo 'viewinbox.php?tid='.$cur_comment['shared_id'].'&mid='.$cur_comment['mid']; } ?>"><?php echo format_time($cur_comment['commented']) ?></a></small></h4>
			</div>
		</div>
		<div class="well-content">
			<?php echo $cur_comment['message']."\n" ?>
			<?php if (!isset($inbox)) { if ($cur_comment['edited'] != '') echo '<p class="comment-edited"><em>'.__('Last edited by', 'luna').' '.luna_htmlspecialchars($cur_comment['edited_by']).' ('.format_time($cur_comment['edited']).')</em></p>'; }; ?>
			<?php if (($signature != '') || (!$luna_user['is_guest'])) echo '<hr />'; ?>
			<?php if ($signature != '') echo "\t\t\t\t\t".'<div class="comment-signature">'.$signature.'</div>'."\n"; ?>
			<?php if (!$luna_user['is_guest']) { ?><div class="comment-actions btn-group fade-50"><?php if (count($comment_actions)) echo implode(" &middot; ", $comment_actions) ?></div><?php } ?>
		</div>
	</div>
</div>