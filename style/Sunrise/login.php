<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xs">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Login</h4>
			</div>
			<div class="modal-body">
				<form id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
					<fieldset>
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
						<div class="form-content">
							<input class="form-control" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php echo $lang['Username'] ?>" />
							<input class="form-control" type="password" name="req_password" tabindex="2" placeholder="<?php echo $lang['Password'] ?>" />
							<div class="control-group">
								<div class="controls remember">
									<label class="remember"><input type="checkbox" name="save_pass" value="1" tabindex="3" checked="checked" /> <?php echo $lang['Remember me'] ?></label>
								</div>
							</div>
							<input class="btn btn-primary btn-block" type="submit" name="login" value="<?php echo $lang['Login'] ?>" tabindex="4" />
							<hr />
							<a class="btn btn-primary btn-block" href="register.php">Register</a>
							<hr />
							<p class="actions"><?php if ($luna_config['o_regs_allow'] == '1') { ?><a href="register.php" tabindex="5"><?php echo $lang['Register'] ?></a> &middot; <?php }; ?><a href="login.php?action=forget" tabindex="6"><?php echo $lang['Forgotten pass'] ?></a></p>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</div>