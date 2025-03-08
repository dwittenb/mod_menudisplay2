<?php

namespace Didi\Module\MenuDisplay\Site\Dispatcher;

// use Didi\Module\Menudisplay\Site\Helper;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\Registry\Registry;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    // return the LayoutData
    protected function getLayoutData()
    {
        $settings = new Registry($this->module->params);
        $data = parent::getLayoutData();

        // add result to data
        $data['items'] = $this->getHelperFactory()
            ->getHelper('MenuDisplayHelper')
            ->getMenuArticles($data['params'], $this->getApplication());

        return $data;
    }
}
