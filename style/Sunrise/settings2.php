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
    generate_me_menu('settings');
?>
</div>
<div class="col-sm-9">
<h2>Settings</h2>
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
			<h3>Under construction</h3>
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