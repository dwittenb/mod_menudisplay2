<?php

/***
 * @package     mod_menudisplay2
 * @copyright   Copyright (C) 2025 https://github.com/dwittenb/mod_menudisplay2, All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author     	Didi
 * @link 		https://github.com/dwittenb/mod_menudisplay2
 ***/

namespace Didi\Module\MenuDisplay\Site\Dispatcher;

defined('_JEXEC') or die;

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
