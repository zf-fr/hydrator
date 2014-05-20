<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator\Factory;

use Hydrator\HydratorPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a hydrator plugin manager
 *
 * Users can define their own hydrators by defining the "zfr_hydrators" key in their
 * config file
 *
 * @TODO: if this ship to ZF3, change zfr_hydrators to hydrators key
 */
class HydratorPluginManagerFactory implements FactoryInterface
{
    /**
     * Create a hydrator plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HydratorPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = isset($config['zfr_hydrators']) ? $config['zfr_hydrators'] : array();

        return new HydratorPluginManager($config);
    }
}
