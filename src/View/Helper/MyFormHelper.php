<?php

namespace App\View\Helper;

use AdminLTE\View\Helper\FormHelper;
use App\View\Helper\MyHtmlHelper;
use Cake\View\View;

class MyFormHelper extends FormHelper
{
    /**
     * {@inheritDoc} \AdminLTE\View\Helper\FormHelper::control()
     */
    public function control($fieldName, array $options = [])
    {
        if (isset($options['cakeControl'])) {
            return parent::control($fieldName, $options);
        }

        $helper = new MyHtmlHelper(new View());
        $help = empty($options['help']) ? '' : $helper->help($options['help']);

        return sprintf('<div %s>%s</div>', $help, parent::control($fieldName, $options));
    }
}
