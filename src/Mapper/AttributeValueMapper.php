<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

interface AttributeValueMapper
{
    public function map(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet;
}
