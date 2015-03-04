<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<form id="confirm_del_user" method="post" action="profile.php?id=<?php echo $id ?>">
	<fieldset>
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $lang['Confirmation info'].' <strong>'.luna_htmlspecialchars($username).'</strong>' ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="delete_user_comply" value="<?php echo $lang['Delete'] ?>" /></span></h3>
			</div>
			<div class="panel-body">
				<?php echo $lang['Delete warning'] ?>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="delete_posts" value="1" checked />
						<?php echo $lang['Delete all posts'] ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>
</form>
<?php

	require load_page('footer.php');