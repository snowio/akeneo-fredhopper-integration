<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\PriceCollection;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;

class SimpleAttributeValueMapper
{
    public static function create()
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $akeneoAttributeValues = $akeneoAttributeValues->filter(function (AkeneoAttributeValue $attributeValue) {
            return !$attributeValue->getValue() instanceof PriceCollection;
        });

        /** @var FredhopperAttributeValueSet $attributeValues */
        $attributeValues = FredhopperAttributeValueSet::create();
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            $attributeValues = $attributeValues->with(FredhopperAttributeValue::of(
                $akeneoAttributeValue->getAttributeCode(),
                $akeneoAttributeValue->getValue()
            ));
        }
        return $attributeValues;
    }

    private function __construct()
    {

    }
}
