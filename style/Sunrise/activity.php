<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

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
	<h1>Activity feed</h1>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#posts" role="tab" data-toggle="tab">Recent posts</a></li>
		<li role="presentation"><a href="#topics" role="tab" data-toggle="tab">Recent topics</a></li>
		<li role="presentation"><a href="#subscriptions" role="tab" data-toggle="tab">Subscriptions</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="posts">
			<h2>Recent posts</h2>
<?php
	$result = $db->query('SELECT id, poster, poster_id, message, posted, edited, edited_by, marked FROM '.$db->prefix.'posts WHERE poster_id='.$luna_user['id'].' LIMIT 10') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	while ($cur_post = $db->fetch_assoc($result)) {
		$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);
?>
			<p><?php echo $cur_post['message']; ?></p>
<?php
	}
?>
		</div>
		<div role="tabpanel" class="tab-pane" id="topics">
			<h2>Recent topics</h2>
		</div>
		<div role="tabpanel" class="tab-pane" id="subscriptions">
			<h2>Subscriptions</h2>
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