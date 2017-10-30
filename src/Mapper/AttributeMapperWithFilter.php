<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class AttributeMapperWithFilter implements AttributeMapper
{
    public static function of(AttributeMapper $mapper, callable $filter): self
    {
        $filterableAttributeMapper = new self;
        $filterableAttributeMapper->mapper = $mapper;
        $filterableAttributeMapper->filter = $filter;
        return $filterableAttributeMapper;
    }

    public function map(AttributeData $akeneoAttributeData): AttributeDataSet
    {
        $filterResult = ($this->filter)($akeneoAttributeData);
        if (!$filterResult) {
            return AttributeDataSet::create();
        }
        return $this->mapper->map($akeneoAttributeData);
    }

    /** @var  AttributeMapper */
    private $mapper;
    /** @var callable */
    private $filter;

    private function __construct()
    {

    }
}
