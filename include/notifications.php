<?php

require LUNA_ROOT.'include/class/luna_notification.php';

/**
 * Create a notification.
 * 
 * @since    1.1
 * 
 * @param    int    $user Notification owner ID.
 * @param    int    $message Notification content.
 * @param    int    $link Notification link (optional).
 * @param    int    $icon Notification icon (optional).
 * 
 * @return   int    Notification ID on success, 0 on fail
 */
function new_notification($user, $link = '', $message, $icon = '') {

	return LunaNotification::add($user, $message, $link, $icon);
}

/**
 * Delete a specific notification.
 * 
 * @since    1.1
 * 
 * @param    int    Notification ID
 * 
 * @return   boolean
 */
function delete_notification($id) {

	return LunaNotification::delete($id);
}

/**
 * Mark a specific notification as viewed.
 * 
 * @since    1.1
 * 
 * @param    int    Notification ID
 * 
 * @return   boolean
 */
function read_notification($id) {

	return LunaNotification::read($id);
}

/**
 * Get a specific notification.
 * 
 * Simple standalone function to avoid calling the class.
 * 
 * @since    1.1
 * 
 * @param    mixed    Either a notification ID, a notification object or a notification array
 * 
 * @return   object|null    LunaNotification if available, null else
 */
function get_notification($notification = null) {

	$notif = null;

	if (is_null($notification) || empty($notification)) {
		return $notif;
	}

	if (is_a($notification, 'LunaNotification')) {
		$notif = $notification;
	} elseif (is_object($notification) || is_array($notification)) {
		$notif = new LunaNotification($notification);
	} else if (is_int($notification)) {
		$notif = LunaNotification::get_instance($notification);
	}

	return $notif;
}

/**
 * Delete a user's notifications.
 * 
 * If no User ID is set, falls back to current user. If none, drop.
 * 
 * Default behavior is to remove all notifications. Be SURE to specify WHICH ONES
 * shouldn't be deleted if you only want to remove viewed/unviewed ones!
 * 
 * @since    1.1
 * 
 * @param    int    $user_id Notification owner ID.
 * @param    int    $viewed Notification status.
 * 
 * @return   int    Number of affected rows
 */
function delete_user_notifications($user_id = null, $viewed = null) {

	global $db, $luna_user;

	if (empty($user_id) && !empty($luna_user['id'])) {
		$user_id = $luna_user['id'];
	}

	if (empty($user_id)) {
		return false;
	}

	$user_id = (int) $user_id;
	$user_id = $db->escape($user_id);

	if ($viewed === 1 || $viewed === 0) {
		$where = ' WHERE user_id='.$user_id.' AND viewed='.$viewed;
	} else {
		$where = ' WHERE user_id='.$user_id;
	}

	$query = 'DELETE FROM '.$db->prefix.'notifications'.$where;
	$result = $db->query($query) or error('Unable to delete user notifications', __FILE__, __LINE__, $db->error());

	return $db->affected_rows();
}

/**
 * Mark a user's notifications as viewed.
 * 
 * This affects all unviewed notifications.
 * 
 * If no User ID is set, falls back to current user. If none, drop.
 * 
 * @since    1.1
 * 
 * @param    int    $user_id Notification owner ID.
 * 
 * @return   int    Number of affected rows
 */
function set_user_notifications_viewed($user_id) {

	global $db, $luna_user;

	if (empty($user_id) && !empty($luna_user['id'])) {
		$user_id = $luna_user['id'];
	}

	if (empty($user_id)) {
		return false;
	}

	$user_id = (int) $user_id;
	$user_id = $db->escape($user_id);

	$result = $db->query('UPDATE '.$db->prefix.'notifications SET viewed=1 WHERE user_id='.$user_id.' AND viewed=0') or error('Unable to mark user notifications as viewed', __FILE__, __LINE__, $db->error());

	return $db->affected_rows();
}

/**
 * Retrieve a user's notifications.
 * 
 * If no User ID is set, falls back to current user. If none, drop.
 * 
 * If $count is set to true, the function will only count the notifications and
 * will not return their content.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * @param    int            $viewed Notification status.
 * @param    boolean        $count Shall we simply count the results?
 * @param    int|boolean    $limit Shall we limit the results?
 * 
 * @return   array|null     Retrieved notifications
 */
function get_user_notifications($user_id = null, $viewed = null, $count = false, $limit = false) {

	global $db, $luna_user;

	$notifications = null;

	if (empty($user_id) && !empty($luna_user['id'])) {
		$user_id = $luna_user['id'];
	}

	if (empty($user_id)) {
		return $notifications;
	}

	$user_id = (int) $user_id;
	$user_id = $db->escape($user_id);

	if ( true === $count ) {
		$select = 'SELECT COUNT(*) FROM '.$db->prefix.'notifications';
	} else {
		$select = 'SELECT * FROM '.$db->prefix.'notifications';
	}

	if ($viewed === 1 || $viewed === 0) {
		$where = ' WHERE user_id='.$user_id.' AND viewed='.$viewed;
	} else {
		$where = ' WHERE user_id='.$user_id;
	}

	$orderby = ' ORDER BY time DESC';

	if ( false !== $limit ) {
		$limit = (int) $limit;
		$limit = ' LIMIT '.$limit;
	} else {
		$limit = '';
	}

	$query = $select.$where.$orderby.$limit;

	$result = $db->query($query) or error('Unable to fetch user notifications', __FILE__, __LINE__, $db->error());

	if ( true === $count ) {
		$notifications = (int) $db->result($result);
	} else {
		while ($notif = $db->fetch_assoc($result)) {
			$notifications[] = new LunaNotification($notif);
		}

		if (empty($notifications)) {
			return null;
		}

		$notifications = array_map('get_notification', $notifications);
	}

	return $notifications;
}

/*
 * Following are a bunch of usefull shortcut functions
 */

/**
 * Get total number of notifications for a specific user.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * @param    int            $viewed Notification status.
 * 
 * @return   array|null     Retrieved notifications
 */
function get_user_notifications_total($user_id = null, $viewed = null) {

	return get_user_notifications($user_id, $viewed, $count = true);
}

/**
 * Get viewed notifications for a specific user.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * 
 * @return   array|null     Retrieved notifications
 */
function get_user_viewed_notifications($user_id = null) {

	return get_user_notifications($user_id, $viewed = 1);
}

/**
 * Get unviewed notifications for a specific user.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * 
 * @return   array|null     Retrieved notifications
 */
function get_user_unviewed_notifications($user_id = null) {

	return get_user_notifications($user_id, $viewed = 0);
}

/**
 * Check if a specific user has any notification available.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * @param    int            $viewed Notification status.
 * 
 * @return   array|null     Retrieved notifications
 */
function has_notifications($user_id = null, $viewed = null) {

	return (boolean) get_user_notifications_total($user_id, $viewed);
}

/**
 * Check if a specific user has any viewed notification.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * 
 * @return   array|null     Retrieved notifications
 */
function has_viewed_notifications($user_id = null) {

	return has_notifications($user_id, $viewed = 1);
}

/**
 * Check if a specific user has any unviewed notification.
 * 
 * @since    1.1
 * 
 * @param    int            $user_id Notification owner ID.
 * 
 * @return   array|null     Retrieved notifications
 */
function has_unviewed_notifications($user_id = null) {

	return has_notifications($user_id, $viewed = 0);
}
