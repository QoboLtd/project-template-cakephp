<?php
namespace App\Model\Table;

use App\Feature\Factory as FeatureFactory;
use CsvMigrations\CsvMigrationsUtils;
use CsvMigrations\Table;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

/**
 * App Model
 */
class AppTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('AuditStash.AuditLog', [
            'blacklist' => ['created', 'modified', 'created_by', 'modified_by']
        ]);
    }

    /**
     * Skip setting associations for disabled modules.
     *
     * {@inheritDoc}
     */
    protected function _setAssociationsFromConfig(array $config)
    {
        $mc = new ModuleConfig(ConfigType::MODULE(), $this->getRegistryAlias());
        $config = $mc->parse();
        $modules = $config->manyToMany->modules;
        if (empty($modules)) {
            return;
        }

        foreach ($modules as $module) {
            try {
                // skip if associated module is disabled
                $feature = FeatureFactory::get($module);
                if (!$feature->isActive()) {
                    continue;
                }
            } catch (\Exception $e) {
                // do nothing
            }

            $this->belongsToMany($module, [
                'className' => $module
            ]);
        }
    }

    /**
     * Skip setting associations for disabled modules.
     *
     * {@inheritDoc}
     */
    protected function setFieldAssociations(array $config, array $data)
    {
        foreach ($data as $module => $fields) {
            foreach ($fields as $field) {
                // skip non related type
                if (!in_array($field->getType(), ['related'])) {
                    continue;
                }

                try {
                    // skip if associated module is disabled
                    $feature = FeatureFactory::get($field->getAssocCsvModule());
                    if (!$feature->isActive()) {
                        continue;
                    }
                } catch (\Exception $e) {
                    // do nothing
                }

                // belongs-to association of the current module.
                if ($module === $config['table']) {
                    $name = CsvMigrationsUtils::createAssociationName($field->getAssocCsvModule(), $field->getName());
                    $this->belongsTo($name, [
                        'className' => $field->getAssocCsvModule(),
                        'foreignKey' => $field->getName()
                    ]);
                }

                // foreign key found in related module.
                if ($field->getAssocCsvModule() === $config['table']) {
                    $name = CsvMigrationsUtils::createAssociationName($module, $field->getName());
                    $this->hasMany($name, [
                        'className' => $module,
                        'foreignKey' => $field->getName()
                    ]);
                }
            }
        }
    }
}
