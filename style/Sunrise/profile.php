<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

</div>
<div class="jumbotron profile-jumbotron">
	<div class="container">
        <div class="media">
            <a class="pull-left" href="#">
                <?php echo $avatar_field; ?>
            </a>
            <div class="media-body">
                <h2 class="media-heading"><?php echo $user_username; ?> <small><?php echo $user_usertitle; ?></small></h2>
            </div>
        </div>
		<span class="pull-right"><a href="me.php?id=<?php echo $luna_user['id'] ?>" class="btn btn-default"><span class="fa fa-cogs"></span></a></span>
	</div>
</div>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">About user</h3>
        </div>
        <div class="panel-body">
            <?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_personality)."\n" ?>
        </div>
    </div>
<?php if (!empty($user_messaging)): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Contact']; ?></h3>
        </div>
        <div class="panel-body">
            <p><?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_messaging)."\n" ?></p>
        </div>
    </div>
<?php
    endif;

    if ($luna_config['o_signatures'] == '1') {
        if (isset($parsed_signature)) {
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Signature']; ?></h3>
        </div>
        <div class="panel-body">
            <?php echo $user_signature ?>
        </div>
    </div>
<?php
        }
    }
?>