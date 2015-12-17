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
use Hydrator\HydratorInterface;
use Hydrator\NamingStrategy\ProvidesNamingStrategyTrait;

/**
 * This strategy allows to set another hydrator for a given strategy. This is especially
 * useful when you have embedded objects and want to hydrate/extract recursively
 */
class HydratorStrategy implements StrategyInterface
{
    use ProvidesNamingStrategyTrait;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($value, $property, ExtractionContext $context = null)
    {
        if (is_object($value)) {
            return $this->hydrator->extract($value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value, $property, HydrationContext $context = null)
    {
        if (is_array($value)) {
            $property = $this->namingStrategy->getNameForHydration($property, $context);
            $method   = 'get' . $property;
            $object = $context->object->{$method}();

            return $this->hydrator->hydrate($value, $object);
        }

        return $value;
    }
}
