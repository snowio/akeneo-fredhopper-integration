<?php
namespace SnowIO\AkeneoFredhopper\EventMapper;

use SnowIO\AkeneoDataModel\Event\AttributeOptionDeletedEvent;
use SnowIO\AkeneoDataModel\Event\AttributeOptionSavedEvent;
use SnowIO\FredhopperDataModel\AttributeOptionSet;

class AttributeOptionEventCommandMapper
{

    public static function create(FredhopperConfiguration $configuration)
    {
        return new self($configuration);
    }

    public function getSaveCommands(array $eventJson): array {
        $event = AttributeOptionSavedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeOptionMapper();
        /** @var AttributeOptionSet $fhAttributeOptions */
        $fhAttributeOptions = $mapper($event->getCurrentAttributeOptionData());
        return $fhAttributeOptions->mapToSaveCommands($event->getTimestamp());
    }
    
    public function getDeleteCommands(array $eventJson): array {
        $event = AttributeOptionDeletedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeOptionMapper();
        /** @var AttributeOptionSet $fhCategoriesData */
        $fhCategoriesData = $mapper($event->getPreviousAttributeOptionData());
        return $fhCategoriesData->mapToDeleteCommands($event->getTimestamp());
    }

    private $fredhopperConfiguration;

    private function __construct(FredhopperConfiguration $configuration)
    {
        $this->fredhopperConfiguration = $configuration;
    }
}