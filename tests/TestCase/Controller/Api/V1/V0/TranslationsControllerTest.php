<?php

namespace App\Test\TestCase\Controller\Api\V1\V0;

use App\Event\Controller\Api\IndexActionListener;
use App\Test\TestCase\Controller\BaseIntegrationTestCase;
use Cake\Core\Configure;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;

/**
 * Translations\Controller\TranslationsController Test Case
 *
 * @property \Cake\Http\Response $_response
 */
class TranslationsControllerTest extends BaseIntegrationTestCase
{
    /**
     * @var object $Translations
     */
    private $Translations;

    public $fixtures = [
        'app.Users',
        'plugin.Translations.Languages',
        'plugin.Translations.LanguageTranslations',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->setApiHeaders(['user_id' => '00000000-0000-0000-0000-000000000002']);

        $this->Translations = TableRegistry::getTableLocator()->get('Translations.Translations');

        // enable event tracking
        $this->Translations->getEventManager()->setEventList(new EventList());

        // Load default plugin configuration
        Configure::load('Translations.translations');

        EventManager::instance()->on(new IndexActionListener());
    }

    public function tearDown()
    {
        unset($this->Translations);

        parent::tearDown();
    }

    public function testIndex(): void
    {
        $this->get('/api/language-translations');

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $body = $this->_response->getBody();
        $response = json_decode($body);
        // $response = json_decode($this->_response->getBody());
        $this->assertTrue($response->success);
        $this->assertEmpty($response->data);
    }

    public function testIndexWithModelAndKey(): void
    {
        $this->get('/api/language-translations?model=Leads&foreign_key=00000000-0000-0000-0000-100000000001');

        $this->assertResponseOk();

        $response = json_decode($this->_response->getBody());
        $this->assertEquals(3, count($response->data));
    }

    public function testIndexWithField(): void
    {
        $this->get('/api/language-translations?model=Leads&foreign_key=00000000-0000-0000-0000-100000000001&field=description');

        $this->assertResponseOk();

        $response = json_decode($this->_response->getBody());
        $this->assertEquals(2, count($response->data));
    }

    public function testIndexWithLanguage(): void
    {
        $this->get('/api/language-translations?model=Leads&foreign_key=00000000-0000-0000-0000-100000000001&language=ru');

        $this->assertResponseOk();

        $response = json_decode($this->_response->getBody());
        $this->assertEquals(2, count($response->data));
    }

    public function testIndexWithFieldAndLanguage(): void
    {
        $this->get('/api/language-translations?model=Leads&foreign_key=00000000-0000-0000-0000-100000000001&field=code&language=ru');

        $this->assertResponseOk();

        $response = json_decode($this->_response->getBody());
        $this->assertEquals(1, count($response->data));
    }
}
