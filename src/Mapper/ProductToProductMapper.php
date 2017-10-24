<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\FredhopperDataModel\Product as FredhopperProduct;

class ProductToProductMapper
{
    public static function create(): self
    {
        $standaloneProductMapper = new self;
        $standaloneProductMapper->categoryIdMapper = function ($categoryCode) { return $categoryCode; };
        $standaloneProductMapper->productIdMapper = function (string $channel, string $sku) {
            return $sku;
        };
        $standaloneProductMapper->attributeValueMapper = SimpleAttributeValueMapper::create();
        return $standaloneProductMapper;
    }

    public function map(SingleChannelProductData $akeneoProductData): FredhopperProduct
    {
        $channel = $akeneoProductData->getChannel();
        $sku = $akeneoProductData->getSku();
        $productId = ($this->productIdMapper)($channel, $sku);
        $categoryCodes = $akeneoProductData->getProperties()->getCategories()
            ->getCategoryCodes();
        $categoryIds = \array_map(function (string $categoryCode) {
            return ($this->categoryIdMapper)($categoryCode);
        }, $categoryCodes);
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperProduct::of($productId, $categoryIds)
            ->withAttributeValues($fredhopperAttributeValues);
    }

    public function withCategoryIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->categoryIdMapper = $fn;
        return $result;
    }

    public function withProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->productIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper($attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $categoryIdMapper;

    /** @var callable */
    private $productIdMapper;

    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {

    }
}
