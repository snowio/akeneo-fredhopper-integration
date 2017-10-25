<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class AttributeValueMapperWithFilter implements AttributeValueMapper
{
    public static function of(AttributeValueMapper $mapper, callable $filter): self
    {
        $mapperWithFilter = new self;
        $mapperWithFilter->mapper = $mapper;
        $mapperWithFilter->filter = $filter;
        return $mapperWithFilter;
    }

    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $filteredAttributeValues = $akeneoAttributeValues->filter($this->filter);
        return $this->mapper->map($filteredAttributeValues);
    }

    /** @var AttributeValueMapper */
    private $mapper;
    /** @var callable */
    private $filter;

    protected function __construct()
    {

    }
}
