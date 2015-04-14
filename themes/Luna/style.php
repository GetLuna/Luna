<?php

if ($luna_user['color_scheme'] < '11') {
	$primary_color = '#000;';
	$secondary_color = '#000';
	$tertiary_color = '#000';
} elseif ($luna_user['color_scheme'] == '11') { // Grey
	$primary_color = '#afafaf';
	$secondary_color = '#9e9e9e';
	$tertiary_color = '#8e8e8e';
} elseif ($luna_user['color_scheme'] == '12') { // Black
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

.navbar-secondary, .first-run-profile, .footer .copyright, .panel-default .panel-heading, .nav-tabs > li > a:hover, .thread-jumbotron, .jumbotron, .user-card-profile, .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
	background-color: <?php echo $secondary_color ?>;
}

.activity-header, .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, .navbar-default, .new-item .label-default {
	background-color: <?php echo $tertiary_color ?>;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, [disabled].btn-primary, .category-header {
	background-color: <?php echo $secondary_color ?>;
	border-color: <?php echo $tertiary_color ?>;
}

a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus, .list-group-forum .list-group-item.active.new-item {
	background-color: <?php echo $tertiary_color ?>;
	border-color: <?php echo $tertiary_color ?>;
}

.list-group-forum a.list-group-item:hover, .list-group-topic .list-group-item:hover {
	border-left-color: <?php echo $primary_color ?>;
}

a, a:focus, .pagination > li > a, .pagination > li > span {
	color: <?php echo $secondary_color ?>;
}

.nav > li > a, a.list-group-item:hover, a:hover, a:active {
	color: <?php echo $tertiary_color ?>;
}

.btn-primary {
	border-color: <?php echo $secondary_color ?>;
}
</style>