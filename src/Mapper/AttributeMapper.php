<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

interface AttributeMapper
{
    public function map(AttributeData $akeneoAttributeData): AttributeDataSet;
}
