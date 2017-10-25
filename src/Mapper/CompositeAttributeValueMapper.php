<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class CompositeAttributeValueMapper implements AttributeValueMapper
{
    public static function create()
    {
        return new self;
    }

    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $result = FredhopperAttributeValueSet::create();
        foreach ($this->mappers as $mapper) {
            $fredhopperAttributeValues = $mapper->map($akeneoAttributeValues);
            $result = $result->add($fredhopperAttributeValues);
        }
        return $result;
    }

    public function with(AttributeValueMapper $attributeValueMapper): self
    {
        $result = clone $this;
        $result->mappers[] = $attributeValueMapper;
        return $result;
    }

    /** @var AttributeValueMapper[] */
    private $mappers = [];

    private function __construct()
    {

    }
}
