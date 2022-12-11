<?php

declare(strict_types=1);

namespace Nsbx\Bundle\ModelConstructorBundle;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use ArrayObject;

abstract class AbstractModel implements ModelInterface
{
    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(object $modelData)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;

        $this->buildModel($modelData);
    }

    protected function buildModel(object $modelData)
    {
        foreach ($this->getMapping() as $propertyKey => $property) {
            if (!is_array($property)) {
                $this->{$propertyKey} = $this->propertyAccessor->getValue($modelData, $property);
            } else {
                if (!($property['class'] ?? false) && ($property['cast'] ?? false)) {
                    $propertyValue = $this->propertyAccessor->getValue($modelData, $property['path']);

                    $this->{$propertyKey} = $this->castValue($propertyValue, $property['cast']);
                    continue;
                }

                $modelClass = $property['class'];

                $propertyData = $this->propertyAccessor->getValue($modelData, $property['path']);

                if ($propertyData === null) {
                    continue;
                }

                if ($property['isCollection'] ?? false) {
                    $this->{$propertyKey} = new ArrayObject();

                    foreach ($propertyData as $itemData) {
                        $itemModel = new $modelClass($itemData);
                        $this->{$propertyKey}->append($itemModel);
                    }
                } else {
                    if (($property['isNullable'] ?? false) && empty($propertyData)) {
                        continue;
                    }

                    $this->{$propertyKey} = new $modelClass($propertyData);
                }
            }
        }
    }

    private function castValue(mixed $value, string $castValue)
    {
        switch ($castValue) {
            case 'int':
                return (int)$value;
            case 'string':
                return (string)$value;
            case 'bool':
                return (bool)$value;
            default:
                return $value;
        }
    }
}
