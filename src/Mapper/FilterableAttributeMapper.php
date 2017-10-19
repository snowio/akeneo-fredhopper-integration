<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;

class FilterableAttributeMapper implements AttributeMapper
{

    public static function of(AttributeMapper $mapper, callable $filter): self
    {
        $filterableAttributeMapper = new self;
        $filterableAttributeMapper->mapper = $mapper;
        $filterableAttributeMapper->filter = $filter;
        return $filterableAttributeMapper;
    }

    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $filterResult = ($this->filter)($akeneoAttribute);
        if (!$filterResult) {
            return [];
        }
        return $this->mapper->map($akeneoAttribute);
    }

    /** @var  AttributeMapper */
    private $mapper;

    /** @var callable */
    private $filter;

    private function __construct()
    {

    }
}