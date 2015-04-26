<?php

/*
 * Copyright (C) 2013-2015 CaerCam
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

class LunaNotification {

	/**
	 * Unique identifier for a notification.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID of notification owner.
	 * 
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Notification message content.
	 * 
	 * @var string
	 */
	public $message = '';

	/**
	 * Notification icon
	 * 
	 * @var string
	 */
	public $icon = '';

	/**
	 * Notification custom link
	 * 
	 * @var string
	 */
	public $link = '';

	/**
	 * Notification date
	 * 
	 * @var int
	 */
	public $time = 0;

	/**
	 * Notification status
	 * 
	 * @var int
	 */
	public $viewed = 0;

	/**
	 * Constructor.
	 * 
	 * @since    1.1
	 *
	 * @param    object    $notification LunaNotification object.
	 */
	public function __construct($notification) {

		$this->init();

		if (is_object($notification)) {
			$notification = get_object_vars($notification);
		}

		foreach ($notification as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Initialize the class
	 * 
	 * @since    1.1
	 */
	private function init() {

		$this->time = time();
	}

	/**
	 * Create a new notification.
	 * 
	 * @since    1.1
	 * 
	 * @return   int|boolean    Notification ID or false
	 */
	private function create() {

		global $db;

		if (empty($this->user_id) || empty($this->message)) {
			return 0;
		}

		$user    = $db->escape($this->user_id);
		$message = $db->escape($this->message);
		$icon    = $db->escape($this->icon);
		$link    = $db->escape($this->link);
		$time    = $db->escape($this->time);

		$db->query('INSERT INTO '.$db->prefix.'notifications (user_id, message, icon, link, time) VALUES('.$user.', \''.$message.'\', \''.$icon.'\', \''.$link.'\', '.$time.')') or error('Unable to add new notification', __FILE__, __LINE__, $db->error());

		return $db->insert_id();
	}

	/**
	 * Delete a notification.
	 * 
	 * This method always returns true except when the notification does not
	 * exist.
	 * 
	 * @since    1.1
	 * 
	 * @return   int|boolean
	 */
	private function remove() {

		global $db;

		if (empty($this->id)) {
			return false;
		}

		$id = (int) $this->id;
		$id = $db->escape($id);

		$db->query('DELETE FROM '.$db->prefix.'notifications WHERE id='.$id) or error('Unable to remove notifications', __FILE__, __LINE__, $db->error());

		return $db->affected_rows();
	}

	/**
	 * Get a specific notification. Static method.
	 * 
	 * @since    1.1
	 * 
	 * @param    int    $id Notification ID.
	 * 
	 * @return   object|boolean    LunaNotification object or false
	 */
	public static function get_instance($id) {

		global $db;

		$id = (int) $id;
		if (!$id) {
			return false;
		}

		$r = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE id='.$id.' LIMIT 1') or error('Unable to fetch notification', __FILE__, __LINE__, $db->error());
		$notif = $db->fetch_assoc($r);

		return new LunaNotification($notif);
	}

	/**
	 * Get a specific notification. Static method.
	 * 
	 * @since    1.1
	 * 
	 * @param    int    $user Notification owner ID.
	 * @param    int    $message Notification content.
	 * @param    int    $link Notification link (optional).
	 * @param    int    $icon Notification icon (optional).
	 * 
	 * @return   int    Notification ID is success, 0 else
	 */
	public static function add($user, $message, $link = '', $icon = '') {

		$notification = array(
			'user_id' => (int) $user,
			'message' => luna_htmlspecialchars($message),
			'link'    => luna_htmlspecialchars($link),
			'icon'    => luna_htmlspecialchars($icon)
		);
		$notification = new LunaNotification($notification);

		return $notification->create();
	}

	/**
	 * Delete a specific notification. Static method.
	 * 
	 * @since    1.1
	 * 
	 * @param    int    $id Notification ID.
	 * 
	 * @return   boolean    Deletion status
	 */
	public static function delete($id) {

		$notification = LunaNotification::get_instance($id);
		if (!$notification) {
			return false;
		}

		return (boolean) $notification->remove();
	}
}
