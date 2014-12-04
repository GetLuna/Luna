<?php

if ($luna_user['color'] == '#33b5e5') {
	$primary_color = '#2788cb';
	$secondary_color = '#1a79bc';
	$tertiary_color = '#136cab';
} elseif ($luna_user['color'] == '#c58be2') {
	$primary_color = '#b679d4';
	$secondary_color = '#a268bf';
	$tertiary_color = '#8b54a7';
} elseif ($luna_user['color'] == '#99cc00') {
	$primary_color = '#08893e';
	$secondary_color = '#047a36';
	$tertiary_color = '#016a2d';
} elseif ($luna_user['color'] == '#ffcd21') {
	$primary_color = '#ffcb1a';
	$secondary_color = '#ffb61a';
	$tertiary_color = '#ffa11a';
} elseif ($luna_user['color'] == '#ff4444') {
	$primary_color = '#ff4444';
	$secondary_color = '#e63838';
	$tertiary_color = '#d42f2f';
} elseif ($luna_user['color'] == '#0d4382') {
	$primary_color = '#145198';
	$secondary_color = '#0d4382';
	$tertiary_color = '#0b3c75';
} elseif ($luna_user['color'] == '#cccccc') {
	$primary_color = '#afafaf';
	$secondary_color = '#9e9e9e';
	$tertiary_color = '#8e8e8e';
}

?>
<style type="text/css">
.navbar-inverse, footer {
	background-color: <?php echo $primary_color ?>;
}

.navbar-secondary, .first-run-profile, footer .copyright, .panel-default .panel-heading, .nav-tabs > li > a:hover {
	background-color: <?php echo $secondary_color ?>;
}

a.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
	background-color: <?php echo $tertiary_color ?>;
	border-color: <?php echo $tertiary_color ?>;
}

.activity-header, .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
	background-color: <?php echo $tertiary_color ?>;
}

.list-group-forum a.list-group-item:hover {
	border-left-color: <?php echo $primary_color ?>;
}
</style>