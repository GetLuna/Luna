<div id="p<?php echo $cur_post['id'] ?>" class="comment <?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if (!isset($inbox)) { if ($cur_post['id'] == $cur_topic['first_post_id']) echo ' firstpost'; if ($post_count == 1) echo ' onlypost'; if ($cur_post['marked'] == true) echo ' marked'; if ($cur_post['soft'] == true) echo ' soft'; } ?><?php if ($cur_post['id'] == $cur_topic['answer']) echo ' answer'; ?>">
	<div class="well well-comment">
		<div class="media">
			<div class="media-left">
				<?php echo $user_avatar; ?>
			</div>
			<div class="media-body">
				<h4 class="media-heading"><?php printf(__('By %s', 'luna'), $username) ?><small> <?php __('on', 'luna') ?> <a class="posttime" href="<?php if (!isset($inbox)) { echo 'viewtopic.php?pid='.$cur_post['id'].'#p'.$cur_post['id']; } else { echo 'viewinbox.php?tid='.$cur_post['shared_id'].'&mid='.$cur_post['mid']; } ?>"><?php echo format_time($cur_post['posted']) ?></a></small></h4>
			</div>
		</div>
		<div class="well-content">
			<?php echo $cur_post['message']."\n" ?>
			<?php if (!isset($inbox)) { if ($cur_post['edited'] != '') echo '<p class="postedit"><em>'.__('Last edited by', 'luna').' '.luna_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'; }; ?>
			<?php if (($signature != '') || (!$luna_user['is_guest'])) echo '<hr />'; ?>
			<?php if ($signature != '') echo "\t\t\t\t\t".'<div class="postsignature">'.$signature.'</div>'."\n"; ?>
			<?php if (!$luna_user['is_guest']) { ?><div class="post-actions btn-group fade-50"><?php if (count($post_actions)) echo implode(" &middot; ", $post_actions) ?></div><?php } ?>
		</div>
	</div>
</div>