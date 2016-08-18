<?php
require( '../config.php' );

// Create connection
$db = new mysqli( $db_host, $db_username, $db_password, $db_name );

// Get the configuration
$result = $db->query( 'SELECT * FROM luna_config' ) or error( 'Luna failed to load the configuration of the board' );

$config = array();
while( $cur_config_item = $result->fetch_assoc() ) {
    $config[$cur_config_item['conf_name']] = $cur_config_item['conf_value'];
}
?> 