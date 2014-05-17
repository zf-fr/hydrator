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

namespace HydratorTest;

use Hydrator\AbstractHydrator;
use Hydrator\Context\ExtractionContext;
use Hydrator\Context\HydrationContext;
use Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Hydrator\Strategy\StrategyInterface;

class AbstractHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSetAndRemoveStrategies()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $this->assertFalse($hydrator->hasStrategy('foo'));

        $hydrator->setStrategy('foo', $this->getMock(StrategyInterface::class));
        $this->assertTrue($hydrator->hasStrategy('foo'));

        $hydrator->removeStrategy('foo');
        $this->assertFalse($hydrator->hasStrategy('foo'));
    }

    public function testCanClearStrategies()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $hydrator->setStrategy('foo', $this->getMock(StrategyInterface::class));
        $hydrator->clearStrategies();

        $this->assertEmpty($hydrator->getStrategies());
    }

    public function testCanGetWildcardStrategy()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $wildcardStrategy = $this->getMock(StrategyInterface::class);
        $hydrator->setStrategy('*', $wildcardStrategy);

        $this->assertTrue($hydrator->hasStrategy('foo'));
        $this->assertSame($wildcardStrategy, $hydrator->getStrategy('foo'));

        $specificStrategy = $this->getMock(StrategyInterface::class);
        $hydrator->setStrategy('foo', $specificStrategy);

        $this->assertNotSame($wildcardStrategy, $hydrator->getStrategy('foo'));
        $this->assertSame($specificStrategy, $hydrator->getStrategy('foo'));
    }

    public function testHasUnderscoreNamingStrategyByDefault()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);
        $this->assertInstanceOf(UnderscoreNamingStrategy::class, $hydrator->getNamingStrategy());
    }

    public function testCanExtractUsingStrategy()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $strategy = $this->getMock(StrategyInterface::class);
        $hydrator->setStrategy('foo', $strategy);

        $object  = new \stdClass;
        $context = new ExtractionContext($object);

        $strategy->expects($this->once())->method('extract')->with('myValue', $context);
        $hydrator->extractValue('foo', 'myValue', $context);
    }

    public function testCanHydrateUsingStrategy()
    {
        /* @var AbstractHydrator $hydrator */
        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $strategy = $this->getMock(StrategyInterface::class);
        $hydrator->setStrategy('foo', $strategy);

        $data    = ['myValue' => 'bar'];
        $context = new HydrationContext($data, new \stdClass());

        $strategy->expects($this->once())->method('hydrate')->with('myValue', $context);
        $hydrator->hydrateValue('foo', 'myValue', $context);
    }
}
