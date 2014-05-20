<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Hydrator;

/**
 * This very simple hydrator uses the public variables of an object.
 */
class ObjectPropertyHydrator extends AbstractHydrator
{
    /**
     * {@inheritDoc}
     */
    public function extract($object)
    {
        $data   = get_object_vars($object);
        $result = [];

        $context         = clone $this->extractionContext; // Performance trick, do not try to instantiate
        $context->object = $object;

        foreach ($data as $property => $value) {
            if (!$this->filterChain->accept($property, $context)) {
                unset($data[$property]);
                continue;
            }

            $property          = $this->namingStrategy->getNameForExtraction($property, $context);
            $result[$property] = $this->extractValue($property, $value, $context);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, $object)
    {
        $context         = clone $this->hydrationContext; // Performance trick, do not try to instantiate
        $context->object = $object;
        $context->data   = $data;

        foreach ($data as $property => $value) {
            $property          = $this->namingStrategy->getNameForHydration($property, $context);
            $object->$property = $this->hydrateValue($property, $value, $context);
        }

        return $object;
    }
}
