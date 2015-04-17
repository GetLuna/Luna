<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * License: http://opensource.org/licenses/MIT MIT
 */

function luna_ajax_foo() {

	$foo = isset( $_POST['foo'] ) && ! empty( $_POST['foo'] ) ? $_POST['foo'] : null;
	if ( ! is_null( $foo ) ) {
		var_dump( $foo );
	}
}
