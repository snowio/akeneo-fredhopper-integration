<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class LocalizableAttributeMapper implements AttributeMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->typeMapper = StandardAttributeMapper::getDefaultTypeMapper();
        $mapper->nameMapper = function (array $names) {
            $result = InternationalizedString::create();
            foreach ($names as $locale => $name) {
                $result = $result->withValue($name, $locale);
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
     * @param AkeneoAttribute $akeneoAttribute
     * @return FredhopperAttributeData[]
     */
    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $type = ($this->typeMapper)($akeneoAttribute->getType());
        $attributes = [];
        $locales = $this->locales ?? \array_keys($akeneoAttribute->getLabels());
        foreach ($locales as $locale) {
            $attributeId = "{$akeneoAttribute->getCode()}_" . \strtolower($locale);
            $names = ($this->nameMapper)($akeneoAttribute->getLabels());
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
