<?php
declare(strict_types=1);
namespace SnowIO\AkeneoFredhopper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;

class StandardAttributeMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        $attributeId = ($this->attributeIdMapper)($akeneoAttributeData->getCode());
        $labels = ($this->nameMapper)($akeneoAttributeData->getLabels());
        if ($akeneoAttributeData->isLocalizable()) {
            return AttributeDataSet::of([FredhopperAttributeData::of($attributeId, FredhopperAttributeType::ASSET, $labels)]);
        }
        $fredhopperType = ($this->typeMapper)($akeneoAttributeData->getType());
        return AttributeDataSet::of([FredhopperAttributeData::of($attributeId, $fredhopperType, $labels)]);
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withTypeMapper(callable $fn): self
    {
        $result = clone $this;
        $result->typeMapper = $fn;
        return $result;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    public static function getDefaultTypeMapper(): callable
    {
        $typeMap = [
            AkeneoAttributeType::SIMPLESELECT => FredhopperAttributeType::LIST,
            AkeneoAttributeType::BOOLEAN => FredhopperAttributeType::INT,
            AkeneoAttributeType::NUMBER => FredhopperAttributeType::INT,
            AkeneoAttributeType::PRICE_COLLECTION => FredhopperAttributeType::FLOAT,
            AkeneoAttributeType::MULTISELECT => FredhopperAttributeType::SET,
        ];
        return function (string $akeneoType) use ($typeMap) {
            return $typeMap[$akeneoType] ?? 'text';
        };
    }

    private $attributeIdMapper;
    private $typeMapper;
    private $nameMapper;

    private function __construct()
    {
        $this->attributeIdMapper = [FredhopperAttributeData::class, 'sanitizeId'];
        $this->typeMapper = self::getDefaultTypeMapper();
        $this->nameMapper = InternationalizedStringMapper::create();
    }
}
