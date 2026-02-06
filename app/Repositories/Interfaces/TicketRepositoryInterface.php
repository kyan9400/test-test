<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TicketRepositoryInterface
{
    public function find(int $id): ?Ticket;

    public function findWithRelations(int $id, array $relations = []): ?Ticket;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Ticket;

    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket;

    public function assignToUser(Ticket $ticket, int $userId): Ticket;

    public function hasTicketCreatedToday(int $customerId): bool;

    public function countByStatus(TicketStatus $status): int;

    public function getStatistics(string $period = 'day'): array;
}
