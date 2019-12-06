<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use RuntimeException;

/**
 * Settings shell command.
 */

class SettingsShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('reset', [
                'help' => 'Delete one key from the database',
                'parser' => [
                    'options' => [
                        'reset' => [
                            'help' => __d('Qobo/ProjectTemplateCakephp', 'Insert key'),
                            'required' => true,
                        ]
                    ]
                ],
            ]);
        $parser->addSubcommand('resetAll', [
               'help' => 'Drop table Settings',
                'parser' => [
                    'options' => [
                        'reset' => [
                            'required' => false,
                        ]
                    ]
                ],
            ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return null
     * @throws RuntimeException
     */
    public function main()
    {
        $alias = Hash::combine(Configure::read('Settings'), '{s}.{s}.{s}.{s}.alias', '{s}.{s}.{s}.{s}.type');
        $links = Hash::filter(Hash::combine(Configure::read('Settings'), '{s}.{s}.{s}.{s}.alias', '{s}.{s}.{s}.{s}.links'));

        /**
         * @var \App\Model\Table\SettingsTable $query
         */
        $query = TableRegistry::get('Settings');
        $settings = $query->getAliasDiff(array_keys($alias));

        $data = [];
        foreach ($settings as $set) {
            $data[] = $this->setData($alias, $set, $set);
            if (empty($links[$set])) {
                continue;
            }
            foreach ($links[$set] as $aliases => $value) {
                if (in_array($value, $settings)) {
                    throw new RuntimeException('Double alias found');
                }
                $data[] = $this->setData($alias, $set, $value);
            }
        }

        try {
            /**
             * @var \Cake\ORM\ResultSet&iterable<\Cake\Datasource\EntityInterface> $entities
             */
            $entities = $query->newEntities($data);
            if ($query->saveMany($entities)) {
                $this->out('Settings successfully updated');
            } else {
                $gotErrors = false;
                foreach ($entities as $key => $value) {
                    if (!empty($value->getErrors())) {
                        $gotErrors = true;
                        $this->out("Error on saving " . $value->get('key'));
                    }
                }
                $gotErrors ? $this->out('Failed to update.') : $this->out('Nothing to update.');
            }
        } catch (RuntimeException $e) {
            throw new RuntimeException($e);
        }

        return null;
    }

    /**
     * Prepare array for new entity
     * @param mixed[] $alias alias
     * @param string $index index
     * @param string $value value
     * @return mixed
     */
    private function setData(array $alias, string $index, string $value)
    {
        return [
            'key' => $value,
            'value' => Configure::read($index),
            'scope' => 'app',
            'context' => 'app',
            'type' => $alias[$index] // dynamic field to pass `type` to the validator
        ];
    }

    /**
     * reset() method. Truncate key in table Settings
     * @param string $key key of DB to delete
     * @return void
     */
    public function reset(string $key = ''): void
    {
        $query = TableRegistry::get('Settings');
        if (empty($key)) {
            $this->out('Insert key to delete');

            return;
        }

        $query->deleteAll(['key' => $key]);
    }

    /**
     * resetAll() method. Truncate all table Settings
     * @return void
     */
    public function resetAll(): void
    {
        $query = TableRegistry::get('Settings');
        $this->out('Truncate table Settings');
        $query->deleteAll([]);
    }
}
