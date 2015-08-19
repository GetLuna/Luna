<?php

/**
 * Luna Nonces Utility Class.
 * 
 * Handles the generation and validity check of nonces.
 * 
 * @package   Luna
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam
 */
class LunaNonces {

	/**
	 * Action name
	 * 
	 * @since    1.1
	 * @var      string
	 */
	protected $action = -1;

	/**
	 * 
	 * 
	 * @since    1.1
	 * @var      string
	 */
	protected $nonce = null;

	/**
	 * Period in which the nonce is considered valid.
	 * 
	 * 43200 seconds = 12 hours
	 * 
	 * @since    1.1
	 * @var      string
	 */
	private $ticker = 43200;

	/**
	 * Calculated tick value
	 * 
	 * @since    1.1
	 * @var      string
	 */
	private $tick = null;

	/**
	 * Current user ID
	 * 
	 * @since    1.1
	 * @var      string
	 */
	private $user = null;

	/**
	 * Nonce seed to harden hash
	 * 
	 * @since    1.1
	 * @var      string
	 */
	private $seed = null;

	/**
	 * Class constructor.
	 * 
	 * @since    1.1
	 * 
	 * @param    string    $action Action name (optional)
	 */
	public function __construct($action = 1) {

		$this->action = $action;

		$this->init();
	}

	/**
	 * Class initialisation.
	 * 
	 * Set the required values.
	 * 
	 * @since    1.1
	 */
	private function init() {

		$this->tick = $this->set_ticker();
		$this->user = $this->set_current_user_id();
		$this->seed = $this->set_cookie_seed();
	}

	/**
	 * Create the tick.
	 * 
	 * @since    1.1
	 * 
	 * @return   int    Tick
	 */
	private function set_ticker() {

		return ceil(time() / $this->ticker);
	}

	/**
	 * Set the current User ID.
	 * 
	 * If no user is logged in (guest nonce) ID is set to -1.
	 * 
	 * @since    1.1
	 * 
	 * @return   string    Current User ID if any, -1 else.
	 */
	private function set_current_user_id() {

		global $luna_user;

		$user_id = -1;
		if (!$luna_user['is_guest']) {
			$user_id = intval($luna_user['id']);
		}

		return $user_id;
	}

	/**
	 * Set the nonce seed.
	 * 
	 * We currently use the forum's cookie_seed value, but this should
	 * be update to use a different, more complexe string value.
	 * 
	 * @since    1.1
	 * 
	 * @return   string    Nonce seed
	 */
	private function set_cookie_seed() {

		global $cookie_seed;

		if (!is_null($cookie_seed)) {
			$seed = $cookie_seed;
		} else {
			throw new Exception('Error: seed not found. Building nonces is insecure without seed.');
		}

		return $seed;
	}

	/**
	 * Create a nonce.
	 * 
	 * Build a secret string with previously set values, hash it and return
	 * a truncated, 12 chars long string.
	 * 
	 * @since    1.1
	 * 
	 * @return   string    Nonce value
	 */
	private function _create() {

		$secret = $this->tick.'|'.$this->action.'|'.$this->user.'|'.$this->seed;

		$this->nonce = substr(luna_hash($secret, 'nonce'), -12, 12);

		return $this->nonce;
	}

	/**
	 * Output a nonce field.
	 * 
	 * Create a HTML <INPUT> field to store the nonce. If no name is set for
	 * the field, generate a default one based on the action.
	 * 
	 * @since    1.1
	 * 
	 * @param    string     $action Nonce action
	 * @param    string     $name Name of the field
	 * 
	 * @return   void
	 */
	private function _field($name = null) {

		$nonce = $this->_create();
		if ( is_null( $name ) ) {
			$name = '_luna_nonce_' . str_replace( '-', '_', strtolower( $this->action ) );
		}

		echo '<input type="hidden" name="' . $name . '" value="' . $nonce . '"/>';
	}

	/**
	 * Check a nonce validity.
	 * 
	 * Create a temporary valid nonce, match it against the submitted nonce
	 * and return the result.
	 * 
	 * @since    1.1
	 * 
	 * @param    string     $nonce Nonce to validate
	 * 
	 * @return   boolean    Validation result
	 */
	private function _verify($nonce) {

		$secret   = $this->tick.'|'.$this->action.'|'.$this->user.'|'.$this->seed;
		$expected = $this->_create();
		if ($this->compare($expected, $nonce)) {
			return true;
		}

		return false;
	}

	/**
	 * Create a nonce.
	 * 
	 * This method is static and can be called publicly.
	 * 
	 * @since    1.1
	 * 
	 * @param    string    $action Nonce action
	 * 
	 * @return   string    Newly created nonce
	 */
	public static function create($action = -1) {

		$nonce = new LunaNonces($action);

		return $nonce->_create();
	}

	/**
	 * Validate a nonce.
	 * 
	 * This method is static and can be called publicly.
	 * 
	 * @since    1.1
	 * 
	 * @param    string     $nonce Nonce value to validate
	 * @param    string     $action Nonce action
	 * 
	 * @return   boolean    Validation result
	 */
	public static function verify($nonce, $action = -1) {

		if (empty($nonce)) {
			return false;
		}

		$check = new LunaNonces($action);
		$check->_verify($nonce);

		return $check;
	}

	/**
	 * Output a nonce field.
	 * 
	 * This method is static and can be called publicly.
	 * 
	 * @since    1.1
	 * 
	 * @param    string     $action Nonce action
	 * @param    string     $name Name of the field
	 * 
	 * @return   void
	 */
	public static function field($action = -1, $name = null) {

		$nonce = new LunaNonces($action);
		$nonce = $nonce->_field($name);
	}

	/**
	 * Match to hash againts each other to determine if they're identical.
	 * 
	 * @since    1.1
	 * 
	 * @param    string     $a First hash
	 * @param    string     $b Second hash
	 * 
	 * @return   boolean    Comparison result
	 */
	protected function compare($a, $b) {

		$a_length = strlen($a);
		if ($a_length !== strlen($b)) {
		    return false;
		}
		$result = 0;

		for ($i = 0; $i < $a_length; $i++) {
			$result |= ord($a[$i]) ^ ord($b[$i]);
		}

		return 0 === $result;
	}
}
