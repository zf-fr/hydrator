<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 *
 * @method HydratorInterface get($name)
 */
class HydratorPluginManager extends AbstractPluginManager
{
    /**
     * @var string[]
     */
    protected $invokableClasses = array(
        ArraySerializableHydrator::class => ArraySerializableHydrator::class,
        ClassMethodsHydrator::class      => ClassMethodsHydrator::class,
        ObjectPropertyHydrator::class    => ObjectPropertyHydrator::class,
        ReflectionHydrator::class        => ReflectionHydrator::class
    );

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof HydrateInterface || $plugin instanceof ExtractInterface) {
            return; // We're okay!
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Hydrator\HydratorInterface,
             Hydrator\HydrateInterface or Hydrator\ExtractInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
