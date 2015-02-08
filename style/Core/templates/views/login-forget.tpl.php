<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

// If there are errors, we display them
if (!empty($errors)) {
?>

<div id="posterror">
	<h3 class="form-heading form-errors"><?php echo $lang['New password errors'] ?></h3>
	<div class="error-info form-content">
		<p><?php echo $lang['New passworderrors info'] ?></p>
		<ul class="error-list">
<?php

	foreach ($errors as $cur_error)
		echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
		</ul>
	</div>
</div>

<?php } ?>

<form class="form" id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
	<h1 class="form-heading"><?php echo $lang['Request pass'] ?></h1>
	<fieldset>
		<div class="form-content">
			<input type="hidden" name="form_sent" value="1" />
			<div class="input-group">
				<input class="form-control" type="text" name="req_email" placeholder="<?php echo $lang['Email'] ?>" />
				<span class="input-group-btn">
					<input class="btn btn-primary" type="submit" name="request_pass" value="<?php echo $lang['Submit'] ?>" />
				</span>
			</div>
		</div>
	</fieldset>
</form>

<?php

	require FORUM_ROOT.'footer.php';