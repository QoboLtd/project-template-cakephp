<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $settings = $this->paginate($this->Settings);
        $this->set(compact('settings'));

        if (!$this->Auth->user('is_admin')) {
            throw new UnauthorizedException('Admin restricted area');
        }

        if ($this->request->is('put')) {
            $data = Hash::flatten($this->request->data());
            $query = TableRegistry::get('Settings');

            $set = [];
            foreach ($data as $key => $value) {
                $entity = $query->findByKey($key)->first();
                $newEntity = $this->Settings->patchEntity($entity, [
                    'key' => $key,
                    'value' => $value,
                ]);
                $set[] = $newEntity;
            }

            if ($query->saveMany($set)) {
                Configure::load('Settings', 'dbconfig', true);
                $this->Flash->success(__('Settings successfully updated'));
            } else {
                $this->Flash->error(__('Failed to update settings, please try again.'));
            }
        }
    }
}
