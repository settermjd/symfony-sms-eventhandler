<?php

namespace App\EventSubscriber;

use Symfony\Contracts\EventDispatcher\Event;

class UsageRecordsRetrievedEvent extends Event
{
    public const NAME = 'usage.records.retrieved';

    /**
     * This is an array of select usage record details
     *
     * @var array<int,array<string>>
     */
    private array $usageRecords;

    /**
     * @param array $usageRecords array<int,array<string>>
     */
    public function __construct(array $usageRecords = [])
    {
        $this->usageRecords = $usageRecords;
    }

    /**
     * @return array<int,array<string>>
     */
    public function getUsageRecords(): array
    {
        return $this->usageRecords;
    }

}