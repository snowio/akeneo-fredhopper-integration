<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;

class LocalizableAttributeMapper implements AttributeMapper
{
    public static function of(array $locales): self
    {
        $mapper = new self;
        $mapper->locales = $locales;
        $mapper->typeMapper = StandardAttributeMapper::getDefaultTypeMapper();
        return $mapper;
    }

    /**
     * @param AkeneoAttribute $akeneoAttribute
     * @return FredhopperAttribute[]
     */
    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $type = ($this->typeMapper)($akeneoAttribute->getType());
        $attributes = [];
        foreach ($this->locales as $locale) {
            $attributeId = "{$akeneoAttribute->getCode()}_" . \strtolower($locale);
            $attributes[] = FredhopperAttribute::of($attributeId, $type, $akeneoAttribute->getLabels());
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
    private $typeMapper;

    private function __construct()
    {

    }
}