<?php

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if ($luna_user['is_admmod'])
	header("Location: index.php");

define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';

$redirect_url = check_url();

?>
<div class="well form-box">
	<h3 class="form-title"><?php echo $lang['Login'] ?></h3>
	<form id="login-form" method="post" action="../login.php?action=in" onsubmit="return">
		<input type="hidden" name="form_sent" value="1" />
		<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
		<div class="form-group">
			<input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php echo $lang['Username'] ?>" />
		</div>
		<div class="form-group">
			<input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php echo $lang['Password'] ?>" />
		</div>
		<div class="form-group">
			<label><input type="checkbox" name="save_pass" value="1" tabindex="3" checked /> <?php echo $lang['Remember me'] ?></label>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary btn-block" value="<?php echo $lang['Login'] ?>" />
		</div>
	</form>
</div>
<?php

require 'footer.php';