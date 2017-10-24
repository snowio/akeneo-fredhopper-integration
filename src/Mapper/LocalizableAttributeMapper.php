<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;

class LocalizableAttributeMapper implements AttributeMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->typeMapper = StandardAttributeMapper::getDefaultTypeMapper();
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
     * @param AkeneoAttribute $akeneoAttribute
     * @return FredhopperAttribute[]
     */
    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $type = ($this->typeMapper)($akeneoAttribute->getType());
        $attributes = [];
        $locales = $this->locales ?? \array_keys($akeneoAttribute->getLabels());
        foreach ($locales as $locale) {
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