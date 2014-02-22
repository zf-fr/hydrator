<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zfr\Hydrator\Filter;

/**
 * This filter accepts any method that starts with "is"
 */
class IsFilter implements FilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function accept($property, $context = null)
    {
        $pos = strpos($property, '::');
        $pos = $pos !== false ? $pos + 2 : 0;

        return substr($property, $pos, 2) === 'is';
    }
}
