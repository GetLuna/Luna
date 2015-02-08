<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Search'] ?></h2>
		<?php if ($luna_config['o_enable_advanced_search'] == 1) { ?>
		<span class="pull-right">
			<a class="btn btn-default hidden-xs" href="search.php?section=advanced"><?php echo $lang['Advanced'] ?></a>
		</span>
		<?php } ?>
	</div>
</div>
<div class="container">
<form id="search" method="get" action="search.php?section=simple">
	<div class="panel panel-default">
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="action" value="search" />
				<div class="input-group"><input class="form-control" type="text" name="keywords" placeholder="Search..." maxlength="100" /><span class="input-group-btn"><button class="btn btn-primary" type="submit" name="search" accesskey="s" /><span class="fa fa-search"></span> <?php echo $lang['Search'] ?></button></span></div>
			</fieldset>
		</div>
	</div>
</form>
