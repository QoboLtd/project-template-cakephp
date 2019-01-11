<?php
namespace App\Shell\Task;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class Upgrade20180706133300Task extends Shell
{
    private $capabilities = [
        'cap__Search_Controller_DashboardsController__index',
        'cap__Search_Controller_DashboardsController__view'
    ];

    /**
     * {@inheritDoc}
     */
    public function getOptionParser() : ConsoleOptionParser
    {
        $parser = new ConsoleOptionParser('console');
        $parser->setDescription('Add default permissions to "everyone" role');

        return $parser;
    }

    /**
     * main() method
     *
     * @return void
     */
    public function main() : void
    {
        $table = TableRegistry::get('RolesCapabilities.Roles');

        $role = $table->find('all')
            ->enableHydration(true)
            ->where(['name' => (string)Configure::readOrFail('RolesCapabilities.Roles.Everyone.name')])
            ->firstOrFail();

        $table = TableRegistry::get('RolesCapabilities.Capabilities');

        foreach ($this->capabilities as $capability) {
            $query = $table->find('all')
                ->where(['name' => $capability, 'role_id' => $role->get('id')]);

            if (! $query->isEmpty()) {
                continue;
            }

            $entity = $table->newEntity(['name' => $capability, 'role_id' => $role->get('id')]);
            if (! $table->save($entity)) {
                $this->abort(
                    sprintf('Failed to add capability "%s" in "%s" role', $capability, $role->get('name'))
                );
            }
        }

        $this->success('Capabilities added successfully');
    }
}
