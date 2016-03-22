<?php
require ('header.php');

$body_classes = check_style_mode();
?>
<!DOCTYPE html>
<html class="<?php echo $body_classes ?>">
	<head>
		<?php load_meta(); ?>
		<link rel="stylesheet" type="text/css" href="vendor/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="vendor/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="vendor/css/prism.css">
		<?php load_css(); ?>
		<script src="vendor/js/jquery.min.js"></script>
		<script src="vendor/js/bootstrap.min.js"></script>
		<script src="vendor/js/prism.js"></script>
		<style>
		.emoji {
			font-size: <?php echo $luna_config['o_emoji_size'] ?>px;
		}
        <?php if ($luna_config['o_use_custom_css']) {
            echo $luna_config['o_custom_css'];
        } ?>
		</style>
	</head>
	<body>
		<?php if ($luna_user['is_guest']): require load_page('login.php'); endif; ?>
        <div id="header">
            <div class="navbar navbar-inverse navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-primary-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.php"><?php echo $menu_title ?></a>
                    </div>
                    <div class="navbar-primary-collapse navbar-collapse collapse">
                        <ul class="nav navbar-nav"><?php echo implode("\n\t\t\t\t", $links); ?></ul>
                        <ul class="nav navbar-nav navbar-right"><?php echo $usermenu; ?></ul>
                    </div>
                </div>
            </div>
        </div>