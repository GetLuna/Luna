<?php

/*
 * Copyright (C) 2013-2016 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

?>
        <div class="container">
<?php
// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;

// Generate the feed links
if ($footer_style == 'index') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? __('RSS active thread feed', 'luna') : __('Atom active thread feed', 'luna');
	$feed_id = '';
} elseif ($footer_style == 'viewforum') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? __('RSS forum feed', 'luna') : __('Atom forum feed', 'luna');
	$feed_id = '&fid='.$forum_id;
} elseif ($footer_style == 'thread') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? __('RSS thread feed', 'luna') : __('Atom thread feed', 'luna');
	$feed_id = '&tid='.$id;
}

if ($luna_config['o_feed_type'] == 1)
	$feed_type = 'rss';
elseif ($luna_config['o_feed_type'] == 2)
	$feed_type = 'atom';

if (($luna_config['o_feed_type'] == 1 || $luna_config['o_feed_type'] == 2) && (isset($footer_style)))
	'<span><a href="extern.php?action=feed&type='.$feed_type.$feed_id.'">'.$feed_lang.'</a></span>'."\n";

$num_users = num_users_online();
$num_guests = num_guests_online();

?>
        </div>
        <footer>
            <?php if ($luna_config['o_board_statistics'] == 1): ?>
                <div class="stats container">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php total_users(); ?></h4>
                            <?php echo _n( 'User', 'Users', get_total_users(), 'luna' ) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php total_threads() ?></h4>
                            <?php echo _n( 'Thread', 'Threads', get_total_threads(), 'luna' ) ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php total_comments() ?></h4>
                            <?php echo _n('Comment', 'Comments', get_total_comments(), 'luna') ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php newest_user() ?></h4>
                            <?php _e('Newest user', 'luna') ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php echo forum_number_format($num_users) ?></h4>
                            <?php if ($luna_config['o_users_online']) { ?>
                            <div class="dropup">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <?php echo _n('User online', 'Users online', $num_users, 'luna') ?> <span class="fa fa-fw fa-angle-up"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php echo online_list() ?>
                                </ul>
                            </div>
                            <?php } else
                                echo _n('User online', 'Users online', $num_users, 'luna'); ?>
                        </div>
                        <div class="col-md-2 col-sm-4 col-xs-12 text-center">
                            <h4><?php echo forum_number_format($num_guests) ?></h4>
                            <?php echo _n('Guest online', 'Guests online', $num_guests, 'luna') ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="footer container">
                <span class="pull-left">
<?php
	if ($luna_config['o_show_copyright'] == '1') {
		if ($luna_config['o_copyright_type'] == '0')
			echo __('Copyright', 'luna').' &copy; '.date('Y').' &middot; '.$luna_config['o_board_title'];
		if ($luna_config['o_copyright_type'] == '1')
			echo $luna_config['o_custom_copyright'];
	}
?>
                <?php if ($luna_config['o_back_to_top'] == '1'): ?><a href="#" class="back-to-top"><i class="fa fa-fw fa-chevron-up"></i></a><?php endif; ?>
                </span>
                <span class="pull-right"><?php printf(__('Powered by %s', 'luna'), ' <a href="http://getluna.org/">Luna '.$luna_config['o_cur_version'].'</a>') ?></span>
            </div>
        </footer>
<?php

// End the transaction
$db->end_transaction();

// Display executed queries (if enabled)
if (defined('LUNA_DEBUG')) {
?>
<div class="container main">
    <div class="row">
        <div class="col-xs-12">
            <?php display_saved_queries(); ?>
        </div>
    </div>
</div>
<div class="footer container text-center">
<?php

// Display debug info (if enabled/defined)
if (defined('LUNA_DEBUG')) {
	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $luna_start);
	echo sprintf(__('Generated in %1$s seconds &middot; %2$s queries executed', 'luna'), $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage')) {
		echo ' &middot; '.sprintf(__('Memory usage: %1$s', 'luna'), file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf(__('(Peak: %1$s)', 'luna'), file_size(memory_get_peak_usage()));
	}
}
?>
</div>
<?php
}

// Close the db connection (and free up any result data)
$db->close();
?>
	</body>
</html>
