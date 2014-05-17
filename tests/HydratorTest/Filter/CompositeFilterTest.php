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
    public function testCanAddFiltersThroughConstructor()
    {
        $filter1 = $this->getMock(FilterInterface::class);
        $filter2 = $this->getMock(FilterInterface::class);

        $compositeFilter = new CompositeFilter([$filter1, $filter2]);

        $this->assertCount(2, $compositeFilter->getFilters());
    }

    public function testCanAddAndRemoveFilter()
    {
        $filter = $this->getMock(FilterInterface::class);

        $compositeFilter = new CompositeFilter();
        $compositeFilter->addFilter($filter);
        $this->assertCount(1, $compositeFilter->getFilters());

        $compositeFilter->removeFilter($filter);
        $this->assertCount(0, $compositeFilter->getFilters());
    }

    public function testCanClearFilters()
    {
        $filter = $this->getMock(FilterInterface::class);

        $compositeFilter = new CompositeFilter();
        $compositeFilter->addFilter($filter);
        $compositeFilter->clearFilters();

        $this->assertEmpty($compositeFilter->getFilters());
    }

    public function testAlwaysAcceptIfNoFilters()
    {
        $compositeFilter = new CompositeFilter();
        $this->assertTrue($compositeFilter->accept('foo'));
    }

    public function testOrCondition()
    {
        $compositeFilter = new CompositeFilter();
        $filterOne       = $this->getMock(FilterInterface::class);
        $filterTwo       = $this->getMock(FilterInterface::class);

        $context = new ExtractionContext(new \stdClass());

        $filterOne->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(true));

        // Never called because first filter evaluates to true
        $filterTwo->expects($this->never())->method('accept');

        $compositeFilter->addFilter($filterOne);
        $compositeFilter->addFilter($filterTwo);

        $this->assertTrue($compositeFilter->accept('foo', $context));
    }

    public function testAndCondition()
    {
        $compositeFilter = new CompositeFilter([], CompositeFilter::CONDITION_AND);
        $filterOne       = $this->getMock(FilterInterface::class);
        $filterTwo       = $this->getMock(FilterInterface::class);

        $context = new ExtractionContext(new \stdClass());

        $filterOne->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(true));

        $filterTwo->expects($this->once())
                  ->method('accept')
                  ->with('foo', $context)
                  ->will($this->returnValue(false));

        $compositeFilter->addFilter($filterOne);
        $compositeFilter->addFilter($filterTwo);

        $this->assertFalse($compositeFilter->accept('foo', $context));
    }

    public function testNestedCompositeFilter()
    {
        $rootCompositeFilter = new CompositeFilter([], CompositeFilter::CONDITION_AND);

        $firstCompositeFilter = new CompositeFilter();
        $filterOne = $this->getMock(FilterInterface::class);
        $filterOne->expects($this->once())->method('accept')->will($this->returnValue(true));
        $firstCompositeFilter->addFilter($filterOne);

        $secondCompositeFilter = new CompositeFilter();
        $filterTwo = $this->getMock(FilterInterface::class);
        $filterTwo->expects($this->once())->method('accept')->will($this->returnValue(false));
        $secondCompositeFilter->addFilter($filterTwo);

        $rootCompositeFilter->addFilter($firstCompositeFilter);
        $rootCompositeFilter->addFilter($secondCompositeFilter);

        $this->assertFalse($rootCompositeFilter->accept('foo'));
    }
}