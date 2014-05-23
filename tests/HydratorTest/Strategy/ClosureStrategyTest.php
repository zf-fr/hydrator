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
use Hydrator\Context\HydrationContext;
use Hydrator\Strategy\ClosureStrategy;

class ClosureStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testCanUseExtractionClosure()
    {
        $strategy = new ClosureStrategy(function ($value, ExtractionContext $context = null) {
            return $value * 2;
        }, null);

        $this->assertEquals(2, $strategy->extract(1));
    }

    public function testCanUseHydrationClosure()
    {
        $strategy = new ClosureStrategy(null, function ($value, HydrationContext $context = null) {
            return $value * 2;
        });

        $this->assertEquals(2, $strategy->hydrate(1));
    }
}
