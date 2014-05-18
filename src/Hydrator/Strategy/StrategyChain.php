<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Hydrator\Strategy;

use Hydrator\Context\ExtractionContext;
use Hydrator\Context\HydrationContext;
use SplPriorityQueue;

/**
 * A strategy chain allows to execute multiple strategies in a specified order
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class StrategyChain implements StrategyInterface
{
    /**
     * @var SplPriorityQueue|StrategyInterface[]
     */
    protected $extractionStrategies;

    /**
     * @var SplPriorityQueue|StrategyInterface[]
     */
    protected $hydrationStrategies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extractionStrategies = new SplPriorityQueue();
        $this->hydrationStrategies  = new SplPriorityQueue();
    }

    /**
     * Add an extraction strategy
     *
     * @param  StrategyInterface $strategy
     * @param  int               $priority
     * @return void
     */
    public function addExtractionStrategy(StrategyInterface $strategy, $priority = 1)
    {
        $this->extractionStrategies->insert($strategy, $priority);
    }

    /**
     * Add a hydration strategy
     *
     * @param  StrategyInterface $strategy
     * @param  int               $priority
     * @return void
     */
    public function addHydrationStrategy(StrategyInterface $strategy, $priority = 1)
    {
        $this->hydrationStrategies->insert($strategy, $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function extract($value, ExtractionContext $context = null)
    {
        foreach ($this->extractionStrategies as $extractionStrategy) {
            $value = $extractionStrategy->extract($value, $context);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value, HydrationContext $context = null)
    {
        foreach ($this->hydrationStrategies as $hydrationStrategy) {
            $value = $hydrationStrategy->hydrate($value, $context);
        }

        return $value;
    }
}
