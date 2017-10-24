<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class LocalizedAttributeValueMapper implements AttributeValueMapper
{
    public static function create()
    {
        return FilterableAttributeValueMapper::of(
            new self,
            function (AkeneoAttributeValue $attributeValue) {
                return $attributeValue->getScope()->getLocale() !== null;
            }
        );
    }

    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $fredhopperLocalisedAttributeValues = [];
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            $locale = $akeneoAttributeValue->getScope()
                ->getLocale();
            $attributeId = "{$akeneoAttributeValue->getAttributeCode()}_" . \strtolower($locale);
            $value = $akeneoAttributeValue->getValue();
            $fredhopperLocalisedAttributeValues[] = FredhopperAttributeValue::of($attributeId, $value);
        }

        return FredhopperAttributeValueSet::of($fredhopperLocalisedAttributeValues);
    }

    private function __construct()
    {

    }
}