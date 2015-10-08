<div class="postview">
	<div id="p<?php echo $cur_post['id'] ?>" class="row topic <?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($cur_post['id'] == $cur_topic['first_post_id']) echo ' firstpost'; ?><?php if ($post_count == 1) echo ' onlypost'; ?><?php if ($cur_post['marked'] == true) echo ' marked'; ?><?php if ($cur_post['id'] == $cur_topic['answer']) echo ' answer'; ?>">
		<div class="col-md-3">
			<div class="profile-card">
				<div class="profile-card-head">
					<div class="user-avatar <?php echo $is_online; ?>">
						<?php echo $user_avatar ?>
					</div>
					<h2><?php echo $username ?></h2>
					<h3><?php echo $user_title ?></h3>
				</div>
				<div class="profile-card-body hidden-sm hidden-xs">
					<?php if (count($user_info)) echo "\t\t\t\t\t\t".implode("\n\t\t\t\t\t\t", $user_info)."\n"; ?>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default panel-topic panel-border">
				<div class="panel-heading">
					<div class="comment-arrow hidden-sm hidden-xs"></div>
					<h3 class="panel-title"><span class="postnr">#<?php echo ($start_from + $post_count) ?><span class="pull-right"><a class="posttime" href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a></span></span></h3>
				</div>
				<div class="panel-body">
					<?php echo $cur_post['message']."\n" ?>
					<?php if ($cur_post['edited'] != '') echo '<p class="postedit"><em>'.$lang['Last edit'].' '.luna_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'; ?>
					<?php if (($signature != '') || (!$luna_user['is_guest'])) echo '<hr />'; ?>
					<?php if ($signature != '') echo "\t\t\t\t\t".'<div class="postsignature">'.$signature.'</div>'."\n"; ?>
					<?php if (!$luna_user['is_guest']) { ?><div class="pull-right post-actions"><?php if (count($post_actions)) echo implode(" &middot; ", $post_actions) ?></div><?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>