<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="row topicview">
	<div class="col-sm-3">
		<div class="list-group list-group-forum list-group-manage">
			<a class="list-group-item" href="index.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
		</div>
		<?php if (!$luna_user['is_guest'] && $luna_config['o_topic_subscriptions'] == '1') { ?>
		<hr />
		<div class="list-group list-group-forum">
			<?php if ($cur_topic['is_subscribed']) { ?>
				<a class="list-group-item" href="misc.php?action=unsubscribe&amp;tid=<?php echo $id ?>"><span class="fa fa-fw fa-star-o"></span> <?php echo $lang['Unsubscribe'] ?></a>
			<?php } else { ?>
				<a class="list-group-item" href="misc.php?action=subscribe&amp;tid=<?php echo $id ?>"><span class="fa fa-fw fa-star"></span> <?php echo $lang['Subscribe'] ?></a>
			<?php } ?>
		</div>
		<?php } ?>
		<?php if ($is_admmod): ?>
		<hr />
		<div class="list-group list-group-forum">
			<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php echo $lang['Moderate topic'] ?></a>
			<?php if($num_pages > 1) { ?>
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&action=all"><span class="fa fa-fw fa-list"></span> <?php echo $lang['All'] ?></a>
			<?php } ?>
			<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&move_topics=<?php echo $id ?>"><span class="fa fa-fw fa-arrows-alt"></span> <?php echo $lang['Move topic'] ?></a>
			<?php if ($cur_topic['closed'] == '1') { ?>
				<a class="list-group-item list-group-disabled" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&open=<?php echo $id ?>"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Open topic'] ?></a>
			<?php } else { ?>
				<a class="list-group-item list-group-enabled" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&close=<?php echo $id ?>"><span class="fa fa-fw fa-times"></span> <?php echo $lang['Close topic'] ?></a>
			<?php } ?>
			
			<?php if ($cur_topic['sticky'] == '1') { ?>
				<a class="list-group-item list-group-enabled" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&unstick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php echo $lang['Unstick topic'] ?></a>
			<?php } else { ?>
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&stick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php echo $lang['Stick topic'] ?></a>
			<?php } ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="col-sm-9">
		<div class="jumbotron thread-jumbotron">
			<span class="pull-right"><?php echo $paging_links ?></span>
			<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>
		</div>
		<?php draw_topic_list(); ?>
		<form method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
		<?php draw_editor('10'); ?>
		</form>
		<div class="pull-right"><?php echo $paging_links ?></div>
	</div>
</div>