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
			<h3>Under construction</h3>
		</div>
		<div role="tabpanel" class="tab-pane" id="email">
			<h3>Under construction</h3>
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