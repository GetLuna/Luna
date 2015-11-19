<div class="">
	<div id="p<?php echo $cur_comment['id'] ?>" class="row comment <?php echo ($comment_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($cur_comment['id'] == $cur_thread['first_comment_id']) echo ' firstcomment'; ?><?php if ($comment_count == 1) echo ' only-comment'; ?><?php if ($cur_comment['soft'] == true) echo ' soft'; ?><?php if ($cur_comment['marked'] == true) echo ' marked'; ?><?php if ($cur_comment['id'] == $cur_thread['answer']) echo ' answer'; ?>">
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
			<div class="panel panel-default panel-thread panel-border">
				<div class="panel-heading">
					<div class="comment-arrow hidden-sm hidden-xs"></div>
					<h3 class="panel-title"><span class="comment-id">#<?php echo ($start_from + $comment_count) ?><span class="pull-right"><a class="commenttime" href="thread.php?pid=<?php echo $cur_comment['id'].'#p'.$cur_comment['id'] ?>"><?php echo format_time($cur_comment['commented']) ?></a></span></span></h3>
				</div>
				<div class="panel-body">
					<?php echo $cur_comment['message']."\n" ?>
					<?php if ($cur_comment['edited'] != '') echo '<p class="comment-edited"><em>'.__('Last edited by', 'luna').' '.luna_htmlspecialchars($cur_comment['edited_by']).' ('.format_time($cur_comment['edited']).')</em></p>'; ?>
					<?php if (($signature != '') || (!$luna_user['is_guest'])) echo '<hr />'; ?>
					<?php if ($signature != '') echo "\t\t\t\t\t".'<div class="comment-signature">'.$signature.'</div>'."\n"; ?>
					<?php if (!$luna_user['is_guest']) { ?><div class="pull-right comment-actions"><?php if (count($comment_actions)) echo implode(" &middot; ", $comment_actions) ?></div><?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>