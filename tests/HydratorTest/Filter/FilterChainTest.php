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

use Hydrator\Filter\ExcludeMethodsFilter;
use Hydrator\Filter\FilterChain;
use Hydrator\Filter\GetFilter;

class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptByDefault()
    {
        $filterContainer = new FilterChain();
        $this->assertTrue($filterContainer->accept('foo'));
    }

    public function testAndFilter()
    {
        $filterContainer = new FilterChain();
        $filterContainer->andFilter(new ExcludeMethodsFilter(['getBar', 'getBaz']));
        $filterContainer->andFilter(new GetFilter());

        $this->assertFalse($filterContainer->accept('getBar'), 'Rejected because of the ExcludeMethods');
        $this->assertFalse($filterContainer->accept('isFoo'), 'Rejected because of the GetFilter');
        $this->assertTrue($filterContainer->accept('getFoo'));
    }

    public function testOrFilter()
    {
        $filterContainer = new FilterChain();
        $filterContainer->orFilter(new ExcludeMethodsFilter(['getBar', 'getBaz', 'hasFoo']));
        $filterContainer->orFilter(new GetFilter());

        $this->assertTrue($filterContainer->accept('getBar'), 'Accepted because of the GetFilter');
        $this->assertTrue($filterContainer->accept('isFoo'), 'Accepted because not in the GetFilter');
        $this->assertFalse($filterContainer->accept('hasFoo'), 'Rejected because is in Exclude and not a Get');
    }
}
