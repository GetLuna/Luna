<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

if ($luna_user['first_run'] == '0') {
?>
<div class="first-run panel panel-default hidden-xs hidden-sm">
	<div class="row first-run-content">
		<div class="col-md-4 col-sm-6 first-run-profile">
			<h3 class="first-run-title"><?php echo sprintf($lang['Hi there'], luna_htmlspecialchars($luna_user['username'])) ?></h3>
			<span class="first-run-avatar thumbnail">
				<?php echo $user_avatar ?>
			</span>
		</div>
		<?php if (!$luna_user['is_guest']) { ?>
		<div class="col-md-4 hidden-sm">
			<h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
			<p><?php echo $luna_config['o_first_run_message']; ?></p>
		</div>
		<div class="col-md-4 col-sm-6">
			<div class="list-group first-run-list">
				<a href="settings.php" class="list-group-item"><?php echo $lang['Extend profile'] ?></a>
				<a href="help.php" class="list-group-item"><?php echo $lang['Get help'] ?></a>
				<a href="search.php" class="list-group-item">Search the board</a>
				<a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php echo $lang['Do not show again'] ?></a>
			</div>
		</div>
		<?php } else { ?>
		<div class="col-md-4 hidden-sm">
			<h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
			<div class="list-group first-run-list">
				<a href="register.php" class="list-group-item"><?php echo $lang['Register'] ?></a>
				<a href="#" data-toggle="modal" data-target="#reqpass" class="list-group-item"><?php echo $lang['Forgotten pass'] ?></a>
			</div>
		</div>
		<div class="col-md-4 col-sm-6">
			<form class="form form-first-run" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
				<fieldset>
					<h3><?php echo $lang['Login'] ?></h3>
					<input type="hidden" name="form_sent" value="1" />
					<div class="first-run-login">
						<input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php echo $lang['Username'] ?>" />
						<input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php echo $lang['Password'] ?>" />
					</div>
					<label><input type="checkbox" name="save_pass" value="1" tabindex="3" checked /> <?php echo $lang['Remember me'] ?></label>
					<span class="pull-right">
						<input class="btn btn-primary btn-login" type="submit" name="login" value="<?php echo $lang['Login'] ?>" tabindex="4" />
					</span>
				</fieldset>
			</form>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
<div class="row index">
	<div class="col-sm-3 col-xs-12">
		<div class="list-group list-group-forum">
			<?php draw_mark_read('list-group-item', 'index') ?>
			<?php if ($id != '0' && $is_admmod) { ?>
				<a class="list-group-item" href="backstage/moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>"><span class="fa fa-fw fa-eye"></span> <?php echo $lang['Moderate forum'] ?></a>
			<?php } ?>
		</div>
	</div>
	<div class="col-sm-9">
		<div class="list-group list-group-forum">
			<?php draw_forum_list('viewforum.php', 'forum.php', 1, 'category.php', '</div></div>') ?>
		</div>
	</div>
</div>