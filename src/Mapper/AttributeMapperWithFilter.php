<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData;

class AttributeMapperWithFilter implements AttributeMapper
{
    public static function of(AttributeMapper $mapper, callable $filter): self
    {
        $filterableAttributeMapper = new self;
        $filterableAttributeMapper->mapper = $mapper;
        $filterableAttributeMapper->filter = $filter;
        return $filterableAttributeMapper;
    }

    public function map(AttributeData $akeneoAttributeData): array
    {
        $filterResult = ($this->filter)($akeneoAttributeData);
        if (!$filterResult) {
            return [];
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
