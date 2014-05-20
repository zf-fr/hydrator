<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

use Hydrator\Context\ExtractionContext;
use Hydrator\Context\HydrationContext;
use Hydrator\Filter\FilterChain;
use Hydrator\NamingStrategy\ProvidesNamingStrategyTrait;
use Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Hydrator\Strategy\ProvidesStrategyTrait;

/**
 * This abstract hydrator provides a built-in support for filters and strategies. All
 * standards ZF3 hydrators extend this class
 */
abstract class AbstractHydrator implements HydratorInterface
{
    use ProvidesNamingStrategyTrait;
    use ProvidesStrategyTrait;

    /**
     * @var ExtractionContext
     */
    protected $extractionContext;

    /**
     * @var HydrationContext
     */
    protected $hydrationContext;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->namingStrategy    = new UnderscoreNamingStrategy();
        $this->extractionContext = new ExtractionContext();
        $this->hydrationContext  = new HydrationContext();
    }

    /**
     * Extract the value using a strategy, if one is set
     *
     * @param  string                 $property The name of the property
     * @param  mixed                  $value    The value to extract
     * @param  ExtractionContext|null $context  The context
     * @return mixed
     */
    public function extractValue($property, $value, ExtractionContext $context = null)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->extract($value, $context);
        }

        return $value;
    }

    /**
     * Hydrate the value using a strategy, if one is st
     *
     * @param  string                $property The name of the property
     * @param  mixed                 $value    The value to hydrate
     * @param  HydrationContext|null $context  The context
     * @return mixed
     */
    public function hydrateValue($property, $value, HydrationContext $context = null)
    {
        if ($this->hasStrategy($property)) {
            return $this->getStrategy($property)->hydrate($value, $context);
        }

        return $value;
    }
}
