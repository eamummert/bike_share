<?php

namespace Libs\Form\Element;

use Zend\Form\Element as ZendElement;
use Zend\Form\Exception;
use Zend\Form\FieldsetInterface;
use Zend\Stdlib\ArrayUtils;

class Collection extends ZendElement\Collection
{
	protected $count = 0;

	protected $shouldCreateTemplate = true;

	protected $shouldCreateChildrenOnPrepareElement = false;

	public function getHydrator()
	{
		return $this->hydrator;
	}

    /**
     * Reset fieldsets if the count is different.
     *
     * @param array|\Traversable $object
     *
     * @return $this|\Zend\Form\Fieldset|FieldsetInterface
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setObject($object)
    {
        if (!is_array($object) && !$object instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object argument; received "%s"',
                __METHOD__,
                (is_object($object) ? get_class($object) : gettype($object))
            ));
        }

        $this->object = $object;

        $oldCount = $this->count;
        $this->count  = count($object);

        if ($this->count > $oldCount)
        {
            for ($i = $oldCount; $i < $this->count; $i++)
            {
                $elementOrFieldset = $this->createNewTargetElementInstance();
                $elementOrFieldset->setName($i);

                $this->add($elementOrFieldset);
            }
        }
        elseif ($this->count < $oldCount)
        {
            for ($i = $oldCount; $i > $this->count; $i--)
            {
                $this->remove($i);
            }
        }

        return $this;
    }

    /**
     * Check to make sure fieldset exists before cloning new one.
     *
     * @return array
     */
    public function extract()
    {
        if ($this->object instanceof \Traversable) {
            $this->object = ArrayUtils::iteratorToArray($this->object, false);
        }

        if (!is_array($this->object)) {
            return [];
        }

        $values = [];

        foreach ($this->object as $key => $value) {
            if ($this->hydrator) {
                $values[$key] = $this->hydrator->extract($value);
            } elseif ($value instanceof $this->targetElement->object) {
                // @see https://github.com/zendframework/zf2/pull/2848

                // Check to make sure fieldset exists before creating new one
                if ($this->has($key)) {
                    $targetElement = $this->get($key);
                } else {
                    $targetElement = clone $this->targetElement;
                }

                $targetElement->object = $value;
                $values[$key] = $targetElement->extract();
                if (! $this->createNewObjects() && $this->has($key)) {
                    $fieldset = $this->get($key);
                    if ($fieldset instanceof FieldsetInterface && $fieldset->allowObjectBinding($value)) {
                        $fieldset->setObject($value);
                    }
                }
            }
        }

        // Recursively extract and populate values for nested fieldsets
        foreach ($this->fieldsets as $fieldset) {
            $name = $fieldset->getName();
            if (isset($values[$name])) {
                $object = $values[$name];

                if ($fieldset->allowObjectBinding($object)) {
                    $fieldset->setObject($object);
                    $values[$name] = $fieldset->extract();
                } else {
                    foreach ($fieldset->fieldsets as $childFieldset) {
                        $childName = $childFieldset->getName();
                        if (isset($object[$childName])) {
                            $childObject = $object[$childName];
                            if ($childFieldset->allowObjectBinding($childObject)) {
                                $fieldset->setObject($childObject);
                                $values[$name][$childName] = $fieldset->extract();
                            }
                        }
                    }
                }
            }
        }

        return $values;
    }

    /**
     * Make sure elements can be replaced in case the count remains the same.
     * @link https://github.com/zendframework/zf2/pull/4884
     *
     * @param array|\Traversable $data
     *
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     */
    public function populateValues($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        // If there is fewer data and allowRemove is true, we reset the element count
        if (count($data) < $this->getCount()) {
            if (!$this->allowRemove) {
                throw new Exception\DomainException(sprintf(
                    'There are fewer elements than specified in the collection (%s). Either set the allow_remove option ' .
                    'to true, or re-submit the form.',
                    get_class($this)
                ));
            }

            $this->setCount(count($data));
        }

        // Check to see if elements have been replaced or removed
        foreach ($this->byName as $name => $elementOrFieldset) {
            if (isset($data[$name])) {
                continue;
            }

            if (!$this->allowRemove) {
                throw new Exception\DomainException(sprintf(
                    'Elements have been removed from the collection (%s) but the allow_remove option is not true.',
                    get_class($this)
                ));
            }

            $this->remove($name);
        }

        if ($this->targetElement instanceof FieldsetInterface) {
            foreach ($this->byName as $name => $fieldset) {
                if (isset($data[$name])) {
                    $fieldset->populateValues($data[$name]);
                    unset($data[$name]);
                }
            }
        } else {
            foreach ($this->byName as $name => $element) {
                $element->setAttribute('value', $data[$name]);
                unset($data[$name]);
            }
        }

        // If there are still data, this means that elements or fieldsets were dynamically added. If allowed by the user, add them
        if (!empty($data) && $this->allowAdd) {
            foreach ($data as $key => $value) {
                $elementOrFieldset = $this->createNewTargetElementInstance();
                $elementOrFieldset->setName($key);

                if ($elementOrFieldset instanceof FieldsetInterface) {
                    $elementOrFieldset->populateValues($value);
                } else {
                    $elementOrFieldset->setAttribute('value', $value);
                }

                $this->add($elementOrFieldset);
            }
        } elseif (!empty($data) && !$this->allowAdd) {
            throw new Exception\DomainException(sprintf(
                    'There are more elements than specified in the collection (%s). Either set the allow_add option ' .
                    'to true, or re-submit the form.',
                    get_class($this)
                )
            );
        }

        if (! $this->createNewObjects()) {
            $this->replaceTemplateObjects();
        }
    }
}

