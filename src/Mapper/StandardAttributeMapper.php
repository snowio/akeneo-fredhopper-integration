<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\Attribute as AkeneoAttribute;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\Attribute as FredhopperAttribute;

class StandardAttributeMapper implements AttributeMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->typeMapper = self::getDefaultTypeMapper();
        $mapper->nameMapper = function (array $labels) {
            return $labels;
        };
        return $mapper;
    }

    public function map(AkeneoAttribute $akeneoAttribute): array
    {
        $labels = ($this->nameMapper)($akeneoAttribute->getLabels());
        if ($akeneoAttribute->isLocalizable()) {
            return [FredhopperAttribute::of($akeneoAttribute->getCode(), 'asset', $labels)];
        }
        $fredhopperType = ($this->typeMapper)($akeneoAttribute->getType());
        return [FredhopperAttribute::of($akeneoAttribute->getCode(), $fredhopperType, $labels)];
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
            AkeneoAttributeType::IDENTIFIER => FredhopperAttributeType::TEXT,
            AkeneoAttributeType::SIMPLESELECT => FredhopperAttributeType::LIST,
            AkeneoAttributeType::BOOLEAN => FredhopperAttributeType::INT,
            AkeneoAttributeType::BOOLEAN => FredhopperAttributeType::INT,
            AkeneoAttributeType::PRICE_COLLECTION => FredhopperAttributeType::FLOAT,
            AkeneoAttributeType::MULTISELECT => FredhopperAttributeType::SET,
        ];
        return function (string $akeneoType) use ($typeMap) {
            return $typeMap[$akeneoType] ?? 'text';
        };
    }

    private $typeMapper;
    private $nameMapper;

    private function __construct()
    {

    }
}