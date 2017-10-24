<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\SingleChannelProductData;
use SnowIO\FredhopperDataModel\Variant as FredhopperVariant;

class ProductToVariantMapper
{
    public static function create(): self
    {
        $variantMapper = new self;
        $variantMapper->skuToProductIdMapper = function (string $channel, string $sku) {
            return $sku;
        };
        $variantMapper->variantGroupCodeToProductIdMapper = function (string $channel, string $code) {
            return $code;
        };
        $variantMapper->variantIdMapper = function (string $channel, string $sku) {
            return "v_{$sku}";
        };
        $variantMapper->attributeValueMapper = SimpleAttributeValueMapper::create();
        return $variantMapper;
    }

    public function map(SingleChannelProductData $akeneoProductData): ?FredhopperVariant
    {
        $channel = $akeneoProductData->getChannel();
        $sku = $akeneoProductData->getSku();
        $variantId = ($this->variantIdMapper)($channel, $sku);
        $variantGroupCode = $akeneoProductData->getProperties()->getVariantGroup();
        if ($variantGroupCode === null) {
            $productId = ($this->skuToProductIdMapper)($channel, $sku);
        } else {
            $productId = ($this->variantGroupCodeToProductIdMapper)($channel, $variantGroupCode);
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

    public function withAttributeValueMapper($attributeValueMapper): self
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