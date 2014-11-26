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

<div class="modal fade modal-form" id="newpass" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $lang['Change pass'] ?></h4>
			</div>
			<div class="modal-body">
			<form id="change_pass" method="post" action="me.php?action=change_pass&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
				<input type="hidden" name="form_sent" value="1" />
				<fieldset>
					<?php if (!$luna_user['is_admmod']): ?>
						<input class="form-control" type="password" name="req_old_password" placeholder="<?php echo $lang['Old pass'] ?>" />
					<?php endif; ?>
					<input class="form-control" type="password" name="req_new_password1" placeholder="<?php echo $lang['New pass'] ?>" />
					<input class="form-control" type="password" name="req_new_password2" placeholder="<?php echo $lang['Confirm new pass'] ?>" />
					<p class="help-block"><?php echo $lang['Pass info'] ?></p>
				</fieldset>
			</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-default" name="update"><?php echo $lang['Save'] ?></button>
			</div>
		</div>
	</div>
</div>