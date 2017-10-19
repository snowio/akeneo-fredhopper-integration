<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\FredhopperDataModel\Variant as FredhopperVariant;

class ProductToVariantMapper
{
    public static function create(): self
    {
        $variantMapper = new self;
        $variantMapper->skuToProductIdMapper = function (SingleChannelProductData $akeneoProductData) {
            $channel = $akeneoProductData->getChannel();
            $sku = $akeneoProductData->getSku();
            return "{$channel}_v_{$sku}";
        };
        $variantMapper->variantGroupCodeToProductIdMapper = function (SingleChannelProductData $akeneoProductData) {
            $channel = $akeneoProductData->getChannel();
            $code = $akeneoProductData->getProperties()->getVariantGroup();
            return "{$channel}_{$code}";
        };
        $variantMapper->variantIdMapper = function (SingleChannelProductData $akeneoProductData) {
            $channel = $akeneoProductData->getChannel();
            $sku = $akeneoProductData->getSku();
            return "{$channel}_{$sku}";
        };
        $variantMapper->attributeValueMapper = SimpleAttributeValueMapper::create();
        return $variantMapper;
    }

    public function map(SingleChannelProductData $akeneoProductData): ?FredhopperVariant
    {
        $variantId = ($this->variantIdMapper)($akeneoProductData);
        $variantGroupCode = $akeneoProductData->getProperties()->getVariantGroup();
        if ($variantGroupCode === null) {
            $productId = ($this->skuToProductIdMapper)($akeneoProductData);
        } else {
            $productId = ($this->variantGroupCodeToProductIdMapper)($akeneoProductData);
        }
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperVariant::of($variantId, $productId)->withAttributeValues($fredhopperAttributeValues);
    }

    public function withSkuToVariantIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->variantIdMapper = $fn;
        return $result;
    }

    public function withVariantGroupCodeToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->skuToProductIdMapper = $fn;
        return $result;
    }

    public function withSkuToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->skuToProductIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper(SimpleAttributeValueMapper $attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $variantIdMapper;

    /** @var callable */
    private $skuToProductIdMapper;

    /** @var callable */
    private $variantGroupCodeToProductIdMapper;

    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {

    }
}