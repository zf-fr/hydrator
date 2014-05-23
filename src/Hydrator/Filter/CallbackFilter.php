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
 * This filter accepts any method where the callable returns true
 */
final class CallbackFilter implements FilterInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        $callback = $this->callback;

        return $callback($property, $context);
    }
}
