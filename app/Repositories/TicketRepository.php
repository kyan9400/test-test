<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TicketRepository implements TicketRepositoryInterface
{
    public function __construct(
        private readonly Ticket $model
    ) {}

    public function find(int $id): ?Ticket
    {
        return $this->model->find($id);
    }

    public function findWithRelations(int $id, array $relations = []): ?Ticket
    {
        return $this->model->with($relations)->find($id);
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['customer', 'assignedUser']);

        if (! empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $query->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);
        }

        if (! empty($filters['email'])) {
            $query->customerEmail($filters['email']);
        }

        if (! empty($filters['phone'])) {
            $query->customerPhone($filters['phone']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): Ticket
    {
        return $this->model->create($data);
    }

    public function updateStatus(Ticket $ticket, TicketStatus $status): Ticket
    {
        $updateData = ['status' => $status];

        if ($status === TicketStatus::DONE) {
            $updateData['answered_at'] = now();
        }

        $ticket->update($updateData);

        return $ticket->fresh();
    }

    public function assignToUser(Ticket $ticket, int $userId): Ticket
    {
        $ticket->update(['assigned_user_id' => $userId]);

        return $ticket->fresh();
    }

    public function hasTicketCreatedToday(int $customerId): bool
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->createdToday()
            ->exists();
    }

    public function countByStatus(TicketStatus $status): int
    {
        return $this->model->status($status)->count();
    }

    public function getStatistics(string $period = 'day'): array
    {
        $query = $this->model->query();

        $query = match ($period) {
            'week' => $query->createdThisWeek(),
            'month' => $query->createdThisMonth(),
            default => $query->createdToday(),
        };

        $total = $query->count();
        $byStatus = [];

        foreach (TicketStatus::cases() as $status) {
            $byStatus[$status->value] = (clone $query)->status($status)->count();
        }

        return [
            'period' => $period,
            'total' => $total,
            'by_status' => $byStatus,
        ];
    }
}

