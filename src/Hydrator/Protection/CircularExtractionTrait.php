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

namespace Zfr\Hydrator\Protection;

/**
 * This trait aims to be used by an extractor to protect against circular extraction
 */
trait CircularExtractionTrait
{
    /**
     * @var array
     */
    static protected $circularHolder = [];

    /**
     * Begin an extraction
     *
     * @param  object $object
     * @return void
     */
    protected function beginExtraction($object)
    {
        self::$circularHolder[spl_object_hash($object)] = true;
    }

    /**
     * End an extraction
     *
     * @param  object $object
     * @return void
     */
    protected function endExtraction($object)
    {
        unset(self::$circularHolder[spl_object_hash($object)]);
    }

    /**
     * Detect a circular extraction for the given object
     *
     * @param  object $object
     * @return bool
     */
    public function isCircularExtraction($object)
    {
        return isset(self::$circularHolder[spl_object_hash($object)]);
    }
}
