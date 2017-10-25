<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData;

interface AttributeMapper
{
    public function map(AttributeData $akeneoAttributeData): array;
}
