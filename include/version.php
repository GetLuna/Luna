<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

class Version {
	// See http://getluna.org/docs/version.php for more info
	const FORUM_VERSION = '1.1-alpha.1.1';
	const FORUM_CORE_VERSION = '1.1.4717';
	const LUNA_CORE_REVISION = 98;

	// The Luna Core code name
	const FORUM_CODE_NAME = 'bittersweetshimmer';

	// The database version number, every change in the database requires this number to go one up
	const FORUM_DB_VERSION = '88.08';

	// The parser version number, every change to the parser requires this number to go one up
	const FORUM_PARSER_VERSION = '11.1.3';

	// The search index version number, every change to the search index requires this number to go one up
	const FORUM_SI_VERSION = '2.0';

	// Luna system requirements
	const MIN_PHP_VERSION = '5.1.0';
	const MIN_MYSQL_VERSION = '5.0.0';
	const MIN_PGSQL_VERSION = '8.0.0';
}
?>