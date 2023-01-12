<?php

declare(strict_types=1);

namespace App\Controller;

use App\EventSubscriber\UsageRecordsRetrievedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class UsageDataController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;
    private Client $client;

    public function __construct(Client $client, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/usage-records', name: 'app_usage-records')]
    public function index(): JsonResponse
    {
        $lastMonth = $this->client
            ->usage
            ->records
            ->lastMonth
            ->read([], 20);

        $usageRecords = [];
        foreach ($lastMonth as $record) {
            $usageRecords[] = [
                $record->asOf,
                $record->category,
                $record->count,
                $record->price,
                $record->priceUnit,
            ];
        }

        $this->eventDispatcher->dispatch(
            new UsageRecordsRetrievedEvent($usageRecords),
            UsageRecordsRetrievedEvent::NAME
        );

        return $this->json($usageRecords);
    }
}
