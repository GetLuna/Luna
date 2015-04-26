<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="row topicview">
	<div class="col-xs-12">
		<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>
		<div class="btn-group">
			<a class="btn btn-primary" href="index.php"><span class="fa fa-fw fa-home"></span></a>
			<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
			<a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_topic['id'] ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
		</div>
		<?php echo $paging_links ?>
		<?php draw_topic_list(); ?>
		<form method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
		<?php draw_editor('10'); ?>
		</form>
		<?php echo $paging_links ?>
		
		<?php if ($is_admmod): ?>
		<div class="btn-toolbar">
			<div class="btn-group">
				<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate topic', 'luna') ?></a>
				<?php if($num_pages > 1) { ?>
					<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&tid=<?php echo $id ?>&action=all"><span class="fa fa-fw fa-list"></span> <?php _e('Show all posts', 'luna') ?></a>
				<?php } ?>
				<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&move_topics=<?php echo $id ?>"><span class="fa fa-fw fa-arrows-alt"></span> <?php _e('Move topic', 'luna') ?></a>
			</div>
			<div class="btn-group">
				<?php if ($cur_topic['closed'] == '1') { ?>
					<a class="btn btn-success" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&open=<?php echo $id ?>"><span class="fa fa-fw fa-check"></span> <?php _e('Open topic', 'luna') ?></a>
				<?php } else { ?>
					<a class="btn btn-danger" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&close=<?php echo $id ?>"><span class="fa fa-fw fa-times"></span> <?php _e('Close topic', 'luna') ?></a>
				<?php } ?>
				
				<?php if ($cur_topic['sticky'] == '1') { ?>
					<a class="btn btn-success" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&unstick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Unstick topic', 'luna') ?></a>
				<?php } else { ?>
					<a class="btn btn-primary" href="backstage/moderate.php?fid=<?php echo $cur_topic['forum_id'] ?>&stick=<?php echo $id ?>"><span class="fa fa-fw fa-thumb-tack"></span> <?php _e('Stick topic', 'luna') ?></a>
				<?php } ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>