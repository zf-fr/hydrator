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

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
trait ProvidesStrategyTrait
{
    /**
     * List of strategies, indexed by a property name
     *
     * @var array|StrategyInterface[]
     */
    protected $strategies = [];

    /**
     * Set a new strategy for a given property
     *
     * @param  string            $name
     * @param  StrategyInterface $strategy
     * @return void
     */
    public function setStrategy($name, StrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
    }

    /**
     * Remove a strategy for a given property
     *
     * @param  string $name
     * @return void
     */
    public function removeStrategy($name)
    {
        unset($this->strategies[$name]);
    }

    /**
     * Get a strategy for a given property (null if none)
     *
     * @param  string $name
     * @return StrategyInterface|null
     */
    public function getStrategy($name)
    {
        if (isset($this->strategies[$name])) {
            return $this->strategies[$name];
        }

        return isset($this->strategies['*']) ? $this->strategies['*'] : null;
    }

    /**
     * Has a given property a strategy attached to it?
     *
     * @param  string $name
     * @return bool True if the given property has a strategy
     */
    public function hasStrategy($name)
    {
        return isset($this->strategies[$name]) || isset($this->strategies['*']);
    }

    /**
     * Get all the strategies
     *
     * @return array|StrategyInterface[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * Clear all the strategies
     *
     * @return void
     */
    public function clearStrategies()
    {
        $this->strategies = [];
    }
}
