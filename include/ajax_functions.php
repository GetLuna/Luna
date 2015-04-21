<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * License: http://opensource.org/licenses/MIT MIT
 */

function luna_send_json($response) {

	@header('Content-Type: application/json; charset=utf-8');

	echo json_encode($response);

	die();
}

function luna_send_json_success($data = null) {

	$response = array('success' => true);

	if (isset($data))
		$response['data'] = $data;

	luna_send_json($response);
}

function luna_send_json_error($data = null) {

	$response = array('success' => false);

	if (isset($data))
		$response['data'] = $data;

	luna_send_json($response);
}
