<?php

if ($luna_user['color_scheme'] == '1') { // ModernBB
	$primary_color = '#14a3ff';
	$secondary_color = '#0b95ed';
	$tertiary_color = '#0589de';
} elseif ($luna_user['color_scheme'] == '2') { // Blue
	$primary_color = '#2788cb';
	$secondary_color = '#1a79bc';
	$tertiary_color = '#136cab';
} elseif ($luna_user['color_scheme'] == '3') { // Luna
	$primary_color = '#145198';
	$secondary_color = '#0d4382';
	$tertiary_color = '#0b3c75';
} elseif ($luna_user['color_scheme'] == '4') { // Purple
	$primary_color = '#b679d4';
	$secondary_color = '#a268bf';
	$tertiary_color = '#8b54a7';
} elseif ($luna_user['color_scheme'] == '5') { // Lime
	$primary_color = '#8bb805';
	$secondary_color = '#7ea703';
	$tertiary_color = '#779e01';
} elseif ($luna_user['color_scheme'] == '6') { // Ao
	$primary_color = '#08893e';
	$secondary_color = '#047a36';
	$tertiary_color = '#016a2d';
} elseif ($luna_user['color_scheme'] == '7') { // Yellow
	$primary_color = '#ffcb1a';
	$secondary_color = '#ffb61a';
	$tertiary_color = '#ffa11a';
} elseif ($luna_user['color_scheme'] == '8') { // Orange
	$primary_color = '#ff7521';
	$secondary_color = '#ff5a21';
	$tertiary_color = '#ff4021';
} elseif ($luna_user['color_scheme'] == '9') { // Red
	$primary_color = '#ff4444';
	$secondary_color = '#e63838';
	$tertiary_color = '#d42f2f';
} elseif ($luna_user['color_scheme'] == '10') { // White
	$primary_color = '#eeeeee';
	$secondary_color = '#dddddd';
	$tertiary_color = '#cccccc';
} elseif ($luna_user['color_scheme'] == '11') { // Grey
	$primary_color = '#afafaf';
	$secondary_color = '#9e9e9e';
	$tertiary_color = '#8e8e8e';
} elseif ($luna_user['color_scheme'] == '12') { // darkgrey
	$primary_color = '#555555';
	$secondary_color = '#444444';
	$tertiary_color = '#333333';
} else { // Luna fallback
	$primary_color = '#145198';
	$secondary_color = '#0d4382';
	$tertiary_color = '#0b3c75';
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

.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, [disabled].btn-primary, .category-header {
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
.navbar-inverse .navbar-brand:hover, .navbar-inverse .navbar-brand:focus, .first-run-title, .navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus, .navbar-inverse .navbar-nav > li > a, .navbar-inverse .navbar-brand, .footer, .footer a, .panel-default .panel-heading, .active.list-group-item, .active.list-group-item:hover, .active.list-group-item:focus, .alert-all, .jumbotron h2, .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus, .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus, .user-card-title, .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, [disabled].btn-primary, .nav > li > a, a.list-group-item:hover, .navbar-default .navbar-brand, .navbar-default .navbar-brand:focus, .navbar-default .navbar-brand:hover, .category-header {
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