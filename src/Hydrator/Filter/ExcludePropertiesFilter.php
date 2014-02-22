<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zfr\Hydrator\Filter;

/**
 * This filter exclude any properties that have a name in the array
 */
class ExcludePropertiesFilter implements FilterInterface
{
    /**
     * The properties to exclude
     *
     * @var array
     */
    protected $properties;

    /**
     * @param array|string $properties The properties to exclude
     */
    public function __construct($properties)
    {
        // Flipping is an optimization to allow us "isset"
        $this->properties = array_flip($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, $context = null)
    {
        return isset($this->properties[$property]);
    }
}
