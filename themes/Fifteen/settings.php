<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

$sections = array( 'profile', 'personalize', 'email', 'contact', 'threads', 'time', 'admin' );
if ( isset( $_GET['section'] ) && in_array( $_GET['section'], $sections ) ) {
	$section = luna_htmlspecialchars( $_GET['section'] );
} else {
	$section = 'profile';
}

?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
	load_me_nav('settings');
?>
</div>
<div class="col-sm-9 profile">
<form id="profile-settings" method="post" action="settings.php?id=<?php echo $id ?>">
	<nav class="navbar navbar-default" role="navigation">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#settings-nav">
				<span class="sr-only"><?php _e('Toggle navigation', 'luna') ?></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="settings.php?id=<?php echo $id ?>" class="navbar-brand"><span class="fa fa-fw fa-cogs"></span> <?php _e('Settings', 'luna') ?></a>
		</div>
		<div class="collapse navbar-collapse" id="settings-nav">
			<ul class="navbar-form navbar-right">
				<div class="btn-compose">
					<button class="btn btn-default" type="submit" name="update"><span class="fa fa-fw fa-check"></span> <?php _e('Save', 'luna') ?></button>
				</div>
			</ul>
		</div>
	</nav>
	<div role="tabpanel">
		<ul id="profilenav" class="nav nav-tabs" role="tablist">
			<li role="presentation"<?php if ( 'profile' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=profile#profile" aria-controls="profile" role="tab" data-toggle="tab"><span class="fa fa-fw fa-user"></span><span class="hidden-xs"> <?php _e('Profile', 'luna') ?></span></a></li>
			<li role="presentation"<?php if ( 'personalize' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=personalize#personalize" aria-controls="personalize" role="tab" data-toggle="tab"><span class="fa fa-fw fa-paint-brush"></span><span class="hidden-xs"> <?php _e('Personalize', 'luna') ?></span></a></li>
			<li role="presentation"<?php if ( 'email' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=email#email" aria-controls="email" role="tab" data-toggle="tab"><span class="fa fa-fw fa-envelope-o"></span><span class="hidden-xs"> <?php _e('Message', 'luna') ?></span></a></li>
			<li role="presentation"<?php if ( 'contact' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=contact#contact" aria-controls="contact" role="tab" data-toggle="tab"><span class="fa fa-fw fa-share-alt"></span><span class="hidden-xs"> <?php _e('Contact', 'luna') ?></span></a></li>
			<li role="presentation"<?php if ( 'threads' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=threads#threads" aria-controls="threads" role="tab" data-toggle="tab"><span class="fa fa-fw fa-list"></span><span class="hidden-xs"> <?php _e('Threads', 'luna') ?></span></a></li>
			<li role="presentation"<?php if ( 'time' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=time#time" aria-controls="time" role="tab" data-toggle="tab"><span class="fa fa-fw fa-clock-o"></span><span class="hidden-xs"> <?php _e('Time', 'luna') ?></span></a></li>
			<?php if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')): ?>
				<li role="presentation"<?php if ( 'admin' === $section ) { ?> class="active"<?php } ?>><a href="settings.php?id=<?php echo $id ?>&amp;section=admin#admin" aria-controls="admin" role="tab" data-toggle="tab"><span class="fa fa-fw fa-dashboard"></span><span class="hidden-xs"> <?php _e('Admin', 'luna') ?></span></a></li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane<?php if ( 'profile' === $section ) { ?> active<?php } ?>" id="profile">
				<fieldset class="form-horizontal form-setting">
					<input type="hidden" name="form_sent" value="1" />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Username', 'luna')?></label>
						<div class="col-sm-9">
							<?php echo $username_field ?>
						</div>
					</div>
					<?php if ($luna_user['id'] == $id || $luna_user['g_id'] == FORUM_ADMIN || ($user['g_moderator'] == '0' && $luna_user['g_mod_change_passwords'] == '1')): ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Password', 'luna') ?></label>
						<div class="col-sm-9">
							<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newpass"><?php _e('Change password', 'luna') ?></a>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Email', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="input-group">
								<?php echo $email_field ?>
								<?php echo $email_button ?>
							</div>
						</div>
					</div>
					<hr />
					<input type="hidden" name="form_sent" value="1" />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Real name', 'luna') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[realname]" value="<?php echo luna_htmlspecialchars($user['realname']) ?>" maxlength="40" />
						</div>
					</div>
					<?php if (isset($title_field)): ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Title', 'luna') ?><span class="help-block"><?php _e('Leave blank to use default', 'luna') ?></span></label>
						<div class="col-sm-9">
							<?php echo $title_field ?>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Location', 'luna') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[location]" value="<?php echo luna_htmlspecialchars($user['location']) ?>" maxlength="30" />
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Avatar', 'luna') ?><span class="help-block"><?php _e('Upload an image to represent you', 'luna') ?></span></label>
						<div class="col-sm-9">
							<?php echo $avatar_user ?>
							<?php echo $avatar_field ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Signature', 'luna') ?><span class="help-block"><?php _e('Write a small piece to attach to every comment you make', 'luna') ?></span></label>
						<div class="col-sm-9">
							<textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea>
							<span class="help-block"><?php printf(__('Max length: %s characters / Max lines: %s', 'luna'), forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Signature preview', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="well">
								<?php echo $signature_preview ?>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane<?php if ( 'personalize' === $section ) { ?> active<?php } ?>" id="personalize">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group<?php if ($luna_config['o_allow_accent_color'] == '0') { echo ' hidden-xs hidden-sm hidden-md hidden-lg'; } ?>">
						<label class="col-sm-3 control-label"><?php _e('Color', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="btn-group accent-group" data-toggle="buttons">
<?php
		$accents = forum_list_accents('main');

		foreach ($accents as $temp) {
			if ($luna_user['color_scheme'] == $temp)
				echo '<label class="btn btn-primary color-accent accent-'.$temp.' active"><input type="radio" name="form[color_scheme]" id="'.$temp.'" value="'.$temp.'" checked></label>';
			else
				echo '<label class="btn btn-primary color-accent accent-'.$temp.'"> <input type="radio" name="form[color_scheme]" id="'.$temp.'" value="'.$temp.'"></label>';
		}
?>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[enforce_accent]" value="1"<?php if ($user['enforce_accent'] == '1') echo ' checked' ?> />
									<?php _e('Enforce the accent on the board.', 'luna') ?>
								</label>
							</div>
						</div>
						<hr />
					</div>
					<div class="form-group<?php if ($luna_config['o_allow_night_mode'] == '0') { echo ' hidden-xs hidden-sm hidden-md hidden-lg'; } ?>">
						<label class="col-sm-3 control-label"><?php _e('Night mode', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="0"<?php if ($user['adapt_time'] == '0') echo ' checked' ?> />
									<?php _e('Never use night mode.', 'luna') ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="1"<?php if ($user['adapt_time'] == '1') echo ' checked' ?> />
									<?php _e('Always use night mode.', 'luna') ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="2"<?php if ($user['adapt_time'] == '2') echo ' checked' ?> />
									<?php _e('Enable night mode automatically.', 'luna') ?>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group <?php if (!$luna_user['is_admmod']) { echo ' hidden-xs hidden-sm hidden-md hidden-lg'; } ?>">
						<label class="col-sm-3 control-label"><?php _e('Backstage accent', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="btn-group accent-group" data-toggle="buttons">
<?php
		$accents = forum_list_accents('back');

		foreach ($accents as $temp) {
			if ($luna_user['accent'] == $temp)
				echo '<label class="btn btn-primary color-accent accent-'.$temp.' active"><input type="radio" name="form[accent]" id="'.$temp.'" value="'.$temp.'" checked></label>';
			else
				echo '<label class="btn btn-primary color-accent accent-'.$temp.'"> <input type="radio" name="form[accent]" id="'.$temp.'" value="'.$temp.'"></label>';
		}
?>
							</div>
						</div>
						<hr />
					</div>
<?php

$languages = forum_list_langs();

// Only display the language selection box if there's more than one language available
if (count($languages) > 1) {
?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Language', 'luna') ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[language]">
<?php
		foreach ($languages as $temp) {
			if ($user['language'] == $temp)
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected>'.$temp.'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
		}
?>
							</select>
						</div>
					</div>
<?php } ?>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane<?php if ( 'email' === $section ) { ?> active<?php } ?>" id="email">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_config['o_pms_enabled'] == 1) { ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Inbox', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[use_pm]" value="1"<?php if ($user['use_pm'] == '1') echo ' checked' ?> />
									<?php _e('Allow users to send messages with Inbox.', 'luna') ?>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Email settings', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked' ?> />
									<?php _e('Display your email address.', 'luna') ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked' ?> />
									<?php _e('Hide your email address but allow form email.', 'luna') ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked' ?> />
									<?php _e('Hide your email address and disallow form email.', 'luna') ?>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Subscriptions', 'luna') ?></label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked' ?> />
									<?php _e('Include a plain text version of new comments in subscription notification emails.', 'luna') ?>
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked' ?> />
									<?php _e('Automatically subscribe to every thread you comment in.', 'luna') ?>
								</label>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane<?php if ( 'contact' === $section ) { ?> active<?php } ?>" id="contact">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group">
						<div class="col-sm-12">
							<div class="input-group input">
								<span class="input-group-addon" id="website-addon"><span class="fa fa-fw fa-link"></span> <?php _e('Website', 'luna') ?></span>
								<input id="website" type="text" class="form-control" name="form[url]" value="<?php echo luna_htmlspecialchars($user['url']) ?>" maxlength="80" aria-describedby="website-addon">
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group">
						<div class="col-sm-12">
							<div class="input-group input">
								<span class="input-group-addon" id="microsoft-addon"><span class="fa fa-fw fa-windows"></span> <?php _e('Microsoft Account', 'luna') ?></span>
								<input id="microsoft" type="text" class="form-control" name="form[msn]" value="<?php echo luna_htmlspecialchars($user['msn']) ?>" maxlength="50" aria-describedby="microsoft-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<div class="input-group">
								<span class="input-group-addon" id="facebook-addon"><span class="fa fa-fw fa-facebook-square"></span> <?php _e('Facebook', 'luna') ?></span>
								<input id="facebook" type="text" class="form-control" name="form[facebook]" value="<?php echo luna_htmlspecialchars($user['facebook']) ?>" maxlength="50" aria-describedby="facebook-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<div class="input-group">
								<span class="input-group-addon" id="twitter-addon"><span class="fa fa-fw fa-twitter"></span> <?php _e('Twitter', 'luna') ?></span>
								<input id="twitter" type="text" class="form-control" name="form[twitter]" value="<?php echo luna_htmlspecialchars($user['twitter']) ?>" maxlength="50" aria-describedby="twitter-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<div class="input-group">
								<span class="input-group-addon" id="google-addon"><span class="fa fa-fw fa-google-plus"></span> <?php _e('Google+', 'luna') ?></span>
								<input id="google" type="text" class="form-control" name="form[google]" value="<?php echo luna_htmlspecialchars($user['google']) ?>" maxlength="50" aria-describedby="google-addon">
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane<?php if ( 'threads' === $section ) { ?> active<?php } ?>" id="threads">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_config['o_smilies_sig'] == '1' || $luna_config['o_signatures'] == '1' || $luna_config['o_avatars'] == '1' || $luna_config['p_message_img_tag'] == '1'): ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Comment display', 'luna') ?></label>
							<div class="col-sm-9">
								<?php if ($luna_config['o_smilies_sig'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked' ?> />
											<?php _e('Show smilies as graphic icons.', 'luna') ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked' ?> />
											<?php _e('Show user signatures.', 'luna') ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_avatars'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked' ?> />
											<?php _e('Show user avatars in comments.', 'luna') ?>
										</label>
									</div>
								<?php endif; if ($luna_config['p_message_img_tag'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked' ?> />
											<?php _e('Show images in comments.', 'luna') ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_signatures'] == '1' && $luna_config['p_sig_img_tag'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked' ?> />
											<?php _e('Show images in user signatures.', 'luna') ?>
										</label>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Threads per page', 'luna') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" maxlength="3" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Comments per page', 'luna') ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" maxlength="3" />
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane<?php if ( 'time' === $section ) { ?> active<?php } ?>" id="time">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Time zone', 'luna') ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[timezone]">
								<option value="-12"<?php if ($user['timezone'] == -12) echo ' selected' ?>><?php _e('(UTC-12:00) International Date Line West', 'luna') ?></option>
								<option value="-11"<?php if ($user['timezone'] == -11) echo ' selected' ?>><?php _e('(UTC-11:00) Niue, Samoa', 'luna') ?></option>
								<option value="-10"<?php if ($user['timezone'] == -10) echo ' selected' ?>><?php _e('(UTC-10:00) Hawaii-Aleutian, Cook Island', 'luna') ?></option>
								<option value="-9.5"<?php if ($user['timezone'] == -9.5) echo ' selected' ?>><?php _e('(UTC-09:30) Marquesas Islands', 'luna') ?></option>
								<option value="-9"<?php if ($user['timezone'] == -9) echo ' selected' ?>><?php _e('(UTC-09:00) Alaska, Gambier Island', 'luna') ?></option>
								<option value="-8.5"<?php if ($user['timezone'] == -8.5) echo ' selected' ?>><?php _e('(UTC-08:30) Pitcairn Islands', 'luna') ?></option>
								<option value="-8"<?php if ($user['timezone'] == -8) echo ' selected' ?>><?php _e('(UTC-08:00) Pacific', 'luna') ?></option>
								<option value="-7"<?php if ($user['timezone'] == -7) echo ' selected' ?>><?php _e('(UTC-07:00) Mountain', 'luna') ?></option>
								<option value="-6"<?php if ($user['timezone'] == -6) echo ' selected' ?>><?php _e('(UTC-06:00) Central', 'luna') ?></option>
								<option value="-5"<?php if ($user['timezone'] == -5) echo ' selected' ?>><?php _e('(UTC-05:00) Eastern', 'luna') ?></option>
								<option value="-4"<?php if ($user['timezone'] == -4) echo ' selected' ?>><?php _e('(UTC-04:00) Atlantic', 'luna') ?></option>
								<option value="-3.5"<?php if ($user['timezone'] == -3.5) echo ' selected' ?>><?php _e('(UTC-03:30) Newfoundland', 'luna') ?></option>
								<option value="-3"<?php if ($user['timezone'] == -3) echo ' selected' ?>><?php _e('(UTC-03:00) Amazon, Central Greenland', 'luna') ?></option>
								<option value="-2"<?php if ($user['timezone'] == -2) echo ' selected' ?>><?php _e('(UTC-02:00) Mid-Atlantic', 'luna') ?></option>
								<option value="-1"<?php if ($user['timezone'] == -1) echo ' selected' ?>><?php _e('(UTC-01:00) Azores, Cape Verde, Eastern Greenland', 'luna') ?></option>
								<option value="0"<?php if ($user['timezone'] == 0) echo ' selected' ?>><?php _e('(UTC) Western European, Greenwich', 'luna') ?></option>
								<option value="1"<?php if ($user['timezone'] == 1) echo ' selected' ?>><?php _e('(UTC+01:00) Central European, West African', 'luna') ?></option>
								<option value="2"<?php if ($user['timezone'] == 2) echo ' selected' ?>><?php _e('(UTC+02:00) Eastern European, Central African', 'luna') ?></option>
								<option value="3"<?php if ($user['timezone'] == 3) echo ' selected' ?>><?php _e('(UTC+03:00) Eastern African', 'luna') ?></option>
								<option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected' ?>><?php _e('(UTC+03:30) Iran', 'luna') ?></option>
								<option value="4"<?php if ($user['timezone'] == 4) echo ' selected' ?>><?php _e('(UTC+04:00) Moscow, Gulf, Samara', 'luna') ?></option>
								<option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected' ?>><?php _e('(UTC+04:30) Afghanistan', 'luna') ?></option>
								<option value="5"<?php if ($user['timezone'] == 5) echo ' selected' ?>><?php _e('(UTC+05:00) Pakistan', 'luna') ?></option>
								<option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected' ?>><?php _e('(UTC+05:30) India, Sri Lanka', 'luna') ?></option>
								<option value="5.75"<?php if ($user['timezone'] == 5.75) echo ' selected' ?>><?php _e('(UTC+05:45) Nepal', 'luna') ?></option>
								<option value="6"<?php if ($user['timezone'] == 6) echo ' selected' ?>><?php _e('(UTC+06:00) Bangladesh, Bhutan, Yekaterinburg', 'luna') ?></option>
								<option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected' ?>><?php _e('(UTC+06:30) Cocos Islands, Myanmar', 'luna') ?></option>
								<option value="7"<?php if ($user['timezone'] == 7) echo ' selected' ?>><?php _e('(UTC+07:00) Indochina, Novosibirsk', 'luna') ?></option>
								<option value="8"<?php if ($user['timezone'] == 8) echo ' selected' ?>><?php _e('(UTC+08:00) Greater China, Australian Western, Krasnoyarsk', 'luna') ?></option>
								<option value="8.75"<?php if ($user['timezone'] == 8.75) echo ' selected' ?>><?php _e('(UTC+08:45) Southeastern Western Australia', 'luna') ?></option>
								<option value="9"<?php if ($user['timezone'] == 9) echo ' selected' ?>><?php _e('(UTC+09:00) Japan, Korea, Chita, Irkutsk', 'luna') ?></option>
								<option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected' ?>><?php _e('(UTC+09:30) Australian Central', 'luna') ?></option>
								<option value="10"<?php if ($user['timezone'] == 10) echo ' selected' ?>><?php _e('(UTC+10:00) Australian Eastern', 'luna') ?></option>
								<option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected' ?>><?php _e('(UTC+10:30) Lord Howe', 'luna') ?></option>
								<option value="11"<?php if ($user['timezone'] == 11) echo ' selected' ?>><?php _e('(UTC+11:00) Solomon Island, Vladivostok', 'luna') ?></option>
								<option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected' ?>><?php _e('(UTC+11:30) Norfolk Island', 'luna') ?></option>
								<option value="12"<?php if ($user['timezone'] == 12) echo ' selected' ?>><?php _e('(UTC+12:00) New Zealand, Fiji, Magadan', 'luna') ?></option>
								<option value="12.75"<?php if ($user['timezone'] == 12.75) echo ' selected' ?>><?php _e('(UTC+12:45) Chatham Islands', 'luna') ?></option>
								<option value="13"<?php if ($user['timezone'] == 13) echo ' selected' ?>><?php _e('(UTC+13:00) Tonga, Phoenix Islands, Kamchatka', 'luna') ?></option>
								<option value="14"<?php if ($user['timezone'] == 14) echo ' selected' ?>><?php _e('(UTC+14:00) Line Islands', 'luna') ?></option>
							</select>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[dst]" value="1"<?php if ($user['dst'] == '1') echo ' checked' ?> />
									<?php _e('Advance time by 1 hour for daylight saving.', 'luna') ?>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Time format', 'luna') ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[time_format]">
<?php
					foreach (array_unique($forum_time_formats) as $key => $time_format) {
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['time_format'] == $key)
							echo ' selected';
						echo '>'. format_time(time(), false, null, $time_format, true, true);
						if ($key == 0)
							echo ' ('.__('Default', 'luna').')';
						echo "</option>\n";
					}
?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Date format', 'luna') ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[date_format]">
<?php
					foreach (array_unique($forum_date_formats) as $key => $date_format) {
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['date_format'] == $key)
							echo ' selected';
						echo '>'. format_time(time(), true, $date_format, null, false, true);
						if ($key == 0)
							echo ' ('.__('Default', 'luna').')';
						echo "</option>\n";
					}
?>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<?php if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')): ?>
			<div role="tabpanel" class="tab-pane<?php if ( 'admin' === $section ) { ?> active<?php } ?>" id="admin">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_user['g_moderator'] == '1') { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Delete or ban user', 'luna') ?></label>
							<div class="col-sm-9">
								<input class="btn btn-danger" type="submit" name="ban" value="<?php _e('Ban user', 'luna') ?>" />
							</div>
						</div>
						<hr />
					<?php } else { if ($luna_user['id'] != $id) { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Choose user group', 'luna') ?></label>
							<div class="col-sm-9">
								<div class="input-group">
									<select id="group_id" class="form-control" name="group_id">
<?php

			$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

			while ($cur_group = $db->fetch_assoc($result)) {
				if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $luna_config['o_default_user_group'] && $user['g_id'] == ''))
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected>'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
				else
					echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
			}

?>
									</select> 
									<span class="input-group-btn"> 
										<input type="submit" class="btn btn-primary" name="update_group_membership" value="<?php _e('Save', 'luna') ?>" /> 
									</span> 
								</div>
							</div>
						</div>
						<hr />
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php _e('Delete or ban user', 'luna') ?></label>
						<div class="col-sm-9">
							<button type="submit" class="btn btn-danger" name="delete_user"><?php _e('Delete user', 'luna') ?></button>
							<button type="submit" class="btn btn-danger" name="ban"><?php _e('Ban user', 'luna') ?></button>
						</div>
					</div>
					<hr />
					<?php if ($user['g_moderator'] == '1' || $user['g_id'] == FORUM_ADMIN) { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Set moderator access', 'luna') ?><br /><button type="submit" class="btn btn-primary" name="update_forums"><span class="fa fa-fw fa-check"></span> <?php _e('Update forums', 'luna') ?></button></label>
							<div class="col-sm-9">
								<p><?php _e('Choose which forums this user should be allowed to moderate. Note: This only applies to moderators. Administrators always have full permissions in all forums.', 'luna') ?></p>
<?php

			$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

			$cur_category = 0;
			while ($cur_forum = $db->fetch_assoc($result)) {
				if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?

					echo "\t\t\t\t\t\t\t".'<div>'."\n\t\t\t\t\t\t\t\t".'<br /><strong>'.luna_htmlspecialchars($cur_forum['cat_name']).'</strong>'."\n\t\t\t\t\t\t\t\t".'</div>';
					$cur_category = $cur_forum['cid'];
				}

				$moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				echo "\n\t\t\t\t\t\t\t\t\t".'<input type="checkbox" name="moderator_in['.$cur_forum['fid'].']" value="1"'.((in_array($id, $moderators)) ? ' checked' : '').' /> '.luna_htmlspecialchars($cur_forum['forum_name']).'<br />'."\n";
			}

?>
								</div>
							</div>
							<hr />
						<?php } ?>
					<?php } ?>
					<?php if ($luna_user['g_id'] == FORUM_ADMIN): ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Comments', 'luna') ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="num_posts" value="<?php echo $user['num_posts'] ?>" maxlength="8" />
							</div>
						</div>
					<?php endif; if ($luna_user['is_admmod']): ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php _e('Admin note', 'luna') ?></label>
							<div class="col-sm-9">
								<input id="admin_note" type="text" class="form-control" name="admin_note" value="<?php echo luna_htmlspecialchars($user['admin_note']) ?>" maxlength="30" />
							</div>
						</div>
					<?php endif; ?>
				</fieldset>
			</div>
			<?php endif; ?>
		</div>
	</div>
</form>
</div>