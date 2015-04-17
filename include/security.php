<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * License: http://opensource.org/licenses/MIT MIT
 */

function luna_validate_ajax( $action = -1, $query_arg = false, $die = true ) {

	$nonce = '';

	if ( $query_arg && isset( $_REQUEST[ $query_arg ] ) )
		$nonce = $_REQUEST[ $query_arg ];
	elseif ( isset( $_REQUEST['_ajax_nonce'] ) )
		$nonce = $_REQUEST['_ajax_nonce'];
	elseif ( isset( $_REQUEST['_lunanonce'] ) )
		$nonce = $_REQUEST['_lunanonce'];
	elseif ( isset( $_REQUEST['_nonce'] ) )
		$nonce = $_REQUEST['_nonce'];

	$result = luna_verify_nonce( $nonce, $action );

	if ( $die && false == $result ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			die( -1 );
		else
			die( '-1' );
	}

	return $result;
}

function luna_nonce_tick() {

	return ceil( time() / 43200 );
}

function luna_create_nonce( $action = -1 ) {

	global $luna_user, $cookie_seed;

	$uid = $luna_user['id'];
	$i   = luna_nonce_tick();

	return substr( luna_hash( $i . '|' . $action . '|' . $uid . '|' . $cookie_seed, 'nonce' ), -12, 10 );
}

function luna_verify_nonce( $nonce, $action = -1 ) {

	global $luna_user, $cookie_seed;

	$nonce = (string) $nonce;
	$uid   = $luna_user['id'];

	if ( empty( $nonce ) ) {
		return false;
	}

	$i = luna_nonce_tick();

	// Nonce generated 0-12 hours ago
	$expected = substr( luna_hash( $i . '|' . $action . '|' . $uid . '|' . $cookie_seed, 'nonce' ), -12, 10 );
	if ( luna_compare_hashes( $expected, $nonce ) ) {
		return 1;
	}

	// Invalid nonce
	return false;
}

function luna_compare_hashes( $a, $b ) {

	$a_length = strlen( $a );
	if ( $a_length !== strlen( $b ) ) {
	    return false;
	}
	$result = 0;

	for ( $i = 0; $i < $a_length; $i++ ) {
		$result |= ord( $a[ $i ] ) ^ ord( $b[ $i ] );
	}

	return $result === 0;
}