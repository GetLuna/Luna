<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="main container">
	<div class="row">
		<div class="col-12">
			<?php draw_error_panel($errors); ?>
			<form class="form-horizontal" id="register" method="post" action="register.php?action=register" onsubmit="this.register.disabled=true;if(process_form(this)){return true;}else{this.register.disabled=false;return false;}">
				<div class="title-block title-block-primary">
					<h2>
						<i class="fas fa-fw fa-user"></i> <?php _e('Register', 'luna') ?>
						<span class="float-right">
							<button type="submit" class="btn btn-light btn-light-primary" name="register"><span class="fas fa-fw fa-check"></span> <?php _e('Register', 'luna') ?></button>
						</span>
					</h2>
				</div>
				<div class="tab-content">
					<input type="hidden" name="form_sent" value="1" />
					<label class="required d-none"><?php _e('If you are human please leave this field blank!', 'luna') ?><input type="text" class="form-control" name="req_username" value="" maxlength="25" /></label>
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php _e('Username', 'luna') ?><span class="help-block"><?php _e('Enter a username between 2 and 25 characters long', 'luna') ?></span></label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="req_user" value="<?php if (isset($_POST['req_user'])) echo luna_htmlspecialchars($_POST['req_user']); ?>" maxlength="25" />
						</div>
					</div>
	<?php if ($luna_config['o_regs_verify'] == '0'): ?>
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php _e('Password', 'luna') ?><span class="help-block"><?php _e('Passwords must be at least 6 characters long and are case sensitive', 'luna') ?></span></label>
						<div class="col-md-9">
							<div class="row">
								<div class="col-md-6">
									<input id="password" type="password" class="form-control" name="req_password1" />
								</div>
								<div class="col-md-6">
									<input type="password" class="form-control" name="req_password2" />
								</div>
							</div>
						</div>
					</div>
	<?php endif; ?>
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php _e('Email', 'luna') ?><?php if ($luna_config['o_regs_verify'] == '1'): ?><span class="help-block"><?php _e('Your password will be sent to this address, make sure it\'s valid', 'luna') ?></span><?php endif; ?></label>
						<div class="col-md-9">
							<?php if ($luna_config['o_regs_verify'] == '1'): ?>
							<div class="row">
								<div class="col-md-6">
							<?php endif; ?>
									<input type="text" class="form-control" name="req_email1" value="<?php if (isset($_POST['req_email1'])) echo luna_htmlspecialchars($_POST['req_email1']); ?>" maxlength="80" />
							<?php if ($luna_config['o_regs_verify'] == '1'): ?>
								</div>
								<div class="col-md-6">
									<input type="text" class="form-control" name="req_email2" value="<?php if (isset($_POST['req_email2'])) echo luna_htmlspecialchars($_POST['req_email2']); ?>" maxlength="80" />
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>