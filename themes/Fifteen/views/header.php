<!DOCTYPE html>
<html class="<?php echo get_theme_mode() ?>">
	<head>
		<?php load_meta(); ?>
		<link rel="stylesheet" type="text/css" href="vendor/css/bootstrap4.min.css">
        <?php if ($luna_config['o_fontawesomepro'] == 0) { ?>
		    <link rel="stylesheet" href="vendor/css/fontawesome-all.min.css">
        <?php } else { ?>
		    <link rel="stylesheet" href="vendor/fontawesome/css/fontawesome-all.min.css">
        <?php }?>
		<link rel="stylesheet" type="text/css" href="vendor/css/prism.css">
		<?php load_css(); ?>
		<script src="vendor/js/jquery.min.js"></script>
		<script src="vendor/js/bootstrap4.min.js"></script>
		<script src="vendor/js/prism.js"></script>
        <?php
        if ($luna_config['o_use_custom_css']) {
            echo '<style>'.$luna_config['o_custom_css'].'</style>';
        }
        if (($luna_config['o_cookie_bar'] == 1) && ($luna_user['is_guest']) && (!isset($_COOKIE['LunaCookieBar']))) {
            echo '<style>';
			echo 'body { margin-bottom: 60px; }';
			echo '@media screen and (max-width: 767px) { body { margin-bottom: 80px; } }';
            echo '</style>';
        }
        ?>
		</style>
	</head>
	<body>
		<?php if ($luna_user['is_guest']): require load_page('login.php'); endif; ?>
        <div id="header">   
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <div class="container">
                    <a class="navbar-brand" href="index.php"><?php echo $menu_title ?></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon<?php echo (($num_new_pm != 0 || $num_notifications != 0)? ' flash' : '') ?>"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <?php echo implode('', $links); ?>
                        </ul>
                        <ul class="navbar-nav my-2 my-lg-0">
                            <?php echo $usermenu; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>