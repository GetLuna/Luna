<?php

/*
 * Copyright (C) 2014-2015 Luna
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

class Version {
	// See http://getluna.org/docs/version.php for more info
	const FORUM_VERSION = '1.2-alpha.1';
	const FORUM_CORE_VERSION = '1.2.4920';
	const LUNA_CORE_REVISION = '120a1';

	// The Luna Core code name
	const LUNA_CODE_NAME = 'cornflowserblue';
	const LUNA_CODE_NAME_SEM = 'Cornflower Blue';

	// The database version number, every change in the database requires this number to go one up
	const FORUM_DB_VERSION = '89.00';

	// The parser version number, every change to the parser requires this number to go one up
	const FORUM_PARSER_VERSION = '11.2.0';

	// The search index version number, every change to the search index requires this number to go one up
	const FORUM_SI_VERSION = '2.0';

	// Luna system requirements
	const MIN_PHP_VERSION = '5.2.0';
	const MIN_MYSQL_VERSION = '5.0.0';
	const MIN_PGSQL_VERSION = '8.0.0';
}
?>