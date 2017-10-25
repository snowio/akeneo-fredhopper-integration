<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\AkeneoDataModel\Price;
use SnowIO\AkeneoDataModel\PriceCollection;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;

class PriceAttributeValueMapper implements AttributeValueMapper
{
    public static function create()
    {
        return AttributeValueMapperWithFilter::of(
            new self,
            function (AkeneoAttributeValue $akeneoAttributeValue) {
                return $akeneoAttributeValue->getValue() instanceof PriceCollection;
            }
        );
    }

    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $fredhopperPriceAttributeValues = [];
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            /** @var PriceCollection $prices */
            $prices = $akeneoAttributeValue->getValue();
            /** @var Price $price */
            foreach ($prices as $price) {
                $attributeId = "{$akeneoAttributeValue->getAttributeCode()}_{$price->getCurrency()}";
                $fredhopperPriceAttributeValues[] = FredhopperAttributeValue::of($attributeId, $price->getAmount());
            }
        }
        return FredhopperAttributeValueSet::of($fredhopperPriceAttributeValues);
    }
}
