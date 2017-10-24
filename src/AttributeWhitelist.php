<?php
namespace SnowIO\AkeneoFredhopper\Recipe;

use SnowIO\AkeneoDataModel\Attribute;
use SnowIO\AkeneoDataModel\AttributeValue;

final class AttributeWhitelist
{
    public static function of(array $attributeCodes): self
    {
        foreach ($attributeCodes as $attributeCode) {
            if (!\is_string($attributeCode)) {
                throw new \InvalidArgumentException;
            }
        }

        $whitelist = new self;
        $whitelist->attributeCodes = \array_flip($attributeCodes);
        return $whitelist;
    }

    public function getAttributeFilter(): callable
    {
        return function (Attribute $akeneoAttribute): bool {
            $attributeCode = $akeneoAttribute->getCode();
            return isset($this->attributeCodes[$attributeCode]);
        };
    }

    public function getAttributeValueFilter(): callable
    {
        return function (AttributeValue $akeneoAttributValue): bool {
            $attributeCode = $akeneoAttributValue->getAttributeCode();
            return isset($this->attributeCodes[$attributeCode]);
        };
    }

    private function __construct()
    {

    }

    private $attributeCodes;
}
