<div class="modal fade modal-form" id="newmail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $lang['Change email'] ?></h4>
			</div>
			<div class="modal-body">
				<form id="change_email" method="post" action="me.php?action=change_email&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
					<fieldset>
						<input type="hidden" name="form_sent" value="1" />
						<input class="form-control" type="text" name="req_new_email" placeholder="<?php echo $lang['New email'] ?>" />
						<input class="form-control" type="password" name="req_password" placeholder="<?php echo $lang['Password'] ?>" />
						<p><?php echo $lang['Email instructions'] ?></p>
					</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="new_email"><?php echo $lang['Save'] ?></button>
			</div>
		</div>
	</div>
</div>