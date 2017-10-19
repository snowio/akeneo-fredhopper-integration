<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;

interface AttributeMapper
{
    public function map(AkeneoAttribute $akeneoAttribute): array;
}