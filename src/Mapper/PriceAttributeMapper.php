<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;

class PriceAttributeMapper implements AttributeMapper
{
    public static function of(array $currencies): self
    {
        $mapper = new self;
        $mapper->currencies = $currencies;
        return $mapper;
    }

    /**
     * @param AkeneoAttribute $akeneoAttribute
     * @return array|\SnowIO\FredhopperDataModel\Attribute[]
     * @throws \Error
     */
    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        if ($akeneoAttribute->getType() !== AkeneoAttributeType::PRICE_COLLECTION) {
            throw new \Error;
        }

        $attributes = [];
        foreach ($this->currencies as $currency) {
            $attributeId = "{$akeneoAttribute->getCode()}_" . \strtolower($currency);
            $attributes[] = FredhopperAttribute::of($attributeId, FredhopperAttributeType::FLOAT, $akeneoAttribute->getLabels());
        }
        return $attributes;
    }

    private $currencies;
}
