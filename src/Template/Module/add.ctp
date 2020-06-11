<?php
/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Utility\Inflector;
use Qobo\Utils\Module\ModuleRegistry;

$config = ModuleRegistry::getModule($this->name)->getConfig();

$alias = isset($config['table']['alias']) ? $config['table']['alias'] : Inflector::humanize(Inflector::underscore($this->name));

$options = [
    'entity' => $entity,
    'fields' => $fields,
    'title' => [
        'page' => __('Create'),
        'alias' => $alias,
        'link' => $this->request->getParam('controller')
    ],
    'handlerOptions' => ['entity' => $this->request],
    'hasPanels' => !empty($config['panels'])
];

echo $this->fetch('pre_element');
echo $this->element('Module/post', ['options' => $options]);
echo $this->fetch('post_element');
