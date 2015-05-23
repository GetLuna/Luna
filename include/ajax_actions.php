<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * License: http://opensource.org/licenses/MIT MIT
 */

function luna_ajax_heartbeat() {

	if (empty($_POST['_nonce']))
		luna_send_json_error();

	$response = array();
	if (false === LunaNonces::verify($_POST['_nonce'], 'heartbeat-nonce')) {
		$response['nonces_expired'] = true;
		luna_send_json($response);
	}

	global $luna_user;
	$notifications = get_user_notifications($luna_user['id'], $viewed = 0, $count = true);
	$messages      = pending_messages($luna_user['id']);

	// Send the current time according to the server
	$response['server_time'] = time();

	$response['notifications'] = intval($notifications);
	$response['messages']      = intval($messages);

	luna_send_json($response);
}

function luna_ajax_fetch_notifications() {

	if (empty($_POST['_nonce']))
		luna_send_json_error(-1);

	$response = array();
	if (false === LunaNonces::verify($_POST['_nonce'], 'fetch-notifications-nonce')) {
		$response['nonces_expired'] = true;
		luna_send_json($response);
	}

	global $luna_user;

	$notifications = get_user_unviewed_notifications($luna_user['id']);
	if (!empty($notifications)) {
		luna_send_json_success($notifications);
	}

	luna_send_json_error();
}

function luna_ajax_read_notification() {

	if (empty($_POST['_nonce']))
		luna_send_json_error(-1);

	$response = array();
	if (false === LunaNonces::verify($_POST['_nonce'], 'read-notification-nonce')) {
		$response['nonces_expired'] = true;
		luna_send_json($response);
	}

	$id = (isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : 0);
	if (!$id) {
		luna_send_json_error();
	}

	global $luna_user;

	read_notification($id, $luna_user['id']);

	luna_send_json_success();
}

function luna_ajax_trash_notification() {

	if (empty($_POST['_nonce']))
		luna_send_json_error(-1);

	$response = array();
	if (false === LunaNonces::verify($_POST['_nonce'], 'trash-notification-nonce')) {
		$response['nonces_expired'] = true;
		luna_send_json($response);
	}

	$id = (isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : 0);
	if (!$id) {
		luna_send_json_error();
	}

	global $luna_user;

	delete_notification($id, $luna_user['id']);

	luna_send_json_success();
}
