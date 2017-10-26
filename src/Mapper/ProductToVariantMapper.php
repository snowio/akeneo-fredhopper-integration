<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\VariantData as FredhopperVariantData;

class ProductToVariantMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function map(AkeneoProductData $akeneoProductData): ?FredhopperVariantData
    {
        $channel = $akeneoProductData->getChannel();
        $sku = $akeneoProductData->getSku();
        $variantId = ($this->variantIdMapper)($sku, $channel);
        $variantGroupCode = $akeneoProductData->getProperties()->getVariantGroup();
        if ($variantGroupCode === null) {
            $productId = ($this->skuToProductIdMapper)($sku, $channel);
        } else {
            $productId = ($this->variantGroupCodeToProductIdMapper)($variantGroupCode, $channel);
        }
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = $this->attributeValueMapper->map($akeneoAttributeValues);
        return FredhopperVariantData::of($variantId, $productId)->withAttributeValues($fredhopperAttributeValues);
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
        $result->variantGroupCodeToProductIdMapper = $fn;
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
        $this->skuToProductIdMapper = function (string $sku, string $channel) {
            return $sku;
        };
        $this->variantGroupCodeToProductIdMapper = function (string $code, string $channel) {
            return $code;
        };
        $this->variantIdMapper = function (string $sku, string $channel) {
            return "v_{$sku}";
        };
        $this->attributeValueMapper = SimpleAttributeValueMapper::create();
    }
}
