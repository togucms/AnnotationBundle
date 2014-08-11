<?php

/*
 * Copyright (c) 2012-2014 Alessandro Siragusa <alessandro@togu.io>
 *
 * This file is part of the Togu CMS.
 *
 * Togu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Togu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Togu.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Togu\AnnotationBundle\Data;

use Metadata\MetadataFactoryInterface;

class AnnotationProcessor
{
	private $metadataFactory;

	public function __construct(MetadataFactoryInterface $metadataFactory)
	{
		$this->metadataFactory = $metadataFactory;
	}

	/**
	 * Get all the fields annotated with @Type of type $type
	 *
	 * @param string $type The annotated type we want to get
	 * @return array the field values
	 */
	public function getFieldValuesOfType($object, $type, &$values) {
		$fields = $this->getFieldsOfType($object, $type);
		foreach ($fields as $field) {
			$value = $field->getValue($object);
			if($value) {
				$values[$value->getId()] = $value;
			}
		}
		$references = $this->getFieldsOfType($object, 'reference');
		foreach ($references as $reference) {
			$collection = $reference->getValue($object);
			if($collection) {
				foreach ($collection as $child) {
					$this->getFieldValuesOfType($child, $type, $values);
				}
			}
		}
	}

	/**
	 *
	 * @param mixed $parent
	 * @param array $objects
	 */
	public function getAllObjects($parent, &$objects) {
		$objects[$parent->getId()] = $parent;

		$references = $this->getFieldsOfType($parent, 'reference');
		foreach ($references as $reference) {
			$collection = $reference->getValue($parent);
			if($collection) {
				foreach ($collection as $child) {
					$this->getAllObjects($child, $objects);
				}
			}
		}

		$referenceones = $this->getFieldsOfType($parent, 'referenceone');
		foreach ($referenceones as $reference) {
			$child = $reference->getValue($parent);
			if($child) {
				$this->getAllObjects($child, $objects);
			}
		}

		$links = $this->getFieldsOfType($parent, 'link');
		foreach ($links as $link) {
			$child = $link->getValue($parent);
			if($child) {
				$objects[$child->getId()] = $child;
			}
		}
	}

	/**
	 * @param mixed $object
	 * @param string $type
	 *
	 * @return array
	 */
	public function getFieldsOfType($object, $type) {
		if (!is_object($object)) {
			throw new \InvalidArgumentException('No object provided');
		}
		$fields = array();

		$classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));

		foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
			if (isset($propertyMetadata->toguType) && $propertyMetadata->toguType === $type) {
				if (!is_object($object)) {
					throw new \InvalidArgumentException('No object provided');
				}
				array_push($fields, $propertyMetadata);
			}
		}

		return $fields;
	}

	public function getModelOfClass($object) {
		if (!is_object($object)) {
			throw new \InvalidArgumentException('No object provided');
		}
		$classMetadata = $this->metadataFactory->getMetadataForClass(get_class($object));
		return $classMetadata->model;
	}

}
