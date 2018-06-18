<?php

/*
 * Copyright (C) 2014-2018 Luna
 * Licensed under GPLv2 (http://getluna.org/license.php)
 */

class Version
{
    const LUNA_VERSION = '2.1-alpha.3';
    const LUNA_BRANCH = '2.1';
    const LUNA_CORE_VERSION = '2.1.5907';
    const LUNA_CODE_NAME = 'Fallow';

    // The database version number, every change in the database requires this number to go one up
    const LUNA_DB_VERSION = '92.20';

    // The parser version number, every change to the parser requires this number to go one up
    const LUNA_PARSER_VERSION = '11.5.1';

    // The search index version number, every change to the search index requires this number to go one up
    const LUNA_SI_VERSION = '2.0';

    // Luna system requirements
    const MIN_PHP_VERSION = '5.4.0';
    const MIN_MYSQL_VERSION = '5.5.3';
    const MIN_PGSQL_VERSION = '8.0.0';
}
