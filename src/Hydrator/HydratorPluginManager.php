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
     * @var string[]
     */
    protected $aliases = array(
        'ArraySerializableHydrator' => ArraySerializableHydrator::class,
        'ArraySerializable'         => ArraySerializableHydrator::class, // ZF2 compat
        'ClassMethodsHydrator'      => ClassMethodsHydrator::class,
        'ClassMethods'              => ClassMethodsHydrator::class, // ZF2 compat
        'ObjectPropertyHydrator'    => ObjectPropertyHydrator::class,
        'ObjectProperty'            => ObjectPropertyHydrator::class, // ZF2 compat
        'ReflectionHydrator'        => ReflectionHydrator::class,
        'Reflection'                => ReflectionHydrator::class // ZF2 compat
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
