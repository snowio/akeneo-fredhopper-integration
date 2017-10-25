<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class LocalizableAttributeMapper implements AttributeMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->typeMapper = StandardAttributeMapper::getDefaultTypeMapper();
        $mapper->nameMapper = function (AkeneoInternationalizedString $labels) {
            $result = FredhopperInternationalizedString::create();
            /** @var LocalizedString $label */
            foreach ($labels as $label) {
                $result = $result->withValue($label->getValue(), $label->getLocale());
            }
            return $result;
        };
        return $mapper;
    }

    public static function of(array $locales): self
    {
        if (empty($locales)) {
            throw new \InvalidArgumentException;
        }

        $mapper = self::create();
        $mapper->locales = $locales;
        return $mapper;
    }

    /**
     * @return FredhopperAttributeData[]
     */
    public function map(AkeneoAttributeData $akeneoAttributeData): array
    {
        $type = ($this->typeMapper)($akeneoAttributeData->getType());
        $attributes = [];
        $locales = $this->locales ?? $akeneoAttributeData->getLabels()->getLocales();
        foreach ($locales as $locale) {
            $attributeId = "{$akeneoAttributeData->getCode()}_" . \strtolower($locale);
            $names = ($this->nameMapper)($akeneoAttributeData->getLabels());
            $attributes[] = FredhopperAttributeData::of($attributeId, $type, $names);
        }
        return $attributes;
    }

    public function withTypeMapper(callable $fn): self
    {
        $result = clone $this;
        $result->typeMapper = $fn;
        return $result;
    }

    private $locales;
    private $nameMapper;
    private $typeMapper;

    private function __construct()
    {

    }
}
