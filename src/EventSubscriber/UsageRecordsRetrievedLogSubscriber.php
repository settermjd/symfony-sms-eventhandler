<?php

declare(strict_types=1);

namespace App\EventSubscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UsageRecordsRetrievedLogSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UsageRecordsRetrievedEvent::NAME => [
                ['usageRecordsRetrieved', 10]
            ],
        ];
    }

    public function usageRecordsRetrieved(UsageRecordsRetrievedEvent $event)
    {
        $this->logger
            ->debug(
                'Usage records retrieved',
                $event->getUsageRecords()
            );
    }
}