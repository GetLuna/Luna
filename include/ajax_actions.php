<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * License: http://opensource.org/licenses/MIT MIT
 */

function luna_ajax_heartbeat() {

	if ( empty( $_POST['_nonce'] ) )
		luna_send_json_error();

	$response = array();

	if ( false === luna_verify_nonce( $_POST['_nonce'], 'heartbeat-nonce' ) ) {
		$response['nonces_expired'] = true;
		luna_send_json( $response );
	}

	global $luna_user;
	$notifications = pending_notifications($luna_user['id']);
	$messages      = pending_messages($luna_user['id']);

	// Send the current time according to the server
	$response['server_time'] = time();

	$response['notifications'] = intval($notifications);
	$response['messages']      = intval($messages);

	luna_send_json( $response );
}

function luna_ajax_check_notifications() {

	$foo = isset( $_POST['foo'] ) && ! empty( $_POST['foo'] ) ? $_POST['foo'] : null;

	if ( ! is_null( $foo ) ) {
		$response = array( 'foo' => $foo );
		luna_send_json_success( $response );
	}

	luna_send_json_error();
}

function luna_ajax_fetch_notifications() {

	global $luna_user;

	$notifications = pending_notifications($luna_user['id'], false);
	if ( ! empty( $notifications ) ) {
		luna_send_json_success( $notifications );
	}

	luna_send_json_error();
}
