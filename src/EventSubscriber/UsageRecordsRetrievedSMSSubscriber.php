<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use NumberFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twilio\Rest\Client;

class UsageRecordsRetrievedSMSSubscriber implements EventSubscriberInterface
{
    private Client $client;
    private string $smsSender;
    private string $smsRecipient;

    public function __construct(Client $client, string $smsSender, string $smsRecipient)
    {
        $this->client = $client;
        $this->smsSender = $smsSender;
        $this->smsRecipient = $smsRecipient;
    }

    public static function getSubscribedEvents()
    {
        return [
            UsageRecordsRetrievedEvent::NAME => [
                ['usageRecordsRetrieved', -10]
            ],
        ];
    }

    public function usageRecordsRetrieved(UsageRecordsRetrievedEvent $event)
    {
        $usageTotal = $this->getUsageTotal($event->getUsageRecords());
        $usageCount = count($event->getUsageRecords());

        $this->client
            ->messages
            ->create(
                $this->smsRecipient,
                [
                    'body' => sprintf('%d usage records retrieved. Usage cost: %s.', $usageCount, $usageTotal),
                    'from' => $this->smsSender
                ]
            );
    }

    /**
     * Retrieve the usage total, formatted as a currency
     *
     * @param array $usageRecords array<int,array<string>>
     */
    private function getUsageTotal(array $usageRecords = []): string
    {
        if (empty($usageRecords)) {
            return "Usage total not available.";
        }

        // Get the total usage amount
        $sum = array_sum(
            array_keys(
                array_column($usageRecords, null, 3)
            )
        );

        // Get the currency
        $currency = $usageRecords[0][4];

        // Format the usage total as a currency
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return  $fmt->formatCurrency($sum, strtoupper($currency));
    }
}