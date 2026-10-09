<?php
/**
 * Database connection.
 *
 * One PDO handle per request, created on first use. PDO rather than mysqli
 * because every query in this codebase goes through a prepared statement with
 * named or positional placeholders, and PDO's interface for that is the one
 * the helper layer in includes/functions.php is built around.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Returns the shared PDO connection, opening it on the first call.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            // Errors throw. Silent failure is how a CMS ends up showing an
            // empty page with no clue why.
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Real prepared statements, not emulated ones. Emulation quietly
            // turns every bound value back into a string, which breaks LIMIT
            // and any integer comparison that matters.
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            throw $e;
        }

        error_log('Database connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('The site is temporarily unavailable. Please try again shortly.');
    }

    return $pdo;
}
