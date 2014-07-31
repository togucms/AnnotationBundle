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

use Metadata\MergeableClassMetadata as BaseMergeableClassMetadata;
use Metadata\MergeableInterface;

class MergeableClassMetadata extends BaseMergeableClassMetadata {
	public $model;
	public function serialize() {
		return serialize ( array (
				$this->name,
				$this->methodMetadata,
				$this->propertyMetadata,
				$this->fileResources,
				$this->createdAt,
				$this->model
		) );
	}
	public function unserialize($str) {
		list ( $this->name, $this->methodMetadata, $this->propertyMetadata, $this->fileResources, $this->createdAt, $this->model ) = unserialize ( $str );

		$this->reflection = new \ReflectionClass ( $this->name );
	}

	public function merge(MergeableInterface $object) {
		parent::merge ( $object );
		if($object->model) {
			$this->model = $object->model;
		}
	}
}
