<?php
namespace foo;

use SnowIO\AkeneoDataModel\AttributeValue;
use SnowIO\AkeneoDataModel\CategoryReference;
use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\AkeneoFredhopper\Mapper\FilterableAttributeValueMapper;
use SnowIO\AkeneoFredhopper\Mapper\ProductToProductMapper;
use SnowIO\AkeneoFredhopper\Mapper\SimpleAttributeValueMapper;

$productAttributes = ['foo', 'bar', 'baz'];
$variantAttributes = ['x', 'y', 'z'];

$productAttributeValueMapper = FilterableAttributeValueMapper::of(
    SimpleAttributeValueMapper::create(),
    function (AttributeValue $akeneoAttributeValue) use ($productAttributes) {
        return \in_array($akeneoAttributeValue->getAttributeCode(), $productAttributes, $strict = true);
    }
);

$productToProductMapper = ProductToProductMapper::create()
    ->withAttributeValueMapper($productAttributeValueMapper)
    ->withCategoryIdMapper(function (CategoryReference $categoryReference) {
        return $categoryReference->
    });

function ($product, $channel) use ($productToProductMapper) {
    /** @var SingleChannelProductData $akeneoData */
    $akeneoData = $product->getAkeneoData($channel);

}
