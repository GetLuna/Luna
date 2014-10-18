<?php

require ('header.php');

if ($luna_user['is_guest']) {
	$usermenu = '<li id="navregister"'.((FORUM_ACTIVE_PAGE == 'register') ? ' class="active"' : '').'><a href="register.php">'.$lang['Register'].'</a></li>
				 <li><a href="#" data-toggle="modal" data-target="#login">'.$lang['Login'].'</a></li>';
} else {
	$usermenu = '<li class="dropdown">
					<a href="#" class="dropdown-toggle avatar-item" data-toggle="dropdown">'.$user_avatar.' <span class="fa fa-angle-down"></a>
					<ul class="dropdown-menu">
						<li><a href="profile.php?id='.$luna_user['id'].'">'.$lang['Profile'].'</a></li>
						<li><a href="me.php?id='.$luna_user['id'].'">Me</a></li>
						<li><a href="help.php">'.$lang['Help'].'</a></li>
						<li class="divider"></li>
						<li><a href="login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">'.$lang['Logout'].'</a></li>
					</ul>
				   </li>';
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo generate_page_title($page_title, $p) ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" type="text/css" href="include/css/trent.css" />
		<link rel="stylesheet" type="text/css" href="include/css/font-awesome.css" />
		<link rel="stylesheet" type="text/css" href="style/Sunrise/style.css" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (!defined('FORUM_ALLOW_INDEX'))
	echo '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />'."\n";
?>
	</head>
	<body>
        <div id="header">
			<div class="navbar navbar-default navbar-static-top">
				<div class="nav-inner">
					<?php echo $menu_title ?>
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav"><?php echo implode("\n\t\t\t\t", $links); ?></ul>
						<ul class="nav navbar-nav navbar-right">
							<?php echo $usermenu; ?>
						</ul>
					</div>
				</div>
				<?php echo $announcement; ?>
			</div>
        </div>
        <div class="container">