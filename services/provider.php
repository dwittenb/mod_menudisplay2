<?php

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
