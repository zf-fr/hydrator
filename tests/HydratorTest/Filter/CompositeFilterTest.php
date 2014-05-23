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

namespace HydratorTest\Filter;

use Hydrator\Context\ExtractionContext;
use Hydrator\Filter\CompositeFilter;
use Hydrator\Filter\FilterInterface;

class CompositeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testAlwaysAcceptIfNoFilters()
    {
        $compositeFilter = new CompositeFilter([], CompositeFilter::TYPE_OR);
        $this->assertTrue($compositeFilter->accept('foo'));

        $compositeFilter = new CompositeFilter([], CompositeFilter::TYPE_AND);
        $this->assertTrue($compositeFilter->accept('foo'));
    }

    public function testOrCondition()
    {
        $filterOne = $this->getMock(FilterInterface::class);
        $filterTwo = $this->getMock(FilterInterface::class);

        $compositeFilter = new CompositeFilter([$filterOne, $filterTwo], CompositeFilter::TYPE_OR);

        $context = new ExtractionContext(new \stdClass());

        $filterOne->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(true));

        // Never called because first filter evaluates to true
        $filterTwo->expects($this->never())->method('accept');

        $this->assertTrue($compositeFilter->accept('foo', $context));
    }

    public function testAndCondition()
    {
        $filterOne = $this->getMock(FilterInterface::class);
        $filterTwo = $this->getMock(FilterInterface::class);

        $compositeFilter = new CompositeFilter([$filterOne, $filterTwo], CompositeFilter::TYPE_AND);

        $context = new ExtractionContext(new \stdClass());

        $filterOne->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(true));

        $filterTwo->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(false));

        $this->assertFalse($compositeFilter->accept('foo', $context));
    }

    public function testNestedCompositeFilter()
    {
        $filterOne = $this->getMock(FilterInterface::class);
        $filterOne->expects($this->atLeastOnce())->method('accept')->will($this->returnValue(true));
        $firstCompositeFilter = new CompositeFilter([$filterOne]);

        $filterTwo = $this->getMock(FilterInterface::class);
        $filterTwo->expects($this->atLeastOnce())->method('accept')->will($this->returnValue(false));
        $secondCompositeFilter = new CompositeFilter([$filterTwo]);

        $rootCompositeFilter = new CompositeFilter(
            [$firstCompositeFilter, $secondCompositeFilter],
            CompositeFilter::TYPE_AND
        );

        $this->assertFalse($rootCompositeFilter->accept('foo'));

        $rootCompositeFilter = new CompositeFilter(
            [$firstCompositeFilter, $secondCompositeFilter],
            CompositeFilter::TYPE_OR
        );

        $this->assertTrue($rootCompositeFilter->accept('foo'));
    }
}
