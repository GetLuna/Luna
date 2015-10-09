<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron">
	<div class="container">
		<h2 class="forum-title"><span class="fa fa-fw fa-search"></span> <?php _e('Search', 'luna') ?></h2>
		<span class="pull-right naviton">
			<a class="btn btn-default" href="search.php?section=advanced"><?php _e('Advanced', 'luna') ?></a>
		</span>
	</div>
</div>
<div class="container">
<form id="search" method="get" action="search.php?section=simple">
	<div class="panel panel-default">
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="action" value="search" />
				<div class="input-group"><input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search', 'luna') ?>" maxlength="100" /><span class="input-group-btn"><button class="btn btn-primary" type="submit" name="search" accesskey="s" /><span class="fa fa-fw fa-search"></span> <?php _e('Search', 'luna') ?></button></span></div>
			</fieldset>
		</div>
	</div>
</form>
