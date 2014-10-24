<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="col-sm-12">
    <div class="profile-card">
        <div class="profile-card-head">
            <div class="user-avatar thumbnail">
                <?php echo $avatar_field; ?>
            </div>
            <h2><?php echo $user_username; ?></h2>
            <h3><?php echo $user_usertitle; ?></h3>
        </div>
        <div class="profile-card-body">
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
</div>