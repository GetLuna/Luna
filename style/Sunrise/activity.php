<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$jumbo_style = 'style="background:'.$user['color'].';"';

?>

</div>
<div class="jumbotron me-jumbotron"<?php echo $jumbo_style ?>>
	<div class="container">
        <div class="media">
            <a class="pull-left" href="#">
                <?php echo generate_avatar_markup($luna_user['id']) ?>
            </a>
            <div class="media-body">
                <h2 class="media-heading"><?php echo $user['username']; ?></h2>
            </div>
        </div>
	</div>
</div>
<div class="container">
<div class="col-sm-3 profile-nav">
<?php
    generate_me_menu('view');
?>
</div>
<div class="col-sm-9 col-profile">
	<?php if (file_exists('z.txt') && ($luna_config['o_reading_list'] == '1')) { ?>
	<h1>Reading list</h1>
	<table class="table">
		<thead>
			<tr>
				<th>Topic</th>
				<th>Forum</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3">No reading list items</td>
			</tr>
		</tbody>
	</table>
	<?php } ?>
	<h1 class="visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline">Activity feed</h1>
	<ul class="nav nav-tabs pull-right visible-xs-inline visible-sm-inline visible-md-inline visible-lg-inline activity-tab" role="tablist">
		<li role="presentation" class="active"><a href="#posts" role="tab" data-toggle="tab">Recent posts</a></li>
		<li role="presentation"><a href="#topics" role="tab" data-toggle="tab">Recent topics</a></li>
		<li role="presentation"><a href="#subscriptions" role="tab" data-toggle="tab">Subscriptions</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="posts">
			<h2 class="activity-header">Recent posts<span class="pull-right"><a href="#" class="btn btn-primary">Show everything</a></span></h2>
<?php
	$result = $db->query('SELECT id, poster, poster_id, message, posted, edited, edited_by, marked FROM '.$db->prefix.'posts WHERE poster_id='.$user['id'].' ORDER BY id DESC LIMIT 10') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	while ($cur_post = $db->fetch_assoc($result)) {
		$cur_post['message'] = parse_message($cur_post['message']);
?>
			<div class="row comment">
				<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<a class="posttime" href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a>
							<hr />
							<?php echo $cur_post['message']."\n" ?>
							<?php if ($cur_post['edited'] != '') echo '<p class="postedit"><em>'.$lang['Last edit'].' '.luna_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'; ?>
						</div>
					</div>
				</div>
			</div>
<?php
	}
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="topics">
			<h2 class="activity-header">Recent topics<span class="pull-right"><a href="#" class="btn btn-primary">Show everything</a></span></h2>
<?php
	$result = $db->query('SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, forum_id FROM '.$db->prefix.'topics WHERE poster=\''.$user['username'].'\' ORDER BY id DESC LIMIT 10') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
	while ($cur_topic = $db->fetch_assoc($result)) {
		$subject = $cur_topic['subject'];
?>
<div class="topic-entry <?php echo $item_status ?>">
    <h3><a href="viewtopic.php?id=<?php echo $cur_topic['id'] ?>"><?php echo $subject ?></a> <small><?php echo $last_post ?></small></h3>
</div>
<?php
	}
?>
			
		</div>
		<div role="tabpanel" class="tab-pane" id="subscriptions">
			<h2 class="activity-header">Subscriptions</h2>
			<p class="lead">Not available in this build</p>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#user").focus();
		var hash = location.hash, hashPieces = hash.split('?'), activeTab = $('[href=' + hashPieces[0] + ']');
		activeTab && activeTab.tab('show');
	});
</script>