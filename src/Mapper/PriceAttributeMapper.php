<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class PriceAttributeMapper implements AttributeMapper
{
    public static function of(array $currencies): self
    {
        $mapper = new self;
        $mapper->currencies = $currencies;
        return $mapper;
    }

    /**
     * @return FredhopperAttributeData[]
     * @throws \Exception
     */
    public function map(AkeneoAttributeData $akeneoAttributeData): array
    {
        if ($akeneoAttributeData->getType() !== AkeneoAttributeType::PRICE_COLLECTION) {
            throw new \Exception;
        }

        $attributes = [];
        foreach ($this->currencies as $currency) {
            $attributeId = "{$akeneoAttributeData->getCode()}_" . \strtolower($currency);
            $attributeNames = ($this->nameMapper)($akeneoAttributeData->getLabels());
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

    private function __construct()
    {
        $this->nameMapper = function (AkeneoInternationalizedString $labels) {
            $result = FredhopperInternationalizedString::create();
            /** @var LocalizedString $label */
            foreach ($labels as $label) {
                $result = $result->withValue($label->getValue(), $label->getLocale());
            }
            return $result;
        };
    }
}
