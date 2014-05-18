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

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class ClosureStrategy implements StrategyInterface
{
    /**
     * @var callable
     */
    protected $extractionClosure;

    /**
     * @var callable
     */
    protected $hydrationClosure;

    /**
     * @param callable|null $extractionClosure
     * @param callable|null $hydrationClosure
     */
    public function __construct(callable $extractionClosure = null, callable $hydrationClosure = null)
    {
        $this->extractionClosure = $extractionClosure;
        $this->hydrationClosure  = $hydrationClosure;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($value, ExtractionContext $context = null)
    {
        if (!$this->extractionClosure) {
            return $value;
        }

        $func = $this->extractionClosure;

        return $func($value, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value, HydrationContext $context = null)
    {
        if (!$this->hydrationClosure) {
            return $value;
        }

        $func = $this->hydrationClosure;

        return $func($value, $context);
    }
}
