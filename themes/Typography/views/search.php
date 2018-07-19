<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="main container">
	<div class="jumbotron default">
		<h2>
			<?php _e('Search', 'luna') ?>
			<?php if ($luna_config['o_enable_advanced_search'] == '1') { ?>
				<span class="float-right">
					<a class="btn btn-light" href="search.php?section=advanced"><?php _e('Advanced', 'luna') ?></a>
				</span>
			<?php } ?>
		</h2>
	</div>
	<div class="row">
		<div class="col-12">
			<form id="search" method="get" action="search.php?section=simple">
				<input type="hidden" name="action" value="search" />
				<input type="hidden" name="sort_dir" value="DESC" />
				<div class="input-group">
					<input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search', 'luna') ?>" maxlength="100" />
					<span class="input-group-append">
						<button class="btn btn-primary" type="submit" name="search" accesskey="s"><span class="fas fa-fw fa-search"></span> <?php _e('Search', 'luna') ?></button>
					</span>
				</div>
			</form>
		</div>
	</div>
</div>