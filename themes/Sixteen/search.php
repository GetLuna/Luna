<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<h2 class="profile-title"><?php _e('Search', 'luna') ?><span class="btn-group pull-right"><a class="btn btn-default" href="search.php?section=advanced"><?php _e('Advanced', 'luna') ?></a></span></h2>
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
