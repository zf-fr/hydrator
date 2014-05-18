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

use DateTime;
use Hydrator\Context\ExtractionContext;
use Hydrator\Context\HydrationContext;

/**
 * Built-in strategy that can outputs Date to a given format. By default, it outputs dates to
 * RFC3339, as this is a widely understood format by all browsers
 */
final class DateStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @param string $format
     */
    public function __construct($format = DateTime::RFC3339)
    {
        $this->format = (string) $format;
    }

    /**
     * Set extraction format
     *
     * @param  string $format
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
    }

    /**
     * Get extraction format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($value, ExtractionContext $context = null)
    {
        if (is_int($value)) {
            $timestamp = $value;
            $value     = new DateTime();
            $value->setTimestamp($timestamp);
        }

        if (!$value instanceof DateTime) {
            return $value;
        }

        return $value->format($this->format);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value, HydrationContext $context = null)
    {
        return new DateTime($value);
    }
}
