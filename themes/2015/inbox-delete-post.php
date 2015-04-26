<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="modal fade modal-form" id="delete-form" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $lang['Inbox'] ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo $lang['Delete post warning'] ?></p>
				<button type="submit" name="delete_multiple" class="btn btn-danger btn-block"><span class="fa fa-fw fa-trash"></span> <?php echo $lang['Delete'] ?></button>
				<a class="btn btn-primary btn-block" data-dismiss="modal" href="#"><?php echo $lang['Cancel'] ?></a>
			</div>
		</div>
	</div>
</div>