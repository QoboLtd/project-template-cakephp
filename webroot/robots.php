<?php

/**
 * This script allows for dynamic robots.txt rules,
 * based on the ALLOW_ROBOTS setting in the .env
 * file.
 *
 * Setting ALLOW_ROBOTS to false will disallow indexing
 * of anything on the site.  Setting it to true will
 * either allow everything or only what is defined in
 * the robots.txt file, if it exists and is readable.
 *
 * Furthermore, setting ALLOW_ROBOTS_EXCEPT_ON will
 * disable indexing for any server hostname that matches
 * the pattern.
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {
    (new \josegonzalez\Dotenv\Loader(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env'))->parse()->toEnv()->putenv();
} catch (\InvalidArgumentException $e) {
    // If there is no .env file, we are probably on
    // a local/dev/test install with the project that
    // is not properly deployed.  No need to allow
    // robots indexing it.
    $allowRobots = false;
}

$allowRobots = (bool)getenv('ALLOW_ROBOTS');

// Switch MIME type to text/plain
header('Content-Type: text/plain');

// Limit indexing on certain domains
if ($allowRobots) {
    $exceptionPattern = (string)getenv('ALLOW_ROBOTS_EXCEPT_ON');
    $currentDomain = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'];
    if ($exceptionPattern && $currentDomain && preg_match('/' . $exceptionPattern . '/i', $currentDomain)) {
        $allowRobots = false;
    }
}

if ($allowRobots) {
    // Allow indexing
    $robotsFile = __DIR__ . DIRECTORY_SEPARATOR . 'robots.txt';
    if (file_exists($robotsFile) && is_readable($robotsFile)) {
        // Use robots.txt rules if file exists and is readable
        readfile($robotsFile);
    } else {
        // Allow indexing of everything if we can't read robots.txt
        echo __("User-agent: *\n");
        echo __("Disallow:\n");
    }
} else {
    // Deny indexing
    echo __("User-agent: *\n");
    echo __("Disallow: /\n");
}
