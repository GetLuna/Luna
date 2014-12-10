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
                <?php echo generate_avatar_markup($luna_user['id']) ?>
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
    generate_me_menu('notifications');
?>
</div>
<div class="col-sm-9 col-profile">
	<h2>Notifications</h2>
</div>