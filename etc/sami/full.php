<?php
/**
 * Sami source documentation config file
 *
 * This configuration file tells Sami to
 * generate source code documentation
 * for the 'src' and 'vendor' folders.
 *
 * NOTE: This might take a while!
 *
 * To generate/update documentation, run:
 * $ ./vendor/bin/sami.php update etc/sami/full.php
 */

use Sami\Parser\Filter\TrueFilter;
use Symfony\Component\Finder\Finder;

$projectRoot = dirname(dirname(__DIR__));
try {
    Dotenv::load($projectRoot);
}
catch (\Exception $e) {
    echo $e->getMessage();
    exit(1);
}

# Find PHP files to document
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('config')
    ->exclude('tests')
    ->exclude('Test')
    ->in(['./src', './vendor'])
;

# If PROJECT_NAME is not set, use the project folder
$projectName = getenv('PROJECT_NAME');
if (empty($projectName)) {
    $projectName = basename($projectRoot);
}

# If BUILD_DIR is not set, use build/ in project root
$buildDir = getenv('BUILD_DIR');
if (empty($buildDir)) {
    $buildDir = $projectRoot . DIRECTORY_SEPARATOR . 'build';
}

$sami = new Sami\Sami($iterator, array(
    'title'     => $projectName,
    'build_dir' => $buildDir . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . 'source',
    'cache_dir' => $buildDir . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'cache',
));

// document all methods and properties, not public only
//$sami['filter'] = function () {
//    return new TrueFilter();
//};

return $sami;
