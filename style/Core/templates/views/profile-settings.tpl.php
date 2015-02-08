<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<link rel="stylesheet" type="text/css" href="include/css/accent.css">
<div class="col-sm-3 profile-nav">
<?php
	generate_profile_menu('settings');
?>
</div>
<div class="col-sm-9 col-profile">
<h2 class="profile-h2"><?php echo $lang['Settings'] ?></h2>
<form id="profile3" class="form-horizontal" method="post" action="profile.php?section=settings&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Settings'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Save'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Time zone'] ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[timezone]">
							<option value="-12"<?php if ($user['timezone'] == -12) echo ' selected="selected"' ?>><?php echo $lang['UTC-12:00'] ?></option>
							<option value="-11"<?php if ($user['timezone'] == -11) echo ' selected="selected"' ?>><?php echo $lang['UTC-11:00'] ?></option>
							<option value="-10"<?php if ($user['timezone'] == -10) echo ' selected="selected"' ?>><?php echo $lang['UTC-10:00'] ?></option>
							<option value="-9.5"<?php if ($user['timezone'] == -9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:30'] ?></option>
							<option value="-9"<?php if ($user['timezone'] == -9) echo ' selected="selected"' ?>><?php echo $lang['UTC-09:00'] ?></option>
							<option value="-8.5"<?php if ($user['timezone'] == -8.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:30'] ?></option>
							<option value="-8"<?php if ($user['timezone'] == -8) echo ' selected="selected"' ?>><?php echo $lang['UTC-08:00'] ?></option>
							<option value="-7"<?php if ($user['timezone'] == -7) echo ' selected="selected"' ?>><?php echo $lang['UTC-07:00'] ?></option>
							<option value="-6"<?php if ($user['timezone'] == -6) echo ' selected="selected"' ?>><?php echo $lang['UTC-06:00'] ?></option>
							<option value="-5"<?php if ($user['timezone'] == -5) echo ' selected="selected"' ?>><?php echo $lang['UTC-05:00'] ?></option>
							<option value="-4"<?php if ($user['timezone'] == -4) echo ' selected="selected"' ?>><?php echo $lang['UTC-04:00'] ?></option>
							<option value="-3.5"<?php if ($user['timezone'] == -3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:30'] ?></option>
							<option value="-3"<?php if ($user['timezone'] == -3) echo ' selected="selected"' ?>><?php echo $lang['UTC-03:00'] ?></option>
							<option value="-2"<?php if ($user['timezone'] == -2) echo ' selected="selected"' ?>><?php echo $lang['UTC-02:00'] ?></option>
							<option value="-1"<?php if ($user['timezone'] == -1) echo ' selected="selected"' ?>><?php echo $lang['UTC-01:00'] ?></option>
							<option value="0"<?php if ($user['timezone'] == 0) echo ' selected="selected"' ?>><?php echo $lang['UTC'] ?></option>
							<option value="1"<?php if ($user['timezone'] == 1) echo ' selected="selected"' ?>><?php echo $lang['UTC+01:00'] ?></option>
							<option value="2"<?php if ($user['timezone'] == 2) echo ' selected="selected"' ?>><?php echo $lang['UTC+02:00'] ?></option>
							<option value="3"<?php if ($user['timezone'] == 3) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:00'] ?></option>
							<option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+03:30'] ?></option>
							<option value="4"<?php if ($user['timezone'] == 4) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:00'] ?></option>
							<option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+04:30'] ?></option>
							<option value="5"<?php if ($user['timezone'] == 5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:00'] ?></option>
							<option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:30'] ?></option>
							<option value="5.75"<?php if ($user['timezone'] == 5.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+05:45'] ?></option>
							<option value="6"<?php if ($user['timezone'] == 6) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:00'] ?></option>
							<option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+06:30'] ?></option>
							<option value="7"<?php if ($user['timezone'] == 7) echo ' selected="selected"' ?>><?php echo $lang['UTC+07:00'] ?></option>
							<option value="8"<?php if ($user['timezone'] == 8) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:00'] ?></option>
							<option value="8.75"<?php if ($user['timezone'] == 8.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+08:45'] ?></option>
							<option value="9"<?php if ($user['timezone'] == 9) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:00'] ?></option>
							<option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+09:30'] ?></option>
							<option value="10"<?php if ($user['timezone'] == 10) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:00'] ?></option>
							<option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+10:30'] ?></option>
							<option value="11"<?php if ($user['timezone'] == 11) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:00'] ?></option>
							<option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected="selected"' ?>><?php echo $lang['UTC+11:30'] ?></option>
							<option value="12"<?php if ($user['timezone'] == 12) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:00'] ?></option>
							<option value="12.75"<?php if ($user['timezone'] == 12.75) echo ' selected="selected"' ?>><?php echo $lang['UTC+12:45'] ?></option>
							<option value="13"<?php if ($user['timezone'] == 13) echo ' selected="selected"' ?>><?php echo $lang['UTC+13:00'] ?></option>
							<option value="14"<?php if ($user['timezone'] == 14) echo ' selected="selected"' ?>><?php echo $lang['UTC+14:00'] ?></option>
						</select>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[dst]" value="1"<?php if ($user['dst'] == '1') echo ' checked="checked"' ?> />
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
					foreach (array_unique($forum_time_formats) as $key => $time_format)
					{
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['time_format'] == $key)
							echo ' selected="selected"';
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
					foreach (array_unique($forum_date_formats) as $key => $date_format)
					{
						echo "\t\t\t\t\t\t\t\t".'<option value="'.$key.'"';
						if ($user['date_format'] == $key)
							echo ' selected="selected"';
						echo '>'. format_time(time(), true, $date_format, null, false, true);
						if ($key == 0)
							echo ' ('.$lang['Default'].')';
						echo "</option>\n";
					}
?>
						</select>
					</div>
				</div>
<?php
$languages = forum_list_langs();

// Only display the language selection box if there's more than one language available
if (count($languages) > 1)
	{
?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Language'] ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[language]">
<?php
		foreach ($languages as $temp)
		{
			if ($user['language'] == $temp)
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
		}
?>
						</select>
					</div>
				</div>
<?php
	}
	$styles = forum_list_styles();

	// Only display the style selection box if there's more than one style available
	if (count($styles) == 1)
		echo "\t\t\t".'<div><input type="hidden" name="form[style]" value="'.$styles[0].'" /></div>'."\n";
	else if (count($styles) > 1)
	{
?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Style'] ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="form[style]">
<?php
		foreach ($styles as $temp)
		{
			if ($user['style'] == $temp)
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
			else
				echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
		}
?>
						</select>
					</div>
				</div>
<?php
	}
	if ($luna_user['is_admmod'])
	{
?>
				<div class="form-group accent-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Backstage Accent'] ?></label>
					<div class="col-sm-9">
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-primary backstage-accent bspink<?php if ($user['backstage_color'] == '#e861aa') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="pink" value="#e861aa"<?php if ($user['backstage_color'] == '#e861aa') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsmagenta<?php if ($user['backstage_color'] == '#d80073') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="magenta" value="#d80073"<?php if ($user['backstage_color'] == '#d80073') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bscrimson<?php if ($user['backstage_color'] == '#ac193d') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="crimson" value="#ac193d"<?php if ($user['backstage_color'] == '#ac193d') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsred<?php if ($user['backstage_color'] == '#e51400') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="red" value="#e51400"<?php if ($user['backstage_color'] == '#e51400') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsorange<?php if ($user['backstage_color'] == '#fa6800') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="orange" value="#fa6800"<?php if ($user['backstage_color'] == '#fa6800') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsamber<?php if ($user['backstage_color'] == '#f0a30a') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="amber" value="#f0a30a"<?php if ($user['backstage_color'] == '#f0a30a') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsshadeyellow<?php if ($user['backstage_color'] == '#e3c800') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="shadeyellow" value="#e3c800"<?php if ($user['backstage_color'] == '#e3c800') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsyellow<?php if ($user['backstage_color'] == '#ffe200') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="yellow" value="#ffe200"<?php if ($user['backstage_color'] == '#ffe200') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bslime<?php if ($user['backstage_color'] == '#98ca00') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="lime" value="#98ca00"<?php if ($user['backstage_color'] == '#98ca00') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsgreen<?php if ($user['backstage_color'] == '#60a917') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="green" value="#60a917"<?php if ($user['backstage_color'] == '#60a917') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsdarkgreen<?php if ($user['backstage_color'] == '#008a17') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="darkgreen" value="#008a17"<?php if ($user['backstage_color'] == '#008a17') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsdarkteal<?php if ($user['backstage_color'] == '#008299') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="darkteal" value="#008299"<?php if ($user['backstage_color'] == '#008299') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsteal<?php if ($user['backstage_color'] == '#03b3b2') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="teal" value="#03b3b2"<?php if ($user['backstage_color'] == '#03b3b2') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsbabyblue<?php if ($user['backstage_color'] == '#5db2ff') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="babyblue" value="#5db2ff"<?php if ($user['backstage_color'] == '#5db2ff') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsmbbblue<?php if ($user['backstage_color'] == '#14a3ff') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="mbbblue" value="#14a3ff"<?php if ($user['backstage_color'] == '#14a3ff') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsblue<?php if ($user['backstage_color'] == '#0072c6') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="blue" value="#0072c6"<?php if ($user['backstage_color'] == '#0072c6') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsdarkblue<?php if ($user['backstage_color'] == '#004b8b') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="darkblue" value="#004b8b"<?php if ($user['backstage_color'] == '#004b8b') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsindigo<?php if ($user['backstage_color'] == '#4617b4') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="indigo" value="#4617b4"<?php if ($user['backstage_color'] == '#4617b4') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsviolet<?php if ($user['backstage_color'] == '#8c0095') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="violet" value="#8c0095"<?php if ($user['backstage_color'] == '#8c0095') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bslightviolet<?php if ($user['backstage_color'] == '#c960d0') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="lightviolet" value="#c960d0"<?php if ($user['backstage_color'] == '#c960d0') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bslightgrey<?php if ($user['backstage_color'] == '#aaa') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="lightgrey" value="#aaa"<?php if ($user['backstage_color'] == '#aaa') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsgrey<?php if ($user['backstage_color'] == '#777') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="grey" value="#777"<?php if ($user['backstage_color'] == '#777') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsdarkgrey<?php if ($user['backstage_color'] == '#444') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="darkgrey" value="#444"<?php if ($user['backstage_color'] == '#444') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary backstage-accent bsblack<?php if ($user['backstage_color'] == '#111') echo ' active' ?>">
								<input type="radio" name="form[backstage_color]" id="black" value="#111"<?php if ($user['backstage_color'] == '#111') echo ' checked' ?>>
							</label>
						</div>
					</div>
				</div>
<?php
	}
	if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1' || $luna_config['o_signatures'] == '1' || $luna_config['o_avatars'] == '1' || ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1')): ?>
				<hr />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Post display'] ?></label>
					<div class="col-sm-9">
<?php if ($luna_config['o_smilies'] == '1' || $luna_config['o_smilies_sig'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show smilies'] ?>
							</label>
						</div>
<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show sigs'] ?>

							</label>
						</div>
<?php endif; if ($luna_config['o_avatars'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show avatars'] ?>
							</label>
						</div>
<?php endif; if ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Show images'] ?>
							</label>
						</div>
<?php endif; if ($luna_config['o_signatures'] == '1' && $luna_config['p_sig_bbcode'] == '1' && $luna_config['p_sig_img_tag'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked="checked"' ?> />
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
				<hr  />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Email setting info'] ?></label>
					<div class="col-sm-9">
						<div class="radio">
							<label>
								<input type="radio" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked="checked"' ?> />
								<?php echo $lang['Email setting 1'] ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Email setting 2'] ?>
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked="checked"' ?> />
								<?php echo $lang['Email setting 3'] ?>
							</label>
						</div>
					</div>
				</div>
<?php if ($luna_config['o_forum_subscriptions'] == '1' || $luna_config['o_topic_subscriptions'] == '1'): ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Subscriptions head'] ?></label>
					<div class="col-sm-9">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Notify full'] ?>
							</label>
						</div>
<?php if ($luna_config['o_topic_subscriptions'] == '1'): ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked="checked"' ?> />
								<?php echo $lang['Auto notify full'] ?>
							</label>
						</div>
<?php endif; ?>
					</div>
				</div>
<?php endif; ?>
			</fieldset>
		</div>
	</div>
</form>