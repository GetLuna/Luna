
							<ul id="navmenu" class="nav navbar-nav navbar-right">
<?php
if (!empty($items['backstage'])) {
	$item = $items['backstage'];
?>
								<li id="navbackstage"><a href="<?php echo $item['url']; ?>"><span class="fa fa-fw fa-tachometer"></span><span class="visible-xs-inline"> <?php echo $item['title']; ?></span></a></li>
<?php
}
if(!empty($items['notifications'])) {
	$item = $items['notifications'];
?>
								<li id="navnotification" class="dropdown">
									<a href="<?php echo $item['url']; ?>"<?php if ($item['flyout']) { ?> data-flyout="flyout" class="dropdown-toggle" data-toggle="dropdown"<?php } ?>><?php if (!$item['num']) { ?><span class="fa fa-fw fa-circle-o"></span><?php } else { ?><span id="notifications-number"><?php if ($item['num']) echo $item['num']; ?></span> <span class="fa fa-fw fa-circle"></span><?php } ?> <span class="visible-xs-inline"> <?php echo $item['title']; ?></span></a>
<?php if ($item['flyout']) { ?>
									<ul class="dropdown-menu notification-menu"></ul>
<?php } ?>
								</li>
<?php
}
if(!empty($items['inbox'])) {
	$item = $items['inbox'];
?>
								<li id="navinbox"><a href="<?php echo $item['url']; ?>"><?php if ($item['num']) echo $item['num']; ?> <span class="fa fa-fw fa-paper-plane-o"></span><span class="visible-xs-inline"> <?php echo $item['title']; ?></span></a></li>
<?php
}

if (!empty($items['guest'])) {
	$item = $items['guest'];
?>
								<li id="navregister"<?php ((FORUM_ACTIVE_PAGE == 'register') ? ' class="active"' : ''); ?>><a href="<?php echo $item['register']['url']; ?>"><?php echo $item['register']['title']; ?></a></li>
								<li><a href="<?php echo $item['login']['url']; ?>" data-toggle="modal" data-target="#login-form"><?php echo $item['login']['title']; ?></a></li>
<?php
} else if (!empty($items['user'])) {
	$item = $items['user'];
?>
								<li id="navprofile" class="dropdown">
									<a href="<?php echo $item['profile']['url']; ?>" class="dropdown-toggle avatar-item" data-toggle="dropdown"> <?php echo draw_user_avatar($luna_user['id'], true, 'avatar'); ?></a>
									<ul class="dropdown-menu">
										<li><a href="<?php echo $item['profile']['url']; ?>"><?php echo $item['profile']['title']; ?></a></li>
										<li><a href="<?php echo $item['settings']['url']; ?>"><?php echo $item['settings']['title']; ?></a></li>
										<li class="divider"></li>
										<li><a href="<?php echo $item['help']['url']; ?>"><?php echo $item['help']['title']; ?></a></li>
										<li class="divider"></li>
										<li><a href="<?php echo $item['logout']['url']; ?>"><?php echo $item['logout']['title']; ?></a></li>
									</ul>
								</li>
								<li id="navlogout" class="hide-if-js">
									<a href="<?php echo $item['logout']['url']; ?>" title="<?php echo $item['logout']['title']; ?>">
										<span class="fa fa-fw fa-sign-out"></span>
									</a>
								</li>
<?php } ?>
							</ul>
