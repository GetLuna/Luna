<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

</div>
<form id="search" method="get" action="search.php?section=advanced">
	<div class="jumbotron">
		<div class="container">
			<h2 class="forum-title"><span class="fa fa-fw fa-search"></span> <?php _e('Search', 'luna') ?></h2>
			<span class="pull-right">
				<input class="btn btn-default" type="submit" name="search" value="<?php _e('Search', 'luna') ?>" accesskey="s" />
			</span>
		</div>
	</div>
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-body">
				<fieldset class="form-inline">
					<input type="hidden"  name="action" value="search" />
					<input placeholder="<?php _e('Keyword', 'luna') ?>" class="form-control" type="text" name="keywords" maxlength="100" />
					<input placeholder="<?php _e('Author', 'luna') ?>"  class="form-control" id="author" type="text" name="author" maxlength="25" />
					<select class="form-control" id="search_in" name="search_in">
						<option value="0"><?php _e('Message text and thread subject', 'luna') ?></option>
						<option value="1"><?php _e('Message text only', 'luna') ?></option>
						<option value="-1"><?php _e('Thread subject only', 'luna') ?></option>
					</select>
					<select class="form-control" name="sort_by">
						<option value="0"><?php _e('Comment time', 'luna') ?></option>
						<option value="1"><?php _e('Author', 'luna') ?></option>
						<option value="2"><?php _e('Subject', 'luna') ?></option>
						<option value="3"><?php _e('Forum', 'luna') ?></option>
					</select>
					<select class="form-control" name="sort_dir">
						<option value="DESC"><?php _e('Descending', 'luna') ?></option>
						<option value="ASC"><?php _e('Ascending', 'luna') ?></option>
					</select>
					<select class="form-control" name="show_as">
						<option value="threads"><?php _e('Threads', 'luna') ?></option>
						<option value="comments"><?php _e('Comments', 'luna') ?></option>
					</select>
				</fieldset>
				<fieldset>
					<div class="row">
						<?php echo draw_search_forum_list(); ?>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</form>
<div class="container">