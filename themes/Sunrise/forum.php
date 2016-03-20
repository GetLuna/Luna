<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="container main">
	<div class="row forumview <?php if (!is_subforum($forum_id, 1)) { echo 'subforumview'; } ?>">
		<div class="col-sm-3">
			<div class="list-group list-group-luna">
				<a class="list-group-item" href="index.php"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Back to index', 'luna') ?></a>
			</div>
			<hr />
            <div class="title-block title-block-primary">
                <h5><?php _e('Recent activity', 'luna') ?></h5>
            </div>
            <div class="list-group list-group-thread">
                <?php draw_index_threads_list(7, 'thread2.php'); ?>
            </div>
			<?php if ($luna_user['g_search'] == '1') { ?>
                <hr />
                <div class="list-group list-group-luna">
                    <?php echo implode('', $page_threadsearches) ?>
                </div>
			<?php } ?>
			<hr />
			<div class="list-group list-group-luna">
				<?php draw_mark_read('list-group-item', 'forumview') ?>
				<?php if ($id != '0' && $is_admmod) { ?>
					<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php _e('Moderate forum', 'luna') ?></a>
				<?php } ?>
			</div>
			<div class="visible-xs-block"><hr /></div>
		</div>
		<div class="col-sm-9">
			<div class="title-block title-block-primary title-block-forum" style="background-color: <?php echo $cur_forum['color']; ?>;">
				<h2 class="forum-title"><?php echo $faicon.luna_htmlspecialchars($cur_forum['forum_name']) ?></h2>
                <span class="naviton"><?php echo $comment_link ?></span>
				<div class="forum-desc"><?php echo $cur_forum['forum_desc'] ?></div>
			</div>
            <div class="list-group list-group-thread">
                <?php draw_subforum_list('forum.php'); ?>
            </div>
            <?php echo $paging_links ?>
			<div class="list-group list-group-thread">
				<?php draw_threads_list(); ?>
			</div>
            <?php echo $paging_links ?>
		</div>
	</div>
</div>