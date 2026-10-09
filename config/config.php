<?php
/**
 * Application configuration.
 *
 * Everything environment-specific lives here and nowhere else, so moving the
 * site to a live server is a one-file change. On Hostinger and most shared
 * hosts the database sits on the same machine as the site, so DB_HOST stays
 * 127.0.0.1 there too — only the name, user and password change.
 *
 * Do not commit real production credentials to version control.
 */

declare(strict_types=1);

/* ---------------------------------------------------------------- database */
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_NAME', 'ngo');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/* ------------------------------------------------------------------- paths */
/** Filesystem root of the public site (no trailing slash). */
define('BASE_PATH', dirname(__DIR__));

/** Where admin-uploaded images are written, relative to BASE_PATH. */
define('UPLOAD_DIR', 'assets/img/uploads');
define('UPLOAD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'uploads');

/** Largest image the admin will accept, in bytes. */
define('UPLOAD_MAX_BYTES', 5 * 1024 * 1024);

/* --------------------------------------------------------------- behaviour */
/**
 * Show PHP errors on screen. Turn this off on the live site — a stack trace
 * from a failed query names the database and the table layout.
 */
define('DEBUG_MODE', true);

date_default_timezone_set('Asia/Kolkata');

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
