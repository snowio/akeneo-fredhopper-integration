<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString;

class PriceAttributeMapper implements AttributeMapper
{
    public static function of(array $currencies): self
    {
        $mapper = new self;
        $mapper->currencies = $currencies;
        $mapper->nameMapper = function (array $names) {
            $result = InternationalizedString::create();
            foreach ($names as $locale => $name) {
                $result = $result->withValue($name, $locale);
            }
            return $result;
        };
        return $mapper;
    }

    /**
     * @param AkeneoAttribute $akeneoAttribute
     * @return FredhopperAttributeData[]
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
            $attributeNames = ($this->nameMapper)($akeneoAttribute->getLabels());
            $attributes[] = FredhopperAttributeData::of($attributeId, FredhopperAttributeType::FLOAT, $attributeNames);
        }
        return $attributes;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    private $currencies;
    private $nameMapper;
}
