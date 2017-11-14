<?php
namespace SnowIO\AkeneoFredhopper\Test\EventMapper;

use PHPUnit\Framework\TestCase;
use SnowIO\AkeneoFredhopper\AttributeOptionMapper;
use SnowIO\AkeneoFredhopper\CategoryMapper;
use SnowIO\AkeneoFredhopper\EventMapper\FredhopperConfiguration;
use SnowIO\AkeneoFredhopper\ProductToProductMapper;
use SnowIO\AkeneoFredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;

abstract class CommandEventMapperTest extends TestCase
{
    public function getFredhopperConfiguration() : FredhopperConfiguration
    {
        return new class extends FredhopperConfiguration
        {
            function customAttributeIsBlacklisted(string $attributeCode): bool
            {

            }

            public function getCategoryIdMapper(): callable
            {

            }

            function getInternationalizedStringMapper(): callable
            {

            }

            function getStaticAttributes(): AttributeDataSet
            {

            }

            function getPriceAttributes(): AttributeDataSet
            {

            }

            public function getCategoryMapper(): callable
            {
                return CategoryMapper::create();
            }

            public function getAttributeMapper(): callable
            {
                return StandardAttributeMapper::create();
            }

            public function getAttributeOptionMapper(): callable
            {
                return AttributeOptionMapper::create();
            }

            public function getProductMapper(): callable
            {
                return ProductToProductMapper::create();
            }
        };
    }

    public abstract function testSaveCommandMapper();
    public abstract function testDeleteCommandMapper();
}
