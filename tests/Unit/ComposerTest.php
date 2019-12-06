<?php

namespace App\Test\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Composer Test
 *
 */
class ComposerTest extends TestCase
{

    public const COMPOSER_JSON = 'composer.json';
    public const COMPOSER_LOCK = 'composer.lock';

    protected $folder;

    protected function setUp(): void
    {
        $this->folder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }

    /**
     * Provide a list composer files
     *
     * @return mixed[]
     */
    public function getComposerFiles(): array
    {
        return [
            [self::COMPOSER_JSON],
            [self::COMPOSER_LOCK],
        ];
    }

    /**
     * @dataProvider getComposerFiles
     */
    public function testComposerFiles(string $file): void
    {
        $this->assertFileExists($this->folder . $file, $file . " file is missing");
        $this->assertFileIsReadable($this->folder . $file, $file . " file is not readable");

        $content = file_get_contents($this->folder . $file);
        $this->assertNotEmpty($content, $file . " file is empty");

        // This is useful for catching merge conflicts, for example
        $content = $content ?: '';
        $json = json_decode($content);
        $this->assertNotNull($json, "Failed to parse JSON in file " . $file);

        $this->assertNotEmpty($json, "Empty result from JSON parsing in file " . $file);
    }

    public function testComposerLockUpToDate(): void
    {
        # Until composer v1.3.0-RC (https://github.com/composer/composer/releases/tag/1.3.0-RC)
        # we could easily compare the hashes.  However now it's not that
        # easy anymore.  Bringing in the whole composer source just for
        # such a quick test seems extensive, therefor we simply compare
        # modification timestamps of the two files.
        #
        # More details: http://stackoverflow.com/a/28730898

        // Skip if composer.lock does not exist
        if (!file_exists($this->folder . self::COMPOSER_LOCK)) {
            $this->markTestSkipped($this->folder . self::COMPOSER_LOCK . " does not exist.");
        }
        // Skip if composer.json does not exist
        if (!file_exists($this->folder . self::COMPOSER_JSON)) {
            $this->markTestSkipped($this->folder . self::COMPOSER_JSON . " does not exist.");
        }

        $lock = filemtime($this->folder . self::COMPOSER_LOCK);
        $json = filemtime($this->folder . self::COMPOSER_JSON);

        $this->assertGreaterThanOrEqual($json, $lock, "composer.lock is outdated");
    }
}
