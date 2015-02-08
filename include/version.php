<?php

/**
 * Copyright (C) 2014 ModernBB
 * Based on work by PunBB (2002-2009), FluxBB (2009-2012)
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

class Version
{
	// See http://modernbb.be/docs/version.php for more info
	const FORUM_VERSION = '3.7.0';

	// The ModernBB Core version
	const FORUM_CORE_VERSION = '0.0.37.2592';

	// The database version number, every change in the database requires this number to go one up
	const FORUM_DB_VERSION = 77;

	// The parser version number, every change to the parser requires this number to go one up
	const FORUM_PARSER_VERSION = 10;

	// The search index version number, every change to the search index requires this number to go one up
	const FORUM_SI_VERSION = 2;

	// The minimal required PHP version to install ModernBB
	const MIN_PHP_VERSION = '5.1.0';

	// The minimal required MySQL version to install ModernBB
	const MIN_MYSQL_VERSION = '5.0.0';

	// The minimal required PostgreSQL version to install ModernBB
	const MIN_PGSQL_VERSION = '8.0.0';
}
?>