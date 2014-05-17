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
use Zend\Stdlib\Hydrator\HydratorInterface as Zf2HydratorInterface;

/**
 * @TODO: remove ZF2 compatibility
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class HydratorPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $invokableClasses = array(
        'Zend\Hydrator\ArraySerializableHydrator' => 'Zend\Hydrator\ArraySerializableHydrator',
        'Zend\Hydrator\ClassMethodsHydrator'      => 'Zend\Hydrator\ClassMethodsHydrator',
        'Zend\Hydrator\ObjectPropertyHydrator'    => 'Zend\Hydrator\ObjectPropertyHydrator',
        'Zend\Hydrator\ReflectionHydrator'        => 'Zend\Hydrator\ReflectionHydrator'
    );

    /**
     * @var array
     */
    protected $aliases = array(
        'ArraySerializableHydrator' => 'Zend\Hydrator\ArraySerializableHydrator',
        'ArraySerializable'         => 'Zend\Hydrator\ArraySerializableHydrator', // ZF2 compat
        'ClassMethodsHydrator'      => 'Zend\Hydrator\ClassMethodsHydrator',
        'ClassMethods'              => 'Zend\Hydrator\ClassMethodsHydrator', // ZF2 compat
        'ObjectPropertyHydrator'    => 'Zend\Hydrator\ObjectPropertyHydrator',
        'ObjectProperty'            => 'Zend\Hydrator\ObjectPropertyHydrator', // ZF2 compat
        'ReflectionHydrator'        => 'Zend\Hydrator\ReflectionHydrator',
        'Reflection'                => 'Zend\Hydrator\ReflectionHydrator' // ZF2 compat
    );

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof HydratorInterface || $plugin instanceof Zf2HydratorInterface) {
            return; // We're okay!
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Hydrator\HydratorInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
