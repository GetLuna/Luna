<?php

/*
 * Copyright (C) 2014 Luna
 * Based on work by PunBB (2002-2009), FluxBB (2009-2012)
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

class Version {
	// See http://modernbb.be/docs/version.php for more info
	const FORUM_VERSION = '0.0.9';

	// The Luna Core version
	const FORUM_CORE_VERSION = '0.1.3357';

	// The database version number, every change in the database requires this number to go one up
	const FORUM_DB_VERSION = 85.15;

	// The parser version number, every change to the parser requires this number to go one up
	const FORUM_PARSER_VERSION = 11;

	// The search index version number, every change to the search index requires this number to go one up
	const FORUM_SI_VERSION = 2;

	// The minimal required PHP version to install Luna
	const MIN_PHP_VERSION = '5.1.0';

	// The minimal required MySQL version to install Luna
	const MIN_MYSQL_VERSION = '5.0.0';

	// The minimal required PostgreSQL version to install Luna
	const MIN_PGSQL_VERSION = '8.0.0';
}
?>