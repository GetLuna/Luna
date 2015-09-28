<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<a href="register.php" class="navbar-brand"><span class="fa fa-fw fa-user"></span> <?php _e('Register', 'luna') ?></a>
	</div>
</nav>
<?php draw_error_panel($errors); ?>
<form class="form-horizontal" id="register" method="post" action="register.php?action=register" onsubmit="this.register.disabled=true;if(process_form(this)){return true;}else{this.register.disabled=false;return false;}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Enter the requested data', 'luna') ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="register" value="<?php _e('Register', 'luna') ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<label class="required hidden"><?php _e('If you are human please leave this field blank!', 'luna') ?><input type="text" class="form-control" name="req_username" value="" maxlength="25" /></label>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Username', 'luna') ?><span class="help-block"><?php _e('Enter a username between 2 and 25 characters long', 'luna') ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="req_user" value="<?php if (isset($_POST['req_user'])) echo luna_htmlspecialchars($_POST['req_user']); ?>" maxlength="25" />
					</div>
				</div>
<?php if ($luna_config['o_regs_verify'] == '0'): ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Password', 'luna') ?><span class="help-block"><?php _e('Passwords must be at least 6 characters long and are case sensitive', 'luna') ?></span></label>
					<div class="col-sm-9">
						<div class="row">
							<div class="col-sm-6">
								<input id="password" type="password" class="form-control" name="req_password1" value="<?php if (isset($_POST['req_password1'])) echo luna_htmlspecialchars($_POST['req_password1']); ?>" />
							</div>
							<div class="col-sm-6">
								<input type="password" class="form-control" name="req_password2" value="<?php if (isset($_POST['req_password2'])) echo luna_htmlspecialchars($_POST['req_password2']); ?>" />
							</div>
						</div>
					</div>
				</div>
<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Email', 'luna') ?><?php if ($luna_config['o_regs_verify'] == '1'): ?><span class="help-block"><?php _e('Your password will be sent to this address, make sure it\'s valid', 'luna') ?></span><?php endif; ?></label>
					<div class="col-sm-9">
						<?php if ($luna_config['o_regs_verify'] == '1'): ?>
						<div class="row">
							<div class="col-sm-6">
						<?php endif; ?>
								<input type="text" class="form-control" name="req_email1" value="<?php if (isset($_POST['req_email1'])) echo luna_htmlspecialchars($_POST['req_email1']); ?>" maxlength="80" />
						<?php if ($luna_config['o_regs_verify'] == '1'): ?>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="req_email2" value="<?php if (isset($_POST['req_email2'])) echo luna_htmlspecialchars($_POST['req_email2']); ?>" maxlength="80" />
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>