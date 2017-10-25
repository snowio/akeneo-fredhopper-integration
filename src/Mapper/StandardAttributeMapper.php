<?php
namespace SnowIO\AkeneoFredhopper\Mapper;

use SnowIO\AkeneoDataModel\AttributeData as AkeneoAttributeData;
use SnowIO\AkeneoDataModel\AttributeType as AkeneoAttributeType;
use SnowIO\AkeneoDataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\AkeneoDataModel\LocalizedString;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class StandardAttributeMapper implements AttributeMapper
{
    public static function create(): self
    {
        $mapper = new self;
        $mapper->typeMapper = self::getDefaultTypeMapper();
        $mapper->attributeIdMapper = function (string $code) {
            return $code;
        };
        $mapper->nameMapper = function (AkeneoInternationalizedString $labels) {
            $result = FredhopperInternationalizedString::create();
            /** @var LocalizedString $label */
            foreach ($labels as $label) {
                $result = $result->withValue($label->getValue(), $label->getLocale());
            }
            return $result;
        };
        return $mapper;
    }

    public function map(AkeneoAttributeData $akeneoAttributeData): array
    {
        $attributeId = ($this->attributeIdMapper)($akeneoAttributeData->getCode());
        $labels = ($this->nameMapper)($akeneoAttributeData->getLabels());
        if ($akeneoAttributeData->isLocalizable()) {
            return [FredhopperAttributeData::of($attributeId, FredhopperAttributeType::ASSET, $labels)];
        }
        $fredhopperType = ($this->typeMapper)($akeneoAttributeData->getType());
        return [FredhopperAttributeData::of($attributeId, $fredhopperType, $labels)];
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

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
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

    private $typeMapper;
    private $nameMapper;
    private $attributeIdMapper;

    private function __construct()
    {

    }
}
