<?php

/***
 * @package     mod_menudisplay2
 * @copyright   Copyright (C) 2025 https://github.com/dwittenb/mod_menudisplay2, All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author     	Didi
 * @link 		https://github.com/dwittenb/mod_menudisplay2
 ***/

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class() implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\Didi\\Module\\MenuDisplay'));
        $container->registerServiceProvider(new HelperFactory('\\Didi\\Module\\MenuDisplay\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
