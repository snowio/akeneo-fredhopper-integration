<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;

class AttributeOptionMapper
{
    public static function create(): self
    {
        $attributeOptionMapper = new self;
        $attributeOptionMapper->attributeIdMapper = function (string $code) { return $code; };
        $attributeOptionMapper->displayValueMapper = function (array $displayValues) { return $displayValues; };
        return $attributeOptionMapper;
    }

    public function map(AkeneoAttributeOption $attributeOption): FredhopperAttributeOption
    {
        $attributeId = ($this->attributeIdMapper)($attributeOption->getAttributeCode());
        $valueId = $attributeOption->getOptionCode();
        $labels = $attributeOption->getLabels();
        return FredhopperAttributeOption::of($attributeId, $valueId)->withDisplayValues($labels);
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withDisplayValueMapper(callable $fn): self
    {
        $result = clone $this;
        $result->displayValueMapper = $fn;
        return $result;
    }

    private $attributeIdMapper;
    private $displayValueMapper;

    private function __construct()
    {
    }
}