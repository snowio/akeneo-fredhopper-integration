<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;

class CompositeAttributeMapper implements AttributeMapper
{
    public static function create()
    {
        return new self;
    }

    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $fredhopperAttributes = [];

        foreach ($this->mappers as $mapper) {
            $fredhopperAttributes = array_merge($fredhopperAttributes, $mapper->map($akeneoAttribute));
        }

        return $fredhopperAttributes;
    }

    public function with(AttributeMapper $attributeMapper): self
    {
        $result = clone $this;
        $result->mappers[] = $attributeMapper;
        return $result;
    }

    /** @var AttributeMapper[] */
    private $mappers = [];

    private function __construct()
    {

    }
}
