<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

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
<div class="col-sm-9">
<form id="profile-settings" method="post" action="settings.php?id=<?php echo $id ?>">
	<h2 class="profile-settings-head"><?php echo $lang['Settings'] ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="update"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Save'] ?></button></span></h2>
	<div role="tabpanel">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $lang['Profile'] ?></a></li>
			<li role="presentation"><a href="#personalize" aria-controls="personalize" role="tab" data-toggle="tab"><?php echo $lang['Personalize'] ?></a></li>
			<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab"><?php echo $lang['Message'] ?></a></li>
			<li role="presentation"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab"><?php echo $lang['Contact'] ?></a></li>
			<li role="presentation"><a href="#threads" aria-controls="threads" role="tab" data-toggle="tab"><?php echo $lang['Threads'] ?></a></li>
			<li role="presentation"><a href="#time" aria-controls="time" role="tab" data-toggle="tab"><?php echo $lang['Time'] ?></a></li>
			<?php if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')): ?>
			<li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab"><?php echo $lang['Admin'] ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="profile">
				<fieldset class="form-horizontal form-setting">
					<input type="hidden" name="form_sent" value="1" />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Username']?></label>
						<div class="col-sm-9">
							<?php echo $username_field ?>
						</div>
					</div>
					<?php if ($luna_user['id'] == $id || $luna_user['g_id'] == FORUM_ADMIN || ($user['g_moderator'] == '0' && $luna_user['g_mod_change_passwords'] == '1')): ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Password'] ?></label>
						<div class="col-sm-9">
							<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newpass"><?php echo $lang['Change pass'] ?></a>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Email'] ?></label>
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
						<label class="col-sm-3 control-label"><?php echo $lang['Realname'] ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[realname]" value="<?php echo luna_htmlspecialchars($user['realname']) ?>" maxlength="40" />
						</div>
					</div>
					<?php if (isset($title_field)): ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Title'] ?><span class="help-block"><?php echo $lang['Leave blank'] ?></span></label>
						<div class="col-sm-9">
							<?php echo $title_field ?>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Location'] ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[location]" value="<?php echo luna_htmlspecialchars($user['location']) ?>" maxlength="30" />
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Avatar'] ?><span class="help-block"><?php echo $lang['Avatar info'] ?></span></label>
						<div class="col-sm-9">
							<?php echo $avatar_user ?>
							<?php echo $avatar_field ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Signature'] ?><span class="help-block"><?php echo $lang['Signature info'] ?></span></label>
						<div class="col-sm-9">
							<textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea>
							<span class="help-block"><?php printf($lang['Sig max size'], forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Sig preview'] ?></label>
						<div class="col-sm-9">
							<div class="well">
								<?php echo $signature_preview ?>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane" id="personalize">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Color'] ?></label>
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
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Night mode</label>
						<div class="col-sm-9">
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="0"<?php if ($user['adapt_time'] == '0') echo ' checked' ?> />
									Never use night mode
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="1"<?php if ($user['adapt_time'] == '1') echo ' checked' ?> />
									Always use night mode
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[adapt_time]" value="2"<?php if ($user['adapt_time'] == '2') echo ' checked' ?> />
									Enable night mode automaticaly
								</label>
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label">Backstage<?php echo $lang['Color'] ?></label>
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
					</div>
<?php

$languages = forum_list_langs();

// Only display the language selection box if there's more than one language available
if (count($languages) > 1) {
?>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Language'] ?></label>
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
			<div role="tabpanel" class="tab-pane" id="email">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_config['o_pms_enabled'] == 1) { ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Inbox'] ?></label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[use_pm]" value="1"<?php if ($user['use_pm'] == '1') echo ' checked' ?> />
									<?php echo $lang['Use Inbox info'] ?>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Email setting info'] ?></label>
						<div class="col-sm-9">
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked' ?> />
									<?php echo $lang['Email setting 1'] ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked' ?> />
									<?php echo $lang['Email setting 2'] ?>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked' ?> />
									<?php echo $lang['Email setting 3'] ?>
								</label>
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Subscriptions head'] ?></label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked' ?> />
									<?php echo $lang['Notify full'] ?>
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked' ?> />
									<?php echo $lang['Auto notify full'] ?>
								</label>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane" id="contact">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Website'] ?></label>
						<div class="col-sm-9">
							<div class="input-group input">
								<span class="input-group-addon" id="website-addon"><span class="fa fa-fw fa-link"></span></span>
								<input id="website" type="text" class="form-control" name="form[url]" value="<?php echo luna_htmlspecialchars($user['url']) ?>" maxlength="80" aria-describedby="website-addon">
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Microsoft'] ?></label>
						<div class="col-sm-9">
							<div class="input-group input">
								<span class="input-group-addon" id="microsoft-addon"><span class="fa fa-fw fa-windows"></span></span>
								<input id="microsoft" type="text" class="form-control" name="form[msn]" value="<?php echo luna_htmlspecialchars($user['msn']) ?>" maxlength="50" aria-describedby="microsoft-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Facebook'] ?></label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon" id="facebook-addon"><span class="fa fa-fw fa-facebook-square"></span></span>
								<input id="facebook" type="text" class="form-control" name="form[facebook]" value="<?php echo luna_htmlspecialchars($user['facebook']) ?>" maxlength="50" aria-describedby="facebook-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Twitter'] ?></label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon" id="twitter-addon"><span class="fa fa-fw fa-twitter"></span></span>
								<input id="twitter" type="text" class="form-control" name="form[twitter]" value="<?php echo luna_htmlspecialchars($user['twitter']) ?>" maxlength="50" aria-describedby="twitter-addon">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Google+'] ?></label>
						<div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-addon" id="google-addon"><span class="fa fa-fw fa-google-plus"></span></span>
								<input id="google" type="text" class="form-control" name="form[google]" value="<?php echo luna_htmlspecialchars($user['google']) ?>" maxlength="50" aria-describedby="google-addon">
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane" id="threads">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1' || $luna_config['o_signatures'] == '1' || $luna_config['o_avatars'] == '1' || $luna_config['p_message_img_tag'] == '1'): ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $lang['Post display'] ?></label>
							<div class="col-sm-9">
								<?php if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked' ?> />
											<?php echo $lang['Show smilies'] ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked' ?> />
											<?php echo $lang['Show sigs'] ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_avatars'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked' ?> />
											<?php echo $lang['Show avatars'] ?>
										</label>
									</div>
								<?php endif; if ($luna_config['p_message_img_tag'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked' ?> />
											<?php echo $lang['Show images'] ?>
										</label>
									</div>
								<?php endif; if ($luna_config['o_signatures'] == '1' && $luna_config['p_sig_img_tag'] == '1'): ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked' ?> />
											<?php echo $lang['Show images sigs'] ?>
										</label>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<hr />
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Topics per page'] ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" maxlength="3" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Posts per page'] ?></label>
						<div class="col-sm-9">
							<input type="text" class="form-control" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" maxlength="3" />
						</div>
					</div>
				</fieldset>
			</div>
			<div role="tabpanel" class="tab-pane" id="time">
				<fieldset class="form-horizontal form-setting">
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Time zone'] ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[timezone]">
								<option value="-12"<?php if ($user['timezone'] == -12) echo ' selected' ?>><?php echo $lang['UTC-12:00'] ?></option>
								<option value="-11"<?php if ($user['timezone'] == -11) echo ' selected' ?>><?php echo $lang['UTC-11:00'] ?></option>
								<option value="-10"<?php if ($user['timezone'] == -10) echo ' selected' ?>><?php echo $lang['UTC-10:00'] ?></option>
								<option value="-9.5"<?php if ($user['timezone'] == -9.5) echo ' selected' ?>><?php echo $lang['UTC-09:30'] ?></option>
								<option value="-9"<?php if ($user['timezone'] == -9) echo ' selected' ?>><?php echo $lang['UTC-09:00'] ?></option>
								<option value="-8.5"<?php if ($user['timezone'] == -8.5) echo ' selected' ?>><?php echo $lang['UTC-08:30'] ?></option>
								<option value="-8"<?php if ($user['timezone'] == -8) echo ' selected' ?>><?php echo $lang['UTC-08:00'] ?></option>
								<option value="-7"<?php if ($user['timezone'] == -7) echo ' selected' ?>><?php echo $lang['UTC-07:00'] ?></option>
								<option value="-6"<?php if ($user['timezone'] == -6) echo ' selected' ?>><?php echo $lang['UTC-06:00'] ?></option>
								<option value="-5"<?php if ($user['timezone'] == -5) echo ' selected' ?>><?php echo $lang['UTC-05:00'] ?></option>
								<option value="-4"<?php if ($user['timezone'] == -4) echo ' selected' ?>><?php echo $lang['UTC-04:00'] ?></option>
								<option value="-3.5"<?php if ($user['timezone'] == -3.5) echo ' selected' ?>><?php echo $lang['UTC-03:30'] ?></option>
								<option value="-3"<?php if ($user['timezone'] == -3) echo ' selected' ?>><?php echo $lang['UTC-03:00'] ?></option>
								<option value="-2"<?php if ($user['timezone'] == -2) echo ' selected' ?>><?php echo $lang['UTC-02:00'] ?></option>
								<option value="-1"<?php if ($user['timezone'] == -1) echo ' selected' ?>><?php echo $lang['UTC-01:00'] ?></option>
								<option value="0"<?php if ($user['timezone'] == 0) echo ' selected' ?>><?php echo $lang['UTC'] ?></option>
								<option value="1"<?php if ($user['timezone'] == 1) echo ' selected' ?>><?php echo $lang['UTC+01:00'] ?></option>
								<option value="2"<?php if ($user['timezone'] == 2) echo ' selected' ?>><?php echo $lang['UTC+02:00'] ?></option>
								<option value="3"<?php if ($user['timezone'] == 3) echo ' selected' ?>><?php echo $lang['UTC+03:00'] ?></option>
								<option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected' ?>><?php echo $lang['UTC+03:30'] ?></option>
								<option value="4"<?php if ($user['timezone'] == 4) echo ' selected' ?>><?php echo $lang['UTC+04:00'] ?></option>
								<option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected' ?>><?php echo $lang['UTC+04:30'] ?></option>
								<option value="5"<?php if ($user['timezone'] == 5) echo ' selected' ?>><?php echo $lang['UTC+05:00'] ?></option>
								<option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected' ?>><?php echo $lang['UTC+05:30'] ?></option>
								<option value="5.75"<?php if ($user['timezone'] == 5.75) echo ' selected' ?>><?php echo $lang['UTC+05:45'] ?></option>
								<option value="6"<?php if ($user['timezone'] == 6) echo ' selected' ?>><?php echo $lang['UTC+06:00'] ?></option>
								<option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected' ?>><?php echo $lang['UTC+06:30'] ?></option>
								<option value="7"<?php if ($user['timezone'] == 7) echo ' selected' ?>><?php echo $lang['UTC+07:00'] ?></option>
								<option value="8"<?php if ($user['timezone'] == 8) echo ' selected' ?>><?php echo $lang['UTC+08:00'] ?></option>
								<option value="8.75"<?php if ($user['timezone'] == 8.75) echo ' selected' ?>><?php echo $lang['UTC+08:45'] ?></option>
								<option value="9"<?php if ($user['timezone'] == 9) echo ' selected' ?>><?php echo $lang['UTC+09:00'] ?></option>
								<option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected' ?>><?php echo $lang['UTC+09:30'] ?></option>
								<option value="10"<?php if ($user['timezone'] == 10) echo ' selected' ?>><?php echo $lang['UTC+10:00'] ?></option>
								<option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected' ?>><?php echo $lang['UTC+10:30'] ?></option>
								<option value="11"<?php if ($user['timezone'] == 11) echo ' selected' ?>><?php echo $lang['UTC+11:00'] ?></option>
								<option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected' ?>><?php echo $lang['UTC+11:30'] ?></option>
								<option value="12"<?php if ($user['timezone'] == 12) echo ' selected' ?>><?php echo $lang['UTC+12:00'] ?></option>
								<option value="12.75"<?php if ($user['timezone'] == 12.75) echo ' selected' ?>><?php echo $lang['UTC+12:45'] ?></option>
								<option value="13"<?php if ($user['timezone'] == 13) echo ' selected' ?>><?php echo $lang['UTC+13:00'] ?></option>
								<option value="14"<?php if ($user['timezone'] == 14) echo ' selected' ?>><?php echo $lang['UTC+14:00'] ?></option>
							</select>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="form[dst]" value="1"<?php if ($user['dst'] == '1') echo ' checked' ?> />
									<?php echo $lang['DST'] ?>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Time format'] ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[time_format]">
<?php
					foreach (array_unique($forum_time_formats) as $key => $time_format) {
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['time_format'] == $key)
							echo ' selected';
						echo '>'. format_time(time(), false, null, $time_format, true, true);
						if ($key == 0)
							echo ' ('.$lang['Default'].')';
						echo "</option>\n";
					}
?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Date format'] ?></label>
						<div class="col-sm-9">
							<select class="form-control" name="form[date_format]">
<?php
					foreach (array_unique($forum_date_formats) as $key => $date_format) {
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['date_format'] == $key)
							echo ' selected';
						echo '>'. format_time(time(), true, $date_format, null, false, true);
						if ($key == 0)
							echo ' ('.$lang['Default'].')';
						echo "</option>\n";
					}
?>
							</select>
						</div>
					</div>
				</fieldset>
			</div>
			<?php if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')): ?>
			<div role="tabpanel" class="tab-pane" id="admin">
				<fieldset class="form-horizontal form-setting">
					<?php if ($luna_user['g_moderator'] == '1') { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $lang['Delete ban legend'] ?></label>
							<div class="col-sm-9">
								<input class="btn btn-danger" type="submit" name="ban" value="<?php echo $lang['Ban user'] ?>" />
							</div>
						</div>
						<hr />
					<?php } else { if ($luna_user['id'] != $id) { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $lang['Group membership legend'] ?></label>
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
										<input type="submit" class="btn btn-primary" name="update_group_membership" value="<?php echo $lang['Save'] ?>" /> 
									</span> 
								</div>
							</div>
						</div>
						<hr />
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?php echo $lang['Delete ban legend'] ?></label>
						<div class="col-sm-9">
							<button type="submit" class="btn btn-danger" name="delete_user"><?php echo $lang['Delete user'] ?></button>
							<button type="submit" class="btn btn-danger" name="ban"><?php echo $lang['Ban user'] ?></button>
						</div>
					</div>
					<hr />
					<?php if ($user['g_moderator'] == '1' || $user['g_id'] == FORUM_ADMIN) { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $lang['Set mods legend'] ?><button type="submit" class="btn btn-primary" name="update_forums"><span class="fa fa-fw fa-check"></span> <?php echo $lang['Update forums'] ?></button></label>
							<div class="col-sm-9">
								<p><?php echo $lang['Moderator in info'] ?></p>
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
							<label class="col-sm-3 control-label"><?php echo $lang['Posts table'] ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="num_posts" value="<?php echo $user['num_posts'] ?>" maxlength="8" />
							</div>
						</div>
					<?php endif; if ($luna_user['is_admmod']): ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php echo $lang['Admin note'] ?></label>
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