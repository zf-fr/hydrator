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
 * A composite filter is built as a tree of filters
 */
class CompositeFilter implements FilterInterface
{
    const TYPE_AND = 0;
    const TYPE_OR  = 1;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var array|FilterInterface[]
     */
    protected $filters;

    /**
     * @param  int               $type
     * @param  FilterInterface[] $filters
     */
    public function __construct($type, array $filters = [])
    {
        $this->type    = $type;
        $this->filters = $filters;
    }

    /**
     * @param  FilterInterface $filter
     * @return void
     */
    public function andFilter(FilterInterface $filter)
    {
        $this->filters = new CompositeFilter(self::TYPE_AND, [$this, $filter]);
    }

    /**
     * @param  FilterInterface $filter
     * @return void
     */
    public function orFilter(FilterInterface $filter)
    {
        $this->filters = new CompositeFilter(self::TYPE_OR, [$this, $filter]);
    }

    /**
     * {@inheritDoc}
     */
    public function accept($property, ExtractionContext $context = null)
    {
        // If there are no filters attached, we decide to always accept
        if (empty($this->filters)) {
            return true;
        }

        // If condition is OR, only one filter needs to evaluate to true
        if ($this->type === self::TYPE_OR) {
            foreach ($this->filters as $filter) {
                if ($filter->accept($property, $context) === true) {
                    return true;
                }
            }

            return false;
        } else {
            // If condition is AND, all the filters need to evaluate to true
            foreach ($this->filters as $filter) {
                if ($filter->accept($property, $context) === false) {
                    return false;
                }
            }

            return true;
        }
    }
}
