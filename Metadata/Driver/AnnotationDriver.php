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

namespace Togu\AnnotationBundle\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Doctrine\Common\Annotations\Reader;

use Togu\AnnotationBundle\Metadata\MergeableClassMetadata;
use Togu\AnnotationBundle\Metadata\PropertyMetadata;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new MergeableClassMetadata($class->getName());

        $classAnnotation = $this->reader->getClassAnnotation(
        	$class,
        	'Togu\\AnnotationBundle\\Annotation\\Model'
		);

        if(null !== $classAnnotation) {
        	$classMetadata->model = $classAnnotation->name;
        }

        foreach ($class->getProperties() as $reflectionProperty) {
        	$propertyMetadata = new PropertyMetadata($class->getName(), $reflectionProperty->getName());

            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Togu\\AnnotationBundle\\Annotation\\Type'
            );

            if (null !== $annotation) {
                // A "@Type" annotation was found
                $propertyMetadata->toguType = $annotation->type;
                $propertyMetadata->getter = $annotation->getter ?: 'get'. ucFirst($reflectionProperty->getName());
                $propertyMetadata->setter = $annotation->setter ?: 'set'. ucFirst($reflectionProperty->getName());
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}