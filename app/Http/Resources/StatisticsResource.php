<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'period' => $this->resource['period'],
            'total' => $this->resource['total'],
            'by_status' => collect($this->resource['by_status'])->map(fn ($count, $status) => [
                'status' => $status,
                'label' => TicketStatus::from($status)->label(),
                'count' => $count,
            ])->values(),
        ];
    }
}
