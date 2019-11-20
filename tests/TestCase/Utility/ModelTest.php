<?php

namespace App\Test\TestCase\Utility;

use App\Utility\Model;
use Cake\TestSuite\TestCase;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\ModuleConfig\Parser\Parser;

class ModelTest extends TestCase
{
    public $fixtures = ['app.things'];

    public function testFields(): void
    {
        $fields = Model::fields('Things');

        //Retrieve migration fields
        $mc = $this->getModuleConfig('Things', []);
        $migrationFields = [];
        $config = json_encode($mc->parse());
        $migrationFields = $mc->parseToArray();

        $ignoreFieldsCount = 0;
        $combinedFieldsCount = 0;
        $trashedFieldsCount = 0;
        $migrationFieldsCount = 0;

        foreach ($fields as $field) {
            if (array_key_exists($field['name'], $migrationFields)) {
                $migrationFieldsCount++;
            } elseif ($field['name'] == 'trashed') {
                $trashedFieldsCount++;
            } elseif (preg_match("/^.*\_(unit|amount|currency)$/", $field['name'])) {
                $combinedFieldsCount++;
            } else {
                //Exist in the database but it is not trashed, combined or migration field
                $ignoreFieldsCount++;
                continue;
            }

            $this->assertArrayHasKey('name', $field);
            $this->assertInternalType('string', $field['name']);

            $this->assertArrayHasKey('label', $field);
            $this->assertInternalType('string', $field['label']);

            $this->assertArrayHasKey('type', $field);
            $this->assertInternalType('string', $field['type']);

            $this->assertArrayHasKey('db_type', $field);
            $this->assertInternalType('string', $field['db_type']);

            $this->assertArrayHasKey('meta', $field);
            $this->assertInternalType('array', $field['meta']);
        }

        $countFields = $migrationFieldsCount + $trashedFieldsCount + $combinedFieldsCount;
        $this->assertEquals(count($fields) - $ignoreFieldsCount, $countFields, "The fields in database, migration field and trashed fields doesn't match. This happens when new fields were introduced in the database, usually from another branch");
    }

    /**
     * Creates a custom instance of `ModuleConfig` with a parser, schema and
     * extra validation.
     *
     * @param string $module Module.
     * @param string[] $options Options.
     * @return ModuleConfig Module Config.
     */
    protected function getModuleConfig(string $module, array $options = []): ModuleConfig
    {
        $configFile = empty($options['configFile']) ? null : $options['configFile'];
        $mc = new ModuleConfig(ConfigType::MIGRATION(), $module, $configFile, ['cacheSkip' => true]);

        $schema = $mc->createSchema(['lint' => true]);
        $mc->setParser(new Parser($schema, ['lint' => true, 'validate' => true]));

        return $mc;
    }
}
