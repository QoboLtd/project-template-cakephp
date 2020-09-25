<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link https://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * @var array $backtrace Holds all the views that are currently being rendered
     */
    private $backtrace = [];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadHelper('Menu.Menu');
        $this->loadHelper('Form', ['className' => 'MyForm']);
        $this->loadHelper('Html', ['className' => 'MyHtml']);
        $this->loadHelper('HtmlEmail');
        $this->loadHelper('Search');
        $this->loadHelper('SystemInfo');
        $this->loadHelper('CakeDC/Users.User');
        $this->loadHelper('Module');
    }

    /**
     * Extends render method to log backtrace information
     *
     * @param string $viewFile Filename of the view
     * @param array $data Data to include in rendered view. If empty the current
     *   View::$viewVars will be used.
     * @return string Rendered output
     * @throws \LogicException When a block is left open.
     * @triggers View.beforeRenderFile $this, [$viewFile]
     * @triggers View.afterRenderFile $this, [$viewFile, $content]
     */
    protected function _render($viewFile, $data = [])
    {
        array_unshift($this->backtrace, $viewFile);
        $output = parent::_render($viewFile, $data);
        array_shift($this->backtrace);

        return $output;
    }

    /**
     * Returns the backtrace for this view.
     *
     * @return string[] The backtrace
     */
    public function getBacktrace(): array
    {
        return $this->backtrace;
    }
}
