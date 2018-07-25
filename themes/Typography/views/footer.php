<?php

/*
 * Copyright (C) 2013-2018 Luna
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
	'<span><a href="extern.php?action=feed&type='.$feed_type.$feed_id.'">'.$feed_lang.'</a></span>';

$num_users = num_users_online();
$num_guests = num_guests_online();

?>
        </div>
        <div class="container">
            <footer>
                <div class="row">
                    <div class="col-lg-2 col-4">
                        <h6><?php _e( 'Users', 'luna' ) ?></h6>
                        <p><?php printf( _n( '%s user online', '%s users online', $num_users, 'luna' ), '<b>'.forum_number_format( $num_users ).'</b>' ) ?></p>
                        <p><?php printf( _n( '%s guest online', '%s guests online', $num_guests, 'luna' ), '<b>'.forum_number_format( $num_guests ).'</b>' ) ?></p>
                        <p><?php printf( __( 'Newest user: %s', 'luna' ), '<b>'.newest_user().'</b>' ) ?></p>
                    </div>
                    <div class="col-lg-2 col-4">
                        <h6><?php _e( 'Board', 'luna' ) ?></h6>
                        <p><?php printf( _n( '%s user', '%s users', get_total_users(), 'luna' ), '<b>'.total_users().'</b>' ) ?></p>
                        <p><?php printf( _n( '%s thread', '%s threads', get_total_threads(), 'luna' ), '<b>'.total_threads().'</b>' ) ?></p>
                        <p><?php printf( _n( '%s comment', '%s comments', get_total_comments(), 'luna' ), '<b>'.total_comments().'</b>' ) ?></p>
                    </div>
                    <div class="col-lg-2 col-4">
                    </div>
                    <div class="col-lg-6 col-12 text-right">
                        <p><?php printf(__('Powered by %s', 'luna'), ' <a href="http://getluna.org/">Luna '.$luna_config['o_cur_version'].'</a>') ?></p>
                        <p>
                            <?php
                                if ($luna_config['o_show_copyright'] == '1') {
                                    if ($luna_config['o_copyright_type'] == '0')
                                        echo __('Copyright', 'luna').' &copy; '.date('Y');
                                    if ($luna_config['o_copyright_type'] == '1')
                                        echo $luna_config['o_custom_copyright'];
                                }
                            ?>
                        </p>
                        <p class="brand"><span class="slogan"><?php echo $luna_config['o_board_slogan'] ?></span> <span class="name"><?php echo $luna_config['o_board_title'] ?></span></p>
                    </div>
                </div>
            </footer>
        </div>
<?php if (($luna_config['o_cookie_bar'] == 1) && ($luna_user['is_guest']) && (!isset($_COOKIE['LunaCookieBar']))) { ?>
		<div class="navbar navbar-inverse navbar-fixed-bottom cookie-bar">
			<div class="container">
				<p class="navbar-text"><?php _e('We use cookies to give you the best experience on this board.', 'luna') ?></p>
				<form class="navbar-form navbar-right">
					<div class="form-group">
						<div class="btn-toolbar"><a class="btn btn-link" href="<?php echo $luna_config['o_cookie_bar_url'] ?>"><?php _e('More info', 'luna') ?></a><a class="btn btn-default" href="index.php?action=disable_cookiebar"><?php _e('Don\'t show again', 'luna') ?></a></div>
					</div>
				</form>
			</div>
		</div>
<?php
}


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
