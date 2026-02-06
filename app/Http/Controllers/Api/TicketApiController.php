<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateTicketRequest;
use App\Http\Resources\StatisticsResource;
use App\Http\Resources\TicketResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {}

    public function store(CreateTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket(
            $request->validated(),
            $request->file('files', [])
        );

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    public function statistics(Request $request): StatisticsResource
    {
        $period = $request->query('period', 'day');

        if (! in_array($period, ['day', 'week', 'month'])) {
            $period = 'day';
        }

        $statistics = $this->ticketService->getStatistics($period);

        return new StatisticsResource($statistics);
    }
}
