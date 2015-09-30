<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_inbox_nav($page) {
	global $luna_config, $luna_user, $tid;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#inbox-nav">
			<span class="sr-only"><?php _e('Toggle navigation', 'luna') ?></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="inbox.php" class="navbar-brand"><span class="fa fa-fw fa-paper-plane-o"></span> <?php _e('Inbox', 'luna') ?></a>
	</div>
	<div class="collapse navbar-collapse" id="inbox-nav">
		<ul class="nav navbar-nav">
			<li>

			</li>
		</ul>
		<ul class="navbar-form navbar-right">
			<div class="btn-compose">
				<?php if ($page == 'view') { ?>
					<a type="button" class="btn btn-default" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fa fa-fw fa-reply"></span> <?php _e('Reply', 'luna') ?></a>
				<?php } ?>
				<a type="button" class="btn btn-default" href="new_inbox.php"><span class="fa fa-fw fa-pencil"></span> <?php _e('Compose', 'luna') ?></a>
			</div>
		</ul>
	</div>
</nav>
<?php

}