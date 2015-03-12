<?php

if ($luna_user['color_scheme'] == '1') { // ModernBB
	$primary_color = '#057ed4';
	$secondary_color = '#088edb';
	$tertiary_color = '#0a9fe2';
	$fourth_color = '#10c1f0';
} else { // Luna fallback
	$primary_color = '#057ed4';
	$secondary_color = '#088edb';
	$tertiary_color = '#0a9fe2';
	$fourth_color = '#10c1f0';
}

?>
<style type="text/css">
.navbar-inverse, .footer, .alert-all, .modal-form .modal-header, .modal-form .modal-footer, .navbar-inverse .navbar-toggle .icon-bar, .btn-primary {
	background-color: <?php echo $primary_color ?>;
}

.navbar-secondary, .first-run-profile, .footer .copyright, .panel-default .panel-heading, .nav-tabs > li > a:hover, .thread-jumbotron, .user-card-profile, .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
	background-color: <?php echo $secondary_color ?>;
}

.activity-header, .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, .navbar-default {
	background-color: <?php echo $tertiary_color ?>;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, [disabled].btn-primary {
	background-color: <?php echo $secondary_color ?>;
	border-color: <?php echo $tertiary_color ?>;
}

a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
	background-color: <?php echo $tertiary_color ?>;
	border-color: <?php echo $tertiary_color ?>;
}

.list-group-forum a.list-group-item:hover, .list-group-topic .list-group-item:hover {
	border-left-color: <?php echo $primary_color ?>;
}

.nav > li > a, a.list-group-item:hover {
	color: <?php echo $tertiary_color ?>;
}

.btn-primary {
	border-color: <?php echo $secondary_color ?>;
}

<?php if ($luna_user['color_scheme'] == '10'): ?>
.navbar-inverse .navbar-brand:hover, .navbar-inverse .navbar-brand:focus, .first-run-title, .navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus, .navbar-inverse .navbar-nav > li > a, .navbar-inverse .navbar-brand, .footer, .footer a, .panel-default .panel-heading, .active.list-group-item, .active.list-group-item:hover, .active.list-group-item:focus, .alert-all, .jumbotron h2, .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus, .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus, .user-card-title, .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, [disabled].btn-primary, .nav > li > a, a.list-group-item:hover, .navbar-default .navbar-brand, .navbar-default .navbar-brand:focus, .navbar-default .navbar-brand:hover {
	color: #555;
}

.navbar-inverse .navbar-toggle, .navbar-inverse .navbar-toggle:hover, .navbar-inverse .navbar-toggle:focus {
	background-color: #555;
}

.btn-primary, .btn-primary.active, .btn-primary.focus, .btn-primary:active, .btn-primary:focus, .btn-primary:hover, .open > .dropdown-toggle.btn-primary {
	border-color: <?php echo $secondary_color ?>;
}

.navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus, .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
	text-shadow: 0px 0px 5px 1px rgba(0,0,0,0.2);
}
<?php endif; ?>

.emoji {
	font-size: <?php echo $luna_config['o_emoji_size'] ?>px;
}
</style>