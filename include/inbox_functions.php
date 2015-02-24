<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_inbox_nav($page) {
	global $lang, $luna_config, $luna_user, $tid;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#inbox-nav">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="inbox.php" class="navbar-brand"><span class="fa fa-fw fa-paper-plane-o"></span> Inbox</a>
	</div>
	<div class="collapse navbar-collapse" id="inbox-nav">
		<ul class="nav navbar-nav">
			<li>
<?php
	if ($luna_user['g_pm_limit'] != '0') { // && !$luna_user['is_admmod']
		$per_cent_box = ceil($luna_user['num_pms'] / $luna_user['g_pm_limit'] * '100');	
		echo '<div class="progress" style="width: 250px;"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$per_cent_box.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$per_cent_box.'%;">'.$per_cent_box.'%</div></div>';
	}
?>
			</li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<div class="btn-compose pull-left">
				<?php if ($page == 'view') { ?>
					<a type="button" class="btn btn-danger navbar-btn" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fa fa-fw fa-reply"></span> Reply</a>
				<?php } ?>
				<a type="button" class="btn btn-danger navbar-btn" href="new_inbox.php"><span class="fa fa-fw fa-pencil"></span> Compose</a>
			</div>
		</ul>
	</div>
</nav>
<?php

}