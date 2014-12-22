<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
</div>
<div class="jumbotron me-jumbotron">
	<div class="container">
        <div class="media">
            <a class="pull-left" href="#">
                <?php echo draw_user_avatar($luna_user['id'], 'avatar-me') ?>
            </a>
            <div class="media-body">
                <h2 class="media-heading"><?php echo $user['username']; ?></h2>
            </div>
        </div>
	</div>
</div>
<div class="container">
<div class="col-sm-3 profile-nav">
<?php
    load_me_nav('settings');
?>
</div>
<div class="col-sm-9">
<h2>Settings<span class="pull-right"><a class="btn btn-success"><span class="fa fa-check"></span> Save</a></h2>
<div role="tabpanel">
	<h3>Under construction</h3>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
		<li role="presentation"><a href="#personalize" aria-controls="personalize" role="tab" data-toggle="tab">Personalize</a></li>
		<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">Email</a></li>
		<li role="presentation"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">Contact</a></li>
		<li role="presentation"><a href="#threads" aria-controls="threads" role="tab" data-toggle="tab">Threads</a></li>
		<li role="presentation"><a href="#time" aria-controls="time" role="tab" data-toggle="tab">Time</a></li>
		<li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab">Admin</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="profile">
			<fieldset id="personality" class="form-horizontal form-setting">
				<div class="form-group">
					<label class="col-sm-3 control-label">Avatar<span class="help-block"><?php echo $lang['Avatar info'] ?></span></label>
					<div class="col-sm-9">
						<img src="img/avatars/placeholder.png" class="img-responsive visible-lg-inline" />
						<a href="#" class="btn btn-default">Upload</a>
					</div>
				</div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Signature<span class="help-block"><?php echo $lang['Signature info'] ?></span></label>
                    <div class="col-sm-9">
						<textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea>
						<span class="help-block"><?php printf($lang['Sig max size'], forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Sig preview'] ?></label>
                    <div class="col-sm-9">
						<div class="well">
							No signature set
						</div>
                    </div>
                </div>
			</fieldset>
		</div>
		<div role="tabpanel" class="tab-pane" id="personalize">
			<fieldset id="personality" class="form-horizontal form-setting">
				<div class="form-group">
					<label class="col-sm-3 control-label">Profile color</label>
					<div class="col-sm-9">
						<div class="btn-group accent-group" data-toggle="buttons">
							<label class="btn btn-primary color-accent accent-blue<?php if ($luna_user['color'] == '#14a3ff') echo ' active' ?>">
								<input type="radio" name="form[color]" id="blue" value="#14a3ff"<?php if ($luna_user['color'] == '#14a3ff') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-denim<?php if ($luna_user['color'] == '#2788cb') echo ' active' ?>">
								<input type="radio" name="form[color]" id="denim" value="#2788cb"<?php if ($luna_user['color'] == '#2788cb') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-luna<?php if ($luna_user['color'] == '#0d4382') echo ' active' ?>">
								<input type="radio" name="form[color]" id="luna" value="#0d4382"<?php if ($luna_user['color'] == '#0d4382') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-purple<?php if ($luna_user['color'] == '#c58be2') echo ' active' ?>">
								<input type="radio" name="form[color]" id="purple" value="#c58be2"<?php if ($luna_user['color'] == '#c58be2') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-green<?php if ($luna_user['color'] == '#99cc00') echo ' active' ?>">
								<input type="radio" name="form[color]" id="green" value="#99cc00"<?php if ($luna_user['color'] == '#99cc00') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-ao<?php if ($luna_user['color'] == '#047a36') echo ' active' ?>">
								<input type="radio" name="form[color]" id="ao" value="#047a36"<?php if ($luna_user['color'] == '#047a36') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-yellow<?php if ($luna_user['color'] == '#ffcd21') echo ' active' ?>">
								<input type="radio" name="form[color]" id="yellow" value="#ffcd21"<?php if ($luna_user['color'] == '#ffcd21') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-orange<?php if ($luna_user['color'] == '#ff7521') echo ' active' ?>">
								<input type="radio" name="form[color]" id="orange" value="#ff7521"<?php if ($luna_user['color'] == '#ff7521') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-red<?php if ($luna_user['color'] == '#ff4444') echo ' active' ?>">
								<input type="radio" name="form[color]" id="red" value="#ff4444"<?php if ($luna_user['color'] == '#ff4444') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-white<?php if ($luna_user['color'] == '#cccccc') echo ' active' ?>">
								<input type="radio" name="form[color]" id="white" value="#cccccc"<?php if ($luna_user['color'] == '#cccccc') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-grey<?php if ($luna_user['color'] == '#999999') echo ' active' ?>">
								<input type="radio" name="form[color]" id="grey" value="#999999"<?php if ($luna_user['color'] == '#999999') echo ' checked' ?>>
							</label>
							<label class="btn btn-primary color-accent accent-black<?php if ($luna_user['color'] == '#444444') echo ' active' ?>">
								<input type="radio" name="form[color]" id="black" value="#444444"<?php if ($luna_user['color'] == '#444444') echo ' checked' ?>>
							</label>
						</div>
                    </div>
                </div>
			</fieldset>
		</div>
		<div role="tabpanel" class="tab-pane" id="email">
			<fieldset id="personality" class="form-horizontal form-setting">
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
				<hr />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Subscriptions head'] ?></label>
                    <div class="col-sm-9">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Notify full'] ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked="checked"' ?> />
                                <?php echo $lang['Auto notify full'] ?>
                            </label>
                        </div>
                    </div>
                </div>
			</fieldset>
		</div>
		<div role="tabpanel" class="tab-pane" id="contact">
			<h3>Under construction</h3>
		</div>
		<div role="tabpanel" class="tab-pane" id="threads">
			<h3>Under construction</h3>
		</div>
		<div role="tabpanel" class="tab-pane" id="time">
			<h3>Under construction</h3>
		</div>
		<div role="tabpanel" class="tab-pane" id="admin">
			<h3>Under construction</h3>
		</div>
	</div>
</div>
</div>