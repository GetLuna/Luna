<?php

/**
 * Copyright (C) 2014 ModernBB
 * Based on work by PunBB (2002-2009), FluxBB (2009-2012)
 * Licensed under GPLv3
 */

class Version
{
	// The ModernBB version of the script in an x.y.z format we also allow -dev, -alpha, -beta and -rc,
	// and all can be followed by a build number, like 3.1-beta.1587. There shouldn't be a "z" when an affix is used.
	// But a "z" is required whenever a build number is in the version and no affix is used. Ex. 3.1.0.1587, and not 3.1.1587
	// Every time a change is made to the core, even when it's just a fix for a patch or a fast change, requires
	// this number to be changed to anything higher than the original state.
	const FORUM_VERSION = '3.3.1';

	// Internal revision number of services
	// The database version number, every change in the database requires this number to go one up
	const FORUM_DB_VERSION = 66;

	// The parser version number, every change to the parser requires this number to go one up
	const FORUM_PARSER_VERSION = 8;

	// The search index version number, every change to the search index requires this number to go one up
	const FORUM_SI_VERSION = 2;

	// The minimal required PHP version to install ModernBB
	const MIN_PHP_VERSION = '5.1.0';

	// The minimal required MySQL version to install ModernBB
	const MIN_MYSQL_VERSION = '5.0.0';
}
?>