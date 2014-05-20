<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator\Filter;

use Hydrator\Context\ExtractionContext;

/**
 * This filter accepts any method that starts with "get"
 */
final class GetFilter implements FilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        return substr($property, 0, 3) === 'get';
    }
}
