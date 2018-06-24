<?php

/*
 * Copyright (C) 2013-2018 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM')) {
    exit;
}

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Define $p if it's not set to avoid a PHP notice
$p = isset($p) ? $p : null;

$hour = date("g", time());
if ($luna_user['adapt_time'] == 1 || (($luna_user['adapt_time'] == 2) && (($hour <= 7) || ($hour >= 19)))) {
    $body_classes = 'night';
} else {
    $body_classes = 'normal';
}

if (__('Direction of language', 'luna') == 'rtl') {
    $body_classes .= ' rtl';
} else {
    $body_classes .= ' ltr';
}

if (file_exists('../img/header.png') || file_exists('../img/header.jpg')) {
    $body_classes .= ' bkg';
}

if (file_exists('../img/header.png')) {
    $body_classes .= ' bkg-png';
} elseif (file_exists('../img/header.jpg')) {
    $body_classes .= ' bkg-jpg';
}

// Check for new notifications
$noticount = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error('Unable to count notifications', __FILE__, __LINE__, $db->error());
$num_notifications = $db->result($noticount);

if ($luna_config['o_notification_flyout'] == 1) {
    if ($num_notifications == '0') {
        $notificon = '<span class="far fa-fw fa-circle"></span>';
        $ind_notification[] = '<a class="dropdown-item" href="../notifications.php">'.__('No new notifications', 'luna').'</a>';
    } else {
        $notificon = $num_notifications.' <span class="fas fa-fw fa-circle"></span>';

        $notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error('Unable to load notifications', __FILE__, __LINE__, $db->error());
        while ($cur_notifi = $db->fetch_assoc($notification_result)) {
            $notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
            $ind_notification[] = '<a class="dropdown-item" href="../notifications.php?notification='.$cur_notifi['id'].'"><span class="timestamp">'.$notifitime.'</span> <span class="fas fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].'</a>';
        }
    }

    $notifications = implode('', $ind_notification);
    $notification_menu_item = '
        <li class="nav-item dropdown dropdown-notifications">
            <a class="nav-link dropdown-toggle" href="#" id="notificationMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="'.(($num_notifications != 0) ? 'flash' : '').'">'.$notificon.'</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationMenu">
                <h6 class="dropdown-header">'.__('Notifications', 'luna').'</h6>
                <div class="dropdown-divider"></div>
                '.$notifications.'
                <div class="dropdown-divider"></div>
                <a class="dropdown-item float-right" href="../notifications.php">'.__('More', 'luna').' <i class="fas fa-fw fa-arrow-right"></i></a>
            </div>
        </li>';
} else {
    if ($num_notifications == '0') {
        $notificon = '<span class="far fa-fw fa-circle"></span>';
    } else {
        $notificon = $num_notifications.' <span class="fas fa-fw fa-circle"></span>';
    }
    
    $notification_menu_item ='
        <li class="nav-item active">
            <a class="nav-link'.(($num_notifications != 0) ? ' flash' : '').'" href="https://getluna.org/docs" class="'.$notificon.'"><span class="visible-xs-inline"> '.__('Notifications', 'luna').'</span></a>
        </li>';
}

if (LUNA_PAGE == 'index') { $page_title = __('Backstage', 'luna'); }
if (LUNA_PAGE == 'update') { $page_title = __('Update', 'luna'); }
if (LUNA_PAGE == 'about') { $page_title = __('About', 'luna'); }
if (LUNA_PAGE == 'board') { $page_title = __('Board', 'luna'); }
if (LUNA_PAGE == 'reports') { $page_title = __('Reports', 'luna'); }
if (LUNA_PAGE == 'censoring') { $page_title = __('Censoring', 'luna'); }
if (LUNA_PAGE == 'moderate') { $page_title = __('Moderate', 'luna'); }
if (LUNA_PAGE == 'users') { $page_title = __('Search', 'luna'); }
if (LUNA_PAGE == 'ranks') { $page_title = __('Ranks', 'luna'); }
if (LUNA_PAGE == 'groups') { $page_title = __('Groups', 'luna'); }
if (LUNA_PAGE == 'bans') { $page_title = __('Bans', 'luna'); }
if (LUNA_PAGE == 'settings') { $page_title = __('Settings', 'luna'); }
if (LUNA_PAGE == 'features') { $page_title = __('Features', 'luna'); }
if (LUNA_PAGE == 'appearance') { $page_title = __('Appearance', 'luna'); }
if (LUNA_PAGE == 'theme') { $page_title = __('Theme', 'luna'); }
if (LUNA_PAGE == 'emoji') { $page_title = __('Emoji', 'luna'); }
if (LUNA_PAGE == 'menu') { $page_title = __('Menu', 'luna'); }
if (LUNA_PAGE == 'maintenance') { $page_title = __('Maintenance', 'luna'); }
if (LUNA_PAGE == 'prune') { $page_title = __('Prune', 'luna'); }

$logout_url = '../login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_csrf_token(); 
$page_title = $page_title.' &middot '.__('Backstage', 'luna');

?>
<!DOCTYPE html>
<html class="<?php echo $body_classes ?> backstage accent-<?php echo $luna_user['accent'] ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <?php if ($config['o_use_cdn']) { ?>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
        <?php } else { ?>
            <link rel="stylesheet" type="text/css" href="../vendor/css/bootstrap4.min.css">
            <script src="../vendor/js/jquery.min.js"></script>
            <script src="../vendor/js/bootstrap4.min.js"></script>
        <?php } ?>
        <?php if ($luna_config['o_fontawesomepro'] == 0) { ?>
		    <link rel="stylesheet" href="../vendor/css/fontawesome-all.min.css">
        <?php } else { ?>
		    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome-all.min.css">
        <?php }?>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
        <?php
            if (__('Direction of language', 'luna') == 'rtl') {
                echo '<link rel="stylesheet" type="text/css" href="../vendor/css/bidirect.css" />';
            }
        ?>
        <link rel="icon" href="../img/favicon.png" />
		<meta name="ROBOTS" content="NOINDEX, FOLLOW" />
		<title><?php echo $page_title ?></title>
	</head>
	<body>
        <header>
            <nav class="navbar navbar-expand navbar-dark bg-primary">
                <div class="container">
                    <a class="navbar-brand" href="index.php">
                        <img src="../img/logo.png" /> <span class="brand">Luna</span>Backstage
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto"></ul>
                        <ul class="navbar-nav my-2 my-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="https://getluna.org/docs"><i class="fas fa-fw fa-book"></i><span class="d-none d-sm-inline"> <?php _e('Docs', 'luna') ?></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://forum.getluna.org"><i class="fas fa-fw fa-life-ring"></i><span class="d-none d-sm-inline"> <?php _e('Support', 'luna') ?></span></a>
                            </li>
                            <?php echo $notification_menu_item ?>
                            <li class="nav-item dropdown dropdown-user">
                                <a class="nav-link dropdown-toggle" href="../profile.php?id=<?php echo $luna_user['id'] ?>" id="profileMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo draw_user_avatar($luna_user['id'], true, 'avatar'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                                    <a class="dropdown-item" href="../profile.php?id=<?php echo $luna_user['id'] ?>"><i class="fas fa-fw fa-user"></i> <?php _e('Profile', 'luna') ?></a>
                                    <a class="dropdown-item" href="../inbox.php"><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Inbox', 'luna') ?></a>
                                    <a class="dropdown-item" href="../settings.php?id=<?php echo $luna_user['id'] ?>"><i class="fas fa-fw fa-cogs"></i> <?php _e('Settings', 'luna') ?></a>
                                    <a class="dropdown-item" href="<?php echo $logout_url; ?>"><i class="fas fa-fw fa-sign-out-alt"></i> <?php _e('Logout', 'luna') ?></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="jumbotron navigon primary">
                <div class="container">
                    <nav class="nav nav-tabs">
                        <a class="nav-item nav-link" href="../index.php">
                            <i class="fas fa-fw fa-arrow-left"></i>
                        </a>
                        <a class="nav-item nav-link <?php echo (LUNA_SECTION == 'backstage') ? 'active' : '' ?>" href="#backstage" aria-controls="backstage" role="tab" data-toggle="tab">
                            <i class="fas fa-fw fa-tachometer-alt"></i><span class="d-none d-md-inline"> <?php _e('Backstage', 'luna') ?></span>
                        </a>
                        <a class="nav-item nav-link <?php echo (LUNA_SECTION == 'content') ? 'active' : '' ?>" href="#content" aria-controls="content" role="tab" data-toggle="tab">
                            <i class="fas fa-fw fa-file"></i><span class="d-none d-md-inline"> <?php _e('Content', 'luna') ?></span>
                        </a>
                        <a class="nav-item nav-link <?php echo (LUNA_SECTION == 'users') ? 'active' : '' ?>" href="#users" aria-controls="users" role="tab" data-toggle="tab">
                            <i class="fas fa-fw fa-users"></i><span class="d-none d-md-inline"> <?php _e('Users', 'luna') ?></span>
                        </a>
                        <?php if ( $is_admin ) { ?>
                            <a class="nav-item nav-link <?php echo (LUNA_SECTION == 'settings') ? 'active' : '' ?>" href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                                <i class="fas fa-fw fa-cogs"></i><span class="d-none d-md-inline"> <?php _e('Settings', 'luna') ?></span>
                            </a>
                            <a class="nav-item nav-link <?php echo (LUNA_SECTION == 'maintenance') ? 'active' : '' ?>" href="#maintenance" aria-controls="maintenance" role="tab" data-toggle="tab">
                                <i class="fas fa-fw fa-coffee"></i><span class="d-none d-md-inline"> <?php _e('Maintenance', 'luna') ?></span>
                            </a>
                        <?php } ?>
                    </nav>
                </div>
            </div>
            <div class="jumbotron navigon secondary">
                <div class="container">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane <?php echo ( LUNA_SECTION == 'backstage' ) ? 'active' : '' ?>" id="backstage">
                            <nav class="nav nav-tabs">
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'index' ) ? 'active' : '' ?>" href="index.php"><i class="fas fa-fw fa-tachometer-alt"></i> <?php _e( 'Backstage', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'update' ) ? 'active' : '' ?>" href="update.php"><i class="fas fa-fw fa-upload"></i> <?php _e( 'Update', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'about' ) ? 'active' : '' ?>" href="about.php"><i class="fas fa-fw fa-moon"></i> <?php _e( 'About', 'luna' ) ?></a>
                            </nav>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo ( LUNA_SECTION == 'content' ) ? 'active' : '' ?>" id="content">
                            <nav class="nav nav-tabs">
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'board' ) ? 'active' : '' ?>" href="board.php"><i class="fas fa-fw fa-list"></i> <?php _e( 'Board', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'reports' ) ? 'active' : '' ?>" href="reports.php"><i class="fas fa-fw fa-flag"></i> <?php _e( 'Reports', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'censoring' ) ? 'active' : '' ?>" href="censoring.php"><i class="fas fa-fw fa-eye-slash"></i> <?php _e( 'Censoring', 'luna' )?></a>
                            </nav>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo ( LUNA_SECTION == 'users' ) ? 'active' : '' ?>" id="users">
                            <nav class="nav nav-tabs">
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'users' ) ? 'active' : '' ?>" href="users.php"><i class="fas fa-fw fa-search"></i> <?php _e( 'Search', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'ranks' ) ? 'active' : '' ?>" href="ranks.php"><i class="fas fa-fw fa-trophy"></i> <?php _e( 'Ranks', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'groups' ) ? 'active' : '' ?>" href="groups.php"><i class="fas fa-fw fa-users"></i> <?php _e( 'Groups', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'bans' ) ? 'active' : '' ?>" href="bans.php"><i class="fas fa-fw fa-ban"></i> <?php _e( 'Bans', 'luna' ) ?></a>
                            </nav>
                        </div>
                        <?php if ( $is_admin ) { ?>
                        <div role="tabpanel" class="tab-pane <?php echo ( LUNA_SECTION == 'settings' ) ? 'active' : ''     ?>" id="settings">
                            <nav class="nav nav-tabs">
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'settings' ) ? 'active' : '' ?>" href="settings.php"><i class="fas fa-fw fa-cogs"></i> <?php _e( 'Settings', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'features' ) ? 'active' : '' ?>" href="features.php"><i class="fas fa-fw fa-chalkboard"></i> <?php _e( 'Features', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'appearance' ) ? 'active' : '' ?>" href="appearance.php"><i class="fas fa-fw fa-paint-brush"></i> <?php _e( 'Appearance', 'luna' )?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'theme' ) ? 'active' : '' ?>" href="theme.php"><i class="fas fa-fw fa-pencil-alt"></i> <?php _e( 'Theme', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'emoji' ) ? 'active' : '' ?>" href="emoji.php"><i class="fas fa-fw fa-smile"></i> <?php _e( 'Emoji', 'luna' ) ?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'menu' ) ? 'active' : '' ?>" href="menu.php"><i class="fas fa-fw fa-bars"></i> <?php _e( 'Menu', 'luna' ) ?></a>
                            </nav>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo ( LUNA_SECTION == 'maintenance' ) ? 'active' : '' ?>" id="maintenance">
                            <nav class="nav nav-tabs">
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'maintenance' ) ? 'active' : '' ?>" href="maintenance.php"><i class="fas fa-fw fa-coffee"></i> <?php _e( 'Maintenance', 'luna' )?></a>
                                <a class="nav-item nav-link <?php echo ( LUNA_PAGE == 'prune' ) ? 'active' : '' ?>" href="prune.php"><i class="fas fa-fw fa-recycle"></i> <?php _e( 'Prune', 'luna' ) ?></a>
                            </nav>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </header>
        <div class="container main">
            <?php if ($luna_config['o_maintenance'] == '1') {?>
                <div class="row"><div class="col-xs-12"><div class="alert alert-danger"><i class="fas fa-fw fa-exclamation-triangle"></i> <?php _e('Luna is currently set in Maintenance Mode. Do not log off.', 'luna' )?></div></div></div>
            <?php } ?>
<?php
if (isset($required_fields)) {
    // Output JavaScript to validate form (make sure required fields are filled out)

    ?>
<script type="text/javascript">
/* <![CDATA[ */
function process_form(the_form) {
	var required_fields = {
<?php
// Output a JavaScript object with localised field names
    foreach ($required_fields as $elem_orig => $elem_trans) {
        echo $elem_orig.': '.addslashes(str_replace('&#160;', ' ', $elem_trans));
    }
    ?>
	if (document.all || document.getElementById) {
		for (var i = 0; i < the_form.length; ++i) {
			var elem = the_form.elements[i];
			if (elem.name && required_fields[elem.name] && !elem.value && elem.type && (/^(?:text(?:area)?|password|file)$/i.test(elem.type))) {
				alert('"' + required_fields[elem.name] + '" <?php _e('is a required field in this form.', 'luna') ?>');
				elem.focus();
				return false;
			}
		}
	}
	return true;
}
/* ]]> */
</script>
<?php

}

if (isset($page_head)) {
    echo implode('', $page_head);
}
