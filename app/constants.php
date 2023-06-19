<?php

//region PostgreSQL error codes
if (! defined('PGSQL_DUPLICATE_KEY_ERROR_CODE')) {
    define('PGSQL_DUPLICATE_KEY_ERROR_CODE', 23505);
}
//endregion PostgreSQL error codes

//region MySQL error codes
if (! defined('MYSQL_DUPLICATE_KEY_ERROR_CODE')) {
    define('MYSQL_DUPLICATE_KEY_ERROR_CODE', 1062);
}
//endregion MySQL error codes
