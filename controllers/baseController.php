<?php
/*
 * Base Controller
 */

use FelixOnline\Core;

class BaseController {
	protected $theme; // placeholder for theme class
	protected $db;

	function __construct() {
		global $db;
		global $safesql;
		$this->db = $db;
		$this->safesql = $safesql;

		/*
		 * Set theme here so that it can be overridden by a controller if necessary
		 */
		$theme = new Core\Theme(CURRENT_THEME);
		$this->theme = $theme->getClass(); // used so that theme can specify a theme class if necessary
		$this->theme->setSite('main');
	}

	function HEAD($matches)
	{
		// Used by updowntester
		ob_end_flush();
		exit;
	}
}
