<?php check_url(); ?>
<div class="modal fade modal-form" id="login-form" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $lang['Login'] ?></h4>
			</div>
			<div class="modal-body">
				<form id="login-fr" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
					<fieldset>
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
						<input class="form-control" type="text" name="req_username" maxlength="25" tabindex="901" placeholder="<?php echo $lang['Username'] ?>" />
						<input class="form-control" type="password" name="req_password" tabindex="902" placeholder="<?php echo $lang['Password'] ?>" />
						<div class="control-group">
							<div class="controls remember">
								<label class="remember"><input type="checkbox" name="save_pass" value="1" tabindex="903" checked /> <?php echo $lang['Remember me'] ?></label>
							</div>
						</div>
						<input class="btn btn-primary btn-block" type="submit" name="login" value="<?php echo $lang['Login'] ?>" tabindex="904" />
						<a class="btn btn-default btn-block" href="register.php" tabindex="905">Register</a>
					</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<?php if ($luna_config['o_regs_allow'] == '1') { ?><a href="register.php" tabindex="906"><?php echo $lang['Register'] ?></a> &middot; <?php }; ?><a href="#" data-toggle="modal" data-target="#reqpass" data-dismiss="modal" tabindex="907"><?php echo $lang['Forgotten pass'] ?></a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-form" id="reqpass" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $lang['Request password'] ?></h4>
			</div>
			<div class="modal-body">
				<form id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
					<fieldset>
						<input type="hidden" name="form_sent" value="1" />
						<div class="input-group">
							<input class="form-control" type="text" name="req_email" placeholder="<?php echo $lang['Email'] ?>" />
							<span class="input-group-btn">
								<input class="btn btn-primary" type="submit" name="request_pass" value="<?php echo $lang['Submit'] ?>" />
							</span>
						</div>
					</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" data-toggle="modal" data-target="#login-form" data-dismiss="modal">Back</a>
			</div>
		</div>
	</div>
</div>