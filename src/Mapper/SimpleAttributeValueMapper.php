<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\PriceCollection;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;

class SimpleAttributeValueMapper implements AttributeValueMapper
{
    public static function create()
    {
        return AttributeValueMapperWithFilter::of(
            new self,
            function (AttributeValue $attributeValue) {
                return !$attributeValue->getValue() instanceof PriceCollection;
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
