<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zfr\Hydrator\Strategy;

/**
 * A strategy is a very powerful concept that allows to alter a property before extracting or
 * hydrating it
 */
interface StrategyInterface
{
    /**
     * Converts the given value so that it can be extracted by the hydrator
     *
     * @param  mixed $value   The original value
     * @param  mixed $context An optional context (most often, the array or data)
     * @return mixed
     */
    public function extract($value, $context = null);

    /**
     * Converts the given value so that it can be hydrated by the hydrator
     *
     * @param  mixed $value   The original value
     * @param  mixed $context An optional context (most often, the object itself)
     * @return mixed
     */
    public function hydrate($value, $context = null);
}
