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

namespace HydratorTest\Strategy;

use Hydrator\Context\ExtractionContext;
use Hydrator\Strategy\ClosureStrategy;
use Hydrator\Strategy\StrategyChain;

class StrategyChainTest extends \PHPUnit_Framework_TestCase
{
    public function testCanChainExtractionStrategies()
    {
        $strategy = new StrategyChain();

        $strategy->addExtractionStrategy(new ClosureStrategy(function($value, ExtractionContext $context = null) {
            return str_replace('_', ' ', $value);
        }, null), 10);

        $strategy->addExtractionStrategy(new ClosureStrategy(function($value, ExtractionContext $context = null) {
            return ucwords($value);
        }, null), 5);

        $this->assertEquals('One Value', $strategy->extract('one_value'));
    }

    public function testCanChainHydrationStrategies()
    {
        $strategy = new StrategyChain();

        $strategy->addHydrationStrategy(new ClosureStrategy(null, function($value, ExtractionContext $context = null) {
            return str_replace('_', ' ', $value);
        }), 10);

        $strategy->addHydrationStrategy(new ClosureStrategy(null, function($value, ExtractionContext $context = null) {
            return ucwords($value);
        }), 5);

        $this->assertEquals('One Value', $strategy->hydrate('one_value'));
    }
}
