<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\PriceSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;

class SimpleAttributeValueMapper implements AttributeValueMapper
{
    public static function create(): self
    {
        return FilterableAttributeValueMapper::of(
            new self,
            function (AttributeValue $attributeValue) {
                return !$attributeValue->getValue() instanceof PriceSet;
            }
        );
    }

    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $attributeValues = [];
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            $attributeValues[] = FredhopperAttributeValue::of(
                $akeneoAttributeValue->getAttributeCode(),
                $akeneoAttributeValue->getValue()
            );
        }
        return FredhopperAttributeValueSet::of($attributeValues);
    }

    private function __construct()
    {

    }
}
