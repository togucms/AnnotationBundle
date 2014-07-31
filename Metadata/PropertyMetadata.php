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

namespace Togu\AnnotationBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
	public $toguType;
	public $getter;
	public $setter;
	private $reflectionClass;

	public function serialize()
	{
		return serialize(array(
				$this->class,
				$this->name,
				$this->toguType,
				$this->getter,
				$this->setter,
		));
	}

	public function unserialize($str)
	{
		list(
			$this->class,
			$this->name,
			$this->toguType,
			$this->getter,
			$this->setter
		) = unserialize($str);

		$this->reflectionClass = new \ReflectionClass($this->class);
		$this->reflection = new \ReflectionProperty($this->class, $this->name);
		$this->reflection->setAccessible(true);
	}

	/**
     * @param object $obj
     *
     * @return mixed
     */
  	public function getValue($object) {
		if(null === $this->reflectionClass) {
			$this->reflectionClass = new \ReflectionClass($this->class);
		}
		$method = $this->reflectionClass->getMethod($this->getter);
		return $method->invoke($object);
	}

	public function setValue($object, $value) {
		if(null === $this->reflectionClass) {
			$this->reflectionClass = new \ReflectionClass($this->class);
		}
		$method = $this->reflectionClass->getMethod($this->setter);
		return $method->invoke($object, $value);
	}
}
