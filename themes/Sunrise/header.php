<?php
require ('header.php');

$hour = date('G', time());

if ($luna_user['adapt_time'] == 1 || (($luna_user['adapt_time'] == 2) && (($hour <= 7) || ($hour >= 19))))
	$body_classes .= ' night';
else
	$body_classes .= ' normal';
?>
<!DOCTYPE html>
<html class="<?php echo $body_classes ?>">
	<head>
		<title><?php echo generate_page_title($page_title, $p) ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="include/css/prism.css" />
		<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="include/js/prism.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
// Allow childs
load_css();

if (!defined('FORUM_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";

// Required fields check
required_fields();
?>
		<style>
		.emoji {
			font-size: <?php echo $luna_config['o_emoji_size'] ?>px;
		}
		</style>
	</head>
	<body>
		<?php if ($luna_user['is_guest']): require load_page('login.php'); endif; ?>
		<div class="container container-main" id="main">
			<div id="header">
				<div class="navbar navbar-inverse navbar-static-top">
					<div class="container">
						<a class="navbar-brand" href="index.php"><?php echo $menu_title ?></a>
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-primary-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div class="navbar-primary-collapse navbar-collapse collapse">
							<ul class="nav navbar-nav hidden-lg hidden-md hidden-sm"><?php echo implode("\n\t\t\t\t", $links); ?></ul>
							<?php if ($luna_config['o_header_search']): ?>
							<form id="search" class="navbar-form navbar-left hidden-xs" method="get" action="search.php?section=simple">
								<fieldset>
									<input type="hidden" name="action" value="search" />
									<div class="form-group">
										<div class="input-group">
											<input class="form-control" type="text" name="keywords" placeholder="<?php _e('Search in posts', 'luna') ?>" maxlength="100" />
											<span class="input-group-btn">
												<button class="btn btn-default btn-search" type="submit" name="search" accesskey="s">
													<span class="fa fa-fw fa-search"></span>
												</button>
											</span>
										</div>
									</div>
								</fieldset>
							</form>
							<?php endif; ?>
							<ul class="nav navbar-nav navbar-right">
								<?php echo $usermenu; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="navbar navbar-inverse navbar-secondary navbar-static-top hidden-xs">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-secondary-collapse">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div class="navbar-secondary-collapse navbar-collapse collapse">
							<ul class="nav navbar-nav"><?php echo implode("\n\t\t\t\t", $links); ?></ul>
							<ul class="nav navbar-nav navbar-right">
								<li><?php draw_mark_read('', 'index') ?></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="container">