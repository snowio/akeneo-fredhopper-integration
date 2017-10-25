<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;

class CompositeAttributeMapper implements AttributeMapper
{
    public static function create()
    {
        return new self;
    }

    public function map(AkeneoAttributeData $akeneoAttributeData): array
    {
        $fredhopperAttributes = [];
        foreach ($this->mappers as $mapper) {
            $fredhopperAttributes = \array_merge($fredhopperAttributes, $mapper->map($akeneoAttributeData));
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
