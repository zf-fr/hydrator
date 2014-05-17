<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface as Zf2HydratorInterface;

/**
 * A hydrator is a simple object that does both extraction and hydration operations
 *
 * @TODO: remove ZF2 compatibility
 */
interface HydratorInterface extends ExtractionInterface, HydrationInterface, Zf2HydratorInterface
{
}
