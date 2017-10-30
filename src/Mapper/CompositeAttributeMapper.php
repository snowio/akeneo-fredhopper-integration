<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class CompositeAttributeMapper implements AttributeMapper
{
    public static function create()
    {
        return new self;
    }

    public function map(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        /** @var AttributeDataSet $fredhopperAttributes */
        $fredhopperAttributes = AttributeDataSet::create();
        foreach ($this->mappers as $mapper) {
            $fredhopperAttributes = $fredhopperAttributes->add($mapper->map($akeneoAttributeData));
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
